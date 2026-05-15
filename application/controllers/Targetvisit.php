<?php

class Targetvisit extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MCity');
        $this->load->model('MUser');
        $this->load->model('MContact');
        $this->load->model('MInvoice');
        $this->load->model('MPayment');
    }

    public function index()
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Target Visit';
        $data['menuGroup'] = 'Sales';
        $data['menu'] = 'TargetVisit';
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else if ($this->session->userdata('level_user') == 'salesspv') {
            $userCity = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->row_array();
            $nama_city = trim(preg_replace("/\\d+/", "", $userCity['nama_city']));
            $data['city'] = $this->db->like('nama_city', $nama_city)->get_where('tb_city', ['id_distributor' => $this->session->userdata('id_distributor')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Targetvisit/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function list($id_city)
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Target Visit';
        $data['menuGroup'] = 'Sales';
        $data['menu'] = 'TargetVisit';

        $data['city'] = $this->MCity->getById($id_city);
        $data['users'] = $this->db->get_where('tb_user', ['id_city' => $id_city, 'phone_user !=' => 0, 'level_user' => 'sales'])->result_array();

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Targetvisit/List');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function print()
    {
        $get = $this->input->get();

        $id_user = $get['id_user'];
        $daterange = explode('-', $get['daterange']);

        $dateFrom = date('Y-m-d', strtotime($daterange[0]));
        $dateTo = date('Y-m-d', strtotime($daterange[1]));

        $data['dateFrom'] = $dateFrom;
        $data['dateTo'] = $dateTo;
        $data['user'] = $this->MUser->getById($id_user);

        // Get Visit Group By
        $this->db->select('tb_visit.id_contact, tb_visit.id_user, tb_contact.nama, COUNT(tb_visit.id_visit) AS jmlVisit');
        $this->db->not_like('source_visit', 'absen');
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_visit.id_contact');
        $this->db->group_by('tb_visit.id_contact');
        $data['groupedVisits'] = $this->db->get_where('tb_visit', ['tb_visit.id_user' => $id_user, 'DATE(date_visit) >=' => $dateFrom, 'DATE(date_visit) <=' => $dateTo])->result_array();

        // Scoring
        $data['controller'] = $this;

        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Targetvisit/Print', $data, true);

        $mpdf->AddPage('L');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    public function paymentScoring($selected_contact)
    {
        // Payment Scoring
        $count_late_payment = 0;
        $invoices = $this->MInvoice->getByIdContactNoMerch($selected_contact['id_contact']);
        $payments = null;
        $array_scoring = array();
        foreach ($invoices as $invoice) {
            $id_surat_jalan = $invoice['id_surat_jalan'];
            $payments = $this->MPayment->getLastByIdInvoiceOnly($invoice['id_invoice']);

            $sj = $this->db->get_where('tb_surat_jalan', ['id_surat_jalan' => $id_surat_jalan])->row_array();

            if ($sj['is_cod'] == 0) {
                $jatuhTempo = date('Y-m-d', strtotime("+" . $selected_contact['termin_payment'] . " days", strtotime($invoice['date_invoice'])));
            } else {
                $jatuhTempo = date('Y-m-d', strtotime($invoice['date_invoice']));
            }

            if ($payments) {

                foreach ($payments as $payment) {
                    $datePayment = date("Y-m-d", strtotime($payment['date_payment']));
                    if ($datePayment > $jatuhTempo) {
                        $count_late_payment += 1;
                        $date1 = new DateTime($datePayment);
                        if ($invoice['status_invoice'] == 'waiting') {
                            $date1 = new DateTime(date('Y-m-d'));
                        }
                        $date2 = new DateTime($jatuhTempo);
                        $days  = $date2->diff($date1)->format('%a');


                        $scoreData = [
                            'id_invoice' => $invoice['id_invoice'],
                            'no_invoice' => $invoice['no_invoice'],
                            'status' => 'late',
                            'days_late' => $days,
                            'date_jatem' => $jatuhTempo,
                            'date_payment' => $datePayment,
                            'percent_score' => 100 - $days,
                            'is_cod' => $sj['is_cod'],
                            'date_invoice' => $invoice['date_invoice'],
                        ];

                        array_push($array_scoring, $scoreData);
                    } else {
                        if ($invoice['status_invoice'] == 'paid') {
                            $scoreData = [
                                'id_invoice' => $invoice['id_invoice'],
                                'no_invoice' => $invoice['no_invoice'],
                                'status' => 'good',
                                'days_late' => 0,
                                'date_jatem' => $jatuhTempo,
                                'date_payment' => $datePayment,
                                'percent_score' => 100,
                                'is_cod' => $sj['is_cod'],
                                'date_invoice' => $invoice['date_invoice'],
                            ];

                            array_push($array_scoring, $scoreData);
                        } else {
                            $dateNow = date("Y-m-d");
                            $count_late_payment += 1;
                            $date1 = new DateTime($dateNow);
                            $date2 = new DateTime($jatuhTempo);
                            $days  = $date2->diff($date1)->format('%a');

                            $scoreData = [
                                'id_invoice' => $invoice['id_invoice'],
                                'no_invoice' => $invoice['no_invoice'],
                                'status' => 'late',
                                'days_late' => $days,
                                'date_jatem' => $jatuhTempo,
                                'date_payment' => $datePayment,
                                'percent_score' => 100 - $days,
                                'is_cod' => $sj['is_cod'],
                                'date_invoice' => $invoice['date_invoice'],
                            ];

                            array_push($array_scoring, $scoreData);
                        }
                    }
                }
            } else {
                $dateNow = date("Y-m-d");
                if ($dateNow > $jatuhTempo) {
                    $count_late_payment += 1;
                    $date1 = new DateTime($dateNow);
                    $date2 = new DateTime($jatuhTempo);
                    $days  = $date2->diff($date1)->format('%a');

                    $scoreData = [
                        'id_invoice' => $invoice['id_invoice'],
                        'no_invoice' => $invoice['no_invoice'],
                        'status' => 'late',
                        'days_late' => $days,
                        'date_jatem' => $jatuhTempo,
                        'date_payment' => $dateNow,
                        'percent_score' => 100 - $days,
                        'is_cod' => $sj['is_cod'],
                        'date_invoice' => $invoice['date_invoice'],
                    ];

                    array_push($array_scoring, $scoreData);
                } else {
                    $scoreData = [
                        'id_invoice' => $invoice['id_invoice'],
                        'no_invoice' => $invoice['no_invoice'],
                        'status' => 'good',
                        'days_late' => 0,
                        'date_jatem' => $jatuhTempo,
                        'date_payment' => $dateNow,
                        'percent_score' => 100,
                        'is_cod' => $sj['is_cod'],
                        'date_invoice' => $invoice['date_invoice'],
                    ];

                    array_push($array_scoring, $scoreData);
                }
            }
        }

        $count_invoice = count($array_scoring);
        if ($count_invoice == 0) {
            $count_invoice = 1;
        }
        $total_score = 0;
        foreach ($array_scoring as $scoring) {
            $total_score += $scoring['percent_score'];
        }

        $val_scoring = number_format($total_score / $count_invoice, 2, '.', '.');

        if ($val_scoring > 100) {
            $val_scoring = 100;
        } else if ($val_scoring <= 100 && $val_scoring > 0) {
            $val_scoring = $val_scoring;
        } else if ($val_scoring < 0) {
            $val_scoring = 0;
        }

        if ($selected_contact['store_status'] == 'data') {
            $val_scoring = 100;
        }

        return number_format($val_scoring, 2, '.', ',');
    }
}
