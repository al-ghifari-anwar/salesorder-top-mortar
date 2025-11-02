<?php

class Jadwalvisit extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MCity');
        $this->load->model('MProduk');
        $this->load->model('MSuratJalan');
        $this->load->model('MDetailSuratJalan');
        $this->load->model('MInvoice');
        $this->load->model('MContact');
        $this->load->model('MKendaraan');
        $this->load->model('MUser');
        $this->load->model('MVisit');
        $this->load->model('MRenvi');
        $this->load->model('MInvoice');
        $this->load->library('form_validation');
    }

    public function index()
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Jadwal Visit Baru';
        $data['menuGroup'] = 'Visit';
        $data['menu'] = 'JadwalVisit';
        if ($this->session->userdata('level_user') == 'admin_c' || $this->session->userdata('level_user') == 'sales') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Jadwalvisit/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function print()
    {
        $id_city = $_GET['ct'];

        $data['city'] = $this->MCity->getById($id_city);

        $cluster = 0;
        if (date('D') == 'Mon' || date('D') == 'Thu') {
            $cluster = 1;
        } else if (date('D') == 'Tue' || date('D') == 'Fri') {
            $cluster = 2;
        } else if (date('D') == 'Wed' || date('D') == 'Sat') {
            $cluster = 2;
        }

        if (date('D') == 'Mon') {
            $dayName = 'senin';
        } else if (date('D') == 'Tue') {
            $dayName = 'selasa';
        } else if (date('D') == 'Wed') {
            $dayName = 'rabu';
        } else if (date('D') == 'Thu') {
            $dayName = 'kamis';
        } else if (date('D') == 'Fri') {
            $dayName = 'jumat';
        } else if (date('D') == 'Sat') {
            $dayName = 'sabtu';
        }

        $data['cluster'] = $cluster;
        $data['dayName'] = $dayName;

        $jatem1s = $this->MRenvi->getJatem1($id_city);
        $jatem2s = $this->MRenvi->getJatem2($id_city);
        $jatem3s = $this->MRenvi->getJatem3($id_city);
        $mingguans = $this->MRenvi->getMingguan($id_city);
        $passives = $this->MRenvi->getPassive($id_city);

        $renvis = array();
        $renvisPassives = array();

        foreach ($jatem1s as $jatem1) {
            $id_inv = $jatem1['id_invoice'];
            $count = $this->db->query("SELECT COUNT(*) AS jmlRenvis FROM tb_renvis_jatem WHERE id_invoice = '$id_inv' AND type_renvis = 'jatem1'")->row_array();
            $jatuhTempo = date('d M Y', strtotime("+" . $jatem1['termin_payment'] . " days", strtotime($jatem1['date_invoice'])));
            $jatem = date('Y-m-d', strtotime("+" . $jatem1['termin_payment'] . " days", strtotime($jatem1['date_invoice'])));
            $date1 = new DateTime(date("Y-m-d"));
            $date2 = new DateTime($jatuhTempo);
            $days  = $date2->diff($date1)->format('%a');
            $operan = "";
            if ($date1 < $date2) {
                $operan = "-";
            }
            $days = $operan . $days;
            $jatem1['days'] = $days;
            $jatem1['jatuh_tempo'] = $jatuhTempo;
            $jatem1['jatem'] = $jatem;
            $jatem1['is_new'] = $count['jmlRenvis'] == 1 ? "1" : "0";
            $id_con = $jatem1['id_contact'];
            $dateJatem = date('Y-m-d', strtotime("+" . $jatem1['termin_payment'] . " days", strtotime($jatem1['date_invoice'])));
            $lastVisit = $this->db->query("SELECT * FROM tb_visit WHERE id_contact = '$id_con' AND DATE(date_visit) >= '$dateJatem' AND source_visit IN ('jatem1','jatem2','jatem3','weekly') ORDER BY date_visit DESC LIMIT 1")->row_array();
            $created_at = $jatem1['created_at'];
            $jatem1['created_at'] = $lastVisit == null ? $created_at : $lastVisit['date_visit'];
            $renvis[] = $jatem1;
        }

        foreach ($jatem2s as $jatem2) {
            $id_inv = $jatem2['id_invoice'];
            $count = $this->db->query("SELECT COUNT(*) AS jmlRenvis FROM tb_renvis_jatem WHERE id_invoice = '$id_inv' AND type_renvis = 'jatem2'")->row_array();
            $jatuhTempo = date('d M Y', strtotime("+" . $jatem2['termin_payment'] . " days", strtotime($jatem2['date_invoice'])));
            $jatem = date('Y-m-d', strtotime("+" . $jatem2['termin_payment'] . " days", strtotime($jatem2['date_invoice'])));
            $date1 = new DateTime(date("Y-m-d"));
            $date2 = new DateTime($jatuhTempo);
            $days  = $date2->diff($date1)->format('%a');
            $operan = "";
            if ($date1 < $date2) {
                $operan = "-";
            }
            $days = $operan . $days;
            $jatem2['days'] = $days;
            $jatem2['jatuh_tempo'] = $jatuhTempo;
            $jatem2['jatem'] = $jatem;
            $jatem2['is_new'] = $count['jmlRenvis'] == 1 ? "1" : "0";
            $id_con = $jatem2['id_contact'];
            $dateJatem = date('Y-m-d', strtotime("+" . $jatem2['termin_payment'] . " days", strtotime($jatem2['date_invoice'])));
            $lastVisit = $this->db->query("SELECT * FROM tb_visit WHERE id_contact = '$id_con' AND DATE(date_visit) >= '$dateJatem' AND source_visit IN ('jatem1','jatem2','jatem3','weekly') ORDER BY date_visit DESC LIMIT 1")->row_array();
            $created_at = $jatem2['created_at'];
            $jatem2['created_at'] = $lastVisit == null ? $created_at : $lastVisit['date_visit'];
            $renvis[] = $jatem2;
        }

        foreach ($jatem3s as $jatem3) {
            $id_inv = $jatem3['id_invoice'];
            $count = $this->db->query("SELECT COUNT(*) AS jmlRenvis FROM tb_renvis_jatem WHERE id_invoice = '$id_inv' AND type_renvis = 'jatem3'")->row_array();
            $jatuhTempo = date('d M Y', strtotime("+" . $jatem3['termin_payment'] . " days", strtotime($jatem3['date_invoice'])));
            $jatem = date('Y-m-d', strtotime("+" . $jatem3['termin_payment'] . " days", strtotime($jatem3['date_invoice'])));
            $date1 = new DateTime(date("Y-m-d"));
            $date2 = new DateTime($jatuhTempo);
            $days  = $date2->diff($date1)->format('%a');
            $operan = "";
            if ($date1 < $date2) {
                $operan = "-";
            }
            $days = $operan . $days;
            $jatem3['days'] = $days;
            $jatem3['jatuh_tempo'] = $jatuhTempo;
            $jatem3['jatem'] = $jatem;
            $jatem3['is_new'] = $count['jmlRenvis'] == 1 ? "1" : "0";
            $id_con = $jatem3['id_contact'];
            $dateJatem = date('Y-m-d', strtotime("+" . $jatem3['termin_payment'] . " days", strtotime($jatem3['date_invoice'])));
            $lastVisit = $this->db->query("SELECT * FROM tb_visit WHERE id_contact = '$id_con' AND DATE(date_visit) >= '$dateJatem' AND source_visit IN ('jatem1','jatem2','jatem3','weekly') ORDER BY date_visit DESC LIMIT 1")->row_array();
            $created_at = $jatem3['created_at'];
            $jatem3['created_at'] = $lastVisit == null ? $created_at : $lastVisit['date_visit'];
            $renvis[] = $jatem3;
        }

        foreach ($mingguans as $mingguan) {
            $id_inv = $mingguan['id_invoice'];
            $mingguan['id_renvis_jatem'] = $mingguan['id_rencana_visit'];
            $count = $this->db->query("SELECT COUNT(*) AS jmlRenvis FROM tb_renvis_jatem WHERE id_invoice = '$id_inv' AND type_renvis = 'jatem3'")->row_array();
            $jatuhTempo = date('d M Y', strtotime("+" . $mingguan['termin_payment'] . " days", strtotime($mingguan['date_invoice'])));
            $jatem = date('Y-m-d', strtotime("+" . $mingguan['termin_payment'] . " days", strtotime($mingguan['date_invoice'])));
            $date1 = new DateTime(date("Y-m-d"));
            $date2 = new DateTime($jatuhTempo);
            $days  = $date2->diff($date1)->format('%a');
            $operan = "";
            if ($date1 < $date2) {
                $operan = "-";
            }
            $days = $operan . $days;
            $mingguan['days'] = $days;
            $mingguan['jatuh_tempo'] = $jatuhTempo;
            $mingguan['jatem'] = $jatem;
            $mingguan['is_new'] = $count['jmlRenvis'] == 1 ? "1" : "0";
            $id_con = $mingguan['id_contact'];
            $dateJatem = date('Y-m-d', strtotime("+" . $mingguan['termin_payment'] . " days", strtotime($mingguan['date_invoice'])));
            $lastVisit = $this->db->query("SELECT * FROM tb_visit WHERE id_contact = '$id_con' AND DATE(date_visit) >= '$dateJatem' AND source_visit IN ('jatem1','jatem2','jatem3','weekly') ORDER BY date_visit DESC LIMIT 1")->row_array();
            $created_at = $mingguan['created_at'];
            $mingguan['created_at'] = $lastVisit == null ? $created_at : $lastVisit['date_visit'];
            $mingguan['type_renvis'] = $mingguan['type_rencana'];
            $renvis[] = $mingguan;
        }

        foreach ($passives as $passive) {
            $id_con = $passive['id_contact'];
            $count = $this->db->query("SELECT COUNT(*) AS jmlRenvis FROM tb_rencana_visit WHERE id_contact = '$id_con' AND type_rencana = 'passive'")->row_array();
            $date_margin = date("Y-m-d", strtotime("-1 month"));
            $lastVisit = $this->db->query("SELECT * FROM tb_visit WHERE id_contact = '$id_con' AND source_visit IN ('jatem1','jatem2','jatem3','weekly','voucher','passive','renvisales') AND date_visit >= '$date_margin' ORDER BY date_visit DESC LIMIT 1")->row_array();
            $passive['last_visit'] = $lastVisit == null ? '0000-00-00' : $lastVisit['date_visit'];
            $created_at = $passive['created_at'];
            $passive['created_at'] = $lastVisit == null ? $created_at : $lastVisit['date_visit'];
            $passive['is_new'] = $count['jmlRenvis'] == 1 ? "1" : "0";
            $passive['type_renvis'] = $passive['type_rencana'];

            $getBadScore = $this->db->query("SELECT * FROM tb_bad_score WHERE id_contact = '$id_con'")->row_array();

            if ($getBadScore) {
                if ($getBadScore['is_approved'] != 1) {
                    $renvisPassives[] = $passive;
                }
            } else {
                $renvisPassives[] = $passive;
            }
        }

        $data['renvis'] = $renvis;
        $data['renvisPassives'] = $renvisPassives;

        $this->load->view('Jadwalvisit/Print', $data);

        // Buat direktori penyimpanan sementara
        // $folderPath = FCPATH . 'assets/tmp/renvis/';
        // Nama file berdasarkan invoice ID + timestamp
        // $fileName = 'renvi_' . $this->session->userdata('id_user') . '_' . time() . '.pdf';
        // $filePath = $folderPath . $fileName;

        // $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        // $mpdf->SetMargins(0, 0, 5);
        // $html = $this->load->view('Jadwalvisit/Print', $data, true);
        // $mpdf->AddPage('P');
        // $mpdf->WriteHTML($html);
        // $mpdf->Output();
        // $mpdf->Output($filePath, \Mpdf\Output\Destination::FILE);

        // $user = $this->db->get_where('tb_user', ['id_user' => $this->session->userdata('id_user')])->row_array();
    }
}
