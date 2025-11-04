<?php

class Reportjadwalvisit extends CI_Controller
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
        $data['title'] = 'Report Jadwal Visit';
        $data['menuGroup'] = 'Visit';
        $data['menu'] = 'ReportJadwalVisit';
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['city'] = $this->MCity->getById($this->session->userdata('id_city'));
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Reportjadwalvisit/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function print()
    {
        $date = $_GET['date'];
        $id_city = $_GET['ct'];

        $data['city'] = $this->MCity->getById($id_city);

        $cluster = 0;
        if (date('D', strtotime($date)) == 'Mon' || date('D', strtotime($date)) == 'Thu') {
            $cluster = 1;
        } else if (date('D', strtotime($date)) == 'Tue' || date('D', strtotime($date)) == 'Fri') {
            $cluster = 2;
        } else if (date('D', strtotime($date)) == 'Wed' || date('D', strtotime($date)) == 'Sat') {
            $cluster = 2;
        }

        if (date('D', strtotime($date)) == 'Mon') {
            $dayName = 'senin';
        } else if (date('D', strtotime($date)) == 'Tue') {
            $dayName = 'selasa';
        } else if (date('D', strtotime($date)) == 'Wed') {
            $dayName = 'rabu';
        } else if (date('D', strtotime($date)) == 'Thu') {
            $dayName = 'kamis';
        } else if (date('D', strtotime($date)) == 'Fri') {
            $dayName = 'jumat';
        } else if (date('D', strtotime($date)) == 'Sat') {
            $dayName = 'sabtu';
        }

        $data['cluster'] = $cluster;
        $data['dayName'] = $dayName;
        $data['date'] = $date;

        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_jadwal_visit.id_contact');
        $data['jadwalVisits'] = $this->db->get_where('tb_jadwal_visit', ['DATE(date_jadwal_visit)' => $date, 'tb_jadwal_visit.id_city' => $id_city, 'tb_jadwal_visit.cluster_jadwal_visit' => $cluster])->result_array();

        // $this->load->view('Jadwalvisit/Print', $data);

        // Buat direktori penyimpanan sementara
        // $folderPath = FCPATH . 'assets/tmp/renvis/';
        // Nama file berdasarkan invoice ID + timestamp
        // $fileName = 'renvi_' . $this->session->userdata('id_user') . '_' . time() . '.pdf';
        // $filePath = $folderPath . $fileName;

        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Reportjadwalvisit/Print', $data, true);
        $mpdf->AddPage('P');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
        // $mpdf->Output($filePath, \Mpdf\Output\Destination::FILE);

        // $user = $this->db->get_where('tb_user', ['id_user' => $this->session->userdata('id_user')])->row_array();
    }
}
