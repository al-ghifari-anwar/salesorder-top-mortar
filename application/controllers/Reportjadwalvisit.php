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
        $data['menu'] = 'ReportJadwalVIsit';
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['toko'] = $this->MContact->getAll($this->session->userdata('id_city'));
            $data['city'] = $this->MCity->getById($this->session->userdata('id_city'));
        } else {
            $data['toko'] = $this->MContact->getAllDefault();
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

        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_visit.id_contact');
        $this->db->join('tb_user', 'tb_user.id_user = tb_visit.id_user');
        $this->db->not_like('source_visit', 'absen');
        $data['visits'] = $this->db->get_where('tb_visit', ['DATE(date_visit)' => $date, 'tb_contact.id_city' => $id_city])->result_array();

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
