<?php

class Scoring extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('MCity');
        $this->load->model('MContact');
        $this->load->model('MInvoice');
        $this->load->model('MPayment');
        $this->load->model('MSuratJalan');
        $this->load->model('MDetailSuratJalan');
    }

    public function city_list()
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Scoring Toko';
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Scroring/CityList');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function list($id_city)
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $post = $this->input->post();

        $city = $this->MCity->getById($id_city);

        if (!$post) {
            $data['title'] = 'Scoring Toko - Kota ' . $city['nama_city'];
            $data['city'] = $city;
            $data['contacts'] = $this->db->get_where('tb_contact', ['id_city' => $id_city])->result_array();
            $data['selected_contact'] = ['id_contact' => 0];
            $data['is_score'] = 0;
        } else {
            $id_contact = $post['id_contact'];
            $selected_contact = $this->db->get_where('tb_contact', ['id_contact' => $id_contact])->row_array();

            $data['title'] = 'Scoring Toko ' . $selected_contact['nama'];
            $data['city'] = $city;
            $data['contacts'] = $this->db->get_where('tb_contact', ['id_city' => $id_city])->result_array();
            $data['selected_contact'] = $selected_contact;
            $data['is_score'] = 1;
        }

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Scroring/List');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function rekap($id_city)
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $city = $this->MCity->getById($id_city);

        $data['title'] = 'Scoring Toko - Kota ' . $city['nama_city'];
        $data['city'] = $city;
        $data['contacts'] = $this->db->get_where('tb_contact', ['id_city' => $id_city])->result_array();

        // $this->load->view('Scoring/Rekap', $data);

        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Scoring/Rekap', $data, true);
        $mpdf->AddPage('L');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    public function combineScoring()
    {
        $this->output->set_content_type('application/json');
        $post = $this->input->post();

        $id_contact = $post['id_contact'];

        $selected_contact = $this->db->get_where('tb_contact', ['id_contact' => $id_contact])->row_array();

        // Payment
        $paymentScore = $this->paymentScoring($selected_contact);
        // Frequency Scoring
        $frequencyScore = $this->frequencyScoring($selected_contact);
        // Order Scoring
        $orderScoring = $this->orderScoring($selected_contact);

        $scoreData = [
            'payment' => $paymentScore,
            'frequency' => $frequencyScore,
            'order' => $orderScoring,
        ];

        return $this->output->set_output(json_encode($scoreData));
    }

    public function orderScoring($selected_contact)
    {
        $id_contact = $selected_contact['id_contact'];

        $orders = $this->MDetailSuratJalan->getClosingItemByIdContact($id_contact);

        $count_orders = $orders['qty_produk'];

        return $count_orders;
    }

    public function frequencyScoring($selected_contact)
    {
        $id_contact = $selected_contact['id_contact'];

        $oldest_invoice = $this->MInvoice->getOldestInvoiceByIdContact($id_contact);
        $date_oldest_inv = date("Y-m-d", strtotime($oldest_invoice['date_invoice']));
        $date_now = date("Y-m-d");

        // Total Month Elapsed
        $periods = new DatePeriod(
            new DateTime($date_oldest_inv),
            new DateInterval('P1M'),
            new DateTime($date_now)
        );

        $array_months = array();
        // Scoring System
        $score = 100;
        foreach ($periods as $period) {
            $month = $period->format('Y-m');
            // Get Invoice On period
            $monthInv = $this->MInvoice->getByIdContactAndMonth($id_contact, $month);
            if ($monthInv) {
                if ($score < 100) {
                    $score += 10;
                }
            } else {
                $score -= 10;
            }
        }

        return number_format($score, 2, '.', ',');
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

            foreach ($payments as $payment) {
                $datePayment = date("Y-m-d", strtotime($payment['date_payment']));
                if ($datePayment > $jatuhTempo) {
                    $count_late_payment += 1;
                    $date1 = new DateTime($datePayment);
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

        return $val_scoring;
    }
}
