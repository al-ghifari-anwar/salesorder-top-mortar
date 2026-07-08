<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MSuratJalan extends CI_Model
{

    public $no_surat_jalan;
    public $id_contact;
    public $dalivery_date;
    public $order_number;
    public $ship_to_name;
    public $ship_to_address;
    public $ship_to_phone;
    public $id_courier;
    public $id_kendaraan;
    public $is_cod;
    public $is_tebus_murah;
    public $payment_scoring;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('MInvoice');
        $this->load->model('MPayment');
    }

    public function getAll()
    {
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_surat_jalan.id_contact');
        $this->db->join('tb_user', 'tb_user.id_user = tb_surat_jalan.id_courier');
        $this->db->join('tb_city', 'tb_city.id_city = tb_contact.id_city');
        $this->db->order_by('id_surat_jalan', 'desc');
        $query = $this->db->get('tb_surat_jalan')->result_array();
        return $query;
    }

    public function getByCity($id_city)
    {
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_surat_jalan.id_contact');
        $this->db->join('tb_user', 'tb_user.id_user = tb_surat_jalan.id_courier');
        $this->db->join('tb_city', 'tb_city.id_city = tb_contact.id_city');
        $this->db->order_by('id_surat_jalan', 'desc');
        $this->db->where('tb_contact.id_city', $id_city);
        $query = $this->db->get('tb_surat_jalan')->result_array();
        return $query;
    }

    public function getByCityAndDate($id_city, $dateFrom, $dateTo)
    {
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_surat_jalan.id_contact');
        $this->db->join('tb_user', 'tb_user.id_user = tb_surat_jalan.id_courier');
        $this->db->join('tb_city', 'tb_city.id_city = tb_contact.id_city');
        $this->db->order_by('id_surat_jalan', 'desc');
        $this->db->where('tb_contact.id_city', $id_city);
        $this->db->where('DATE(tb_surat_jalan.dalivery_date) >=', $dateFrom);
        $this->db->where('DATE(tb_surat_jalan.dalivery_date) <=', $dateTo);
        $query = $this->db->get('tb_surat_jalan')->result_array();
        return $query;
    }

    public function getNotClosing()
    {
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_surat_jalan.id_contact');
        $this->db->join('tb_user', 'tb_user.id_user = tb_surat_jalan.id_courier');
        $this->db->join('tb_city', 'tb_city.id_city = tb_contact.id_city');
        if ($this->session->userdata('level_user') == 'admin_c') {
            $this->db->where('tb_city.id_city', $this->session->userdata('id_city'));
        }
        $this->db->order_by('id_surat_jalan', 'desc');
        $query = $this->db->get_where('tb_surat_jalan', ['is_closing' => 0, 'tb_city.id_distributor' => $this->session->userdata('id_distributor')])->result_array();
        return $query;
    }

    public function getById($id)
    {
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_surat_jalan.id_contact');
        $this->db->join('tb_user', 'tb_user.id_user = tb_surat_jalan.id_courier');
        $this->db->join('tb_kendaraan', 'tb_kendaraan.id_kendaraan = tb_surat_jalan.id_kendaraan');
        $this->db->join('tb_city', 'tb_city.id_city = tb_contact.id_city');
        $query = $this->db->get_where('tb_surat_jalan', ['id_surat_jalan' => $id])->row_array();
        return $query;
    }

    public function insert()
    {
        $post = $this->input->post();
        date_default_timezone_set('Asia/Jakarta');
        $id_contact = $post['id_contact'];
        $selected_contact = $this->db->get_where('tb_contact', ['id_contact' => $id_contact])->row_array();

        $this->no_surat_jalan = $post['no_surat_jalan'];
        $this->id_contact = $post['id_contact'];
        $this->dalivery_date = date("Y-m-d H:i:s");
        $this->order_number = $post['order_number'];
        $this->ship_to_name = $post['ship_to_name'];
        $this->ship_to_address = $post['ship_to_address'];
        $this->ship_to_phone = $post['ship_to_phone'];
        $this->id_courier = $post['id_courier'];
        $this->id_kendaraan = $post['id_kendaraan'];
        $this->payment_scoring = $this->paymentScoring($selected_contact);
        if ($post['is_cod'] == true) {
            $this->is_cod = 1;
        } else {
            $this->is_cod = 0;
        }

        if ($post['is_tebus_murah'] == true) {
            $this->is_tebus_murah = 1;
        } else {
            $this->is_tebus_murah = 0;
        }

        $getSj = $this->db->get_where('tb_surat_jalan', ['no_surat_jalan' => $this->no_surat_jalan])->row_array();

        if ($getSj == null) {
            $query = $this->db->insert('tb_surat_jalan', $this);

            if ($query) {
                redirect('surat-jalan/' . $this->db->insert_id());
            } else {
                $this->session->set_flashdata('failed', "Gagal menyimpan data surat jalan!");
                redirect('surat-jalan');
            }
        } else {
            $this->session->set_flashdata('failed', "Oops terjadi kesalahan, harap coba lagi!");
            redirect('surat-jalan');
        }
    }

    public function update($id)
    {
        $post = $this->input->post();
        $this->no_surat_jalan = $post['no_surat_jalan'];
        $this->id_contact = $post['id_contact'];
        $this->order_number = $post['order_number'];
        $this->ship_to_name = $post['ship_to_name'];
        $this->ship_to_address = $post['ship_to_address'];
        $this->ship_to_phone = $post['ship_to_phone'];
        $this->id_courier = $post['id_courier'];
        $this->id_kendaraan = $post['id_kendaraan'];

        $query = $this->db->update('tb_surat_jalan', $this, ['id_surat_jalan' => $id]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function create($suratJalanData)
    {
        $query = $this->db->insert('tb_surat_jalan', $suratJalanData);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function delete($id)
    {
        $query = $this->db->delete('tb_surat_jalan', ['id_surat_jalan' => $id]);

        if ($query) {
            return true;
        } else {
            return false;
        }
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
                $jatuhTempo = date('Y-m-d', strtotime("+3 days", strtotime($invoice['date_invoice'])));
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
                }
            } else {
                if ($invoice['status_invoice'] == 'paid') {
                    $dateNow = date("Y-m-d");
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
