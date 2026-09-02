<?php

class Reportjadwalvisitbulanan extends CI_Controller
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
        $this->load->model('HTelegram');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['title'] = 'Report Jadwal Visit Bulanan';
        $data['menuGroup'] = 'Visit';
        $data['menu'] = 'ReportJadwalVisitBulanan';
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['city'] = $this->MCity->getById($this->session->userdata('id_city'));
        } else if ($this->session->userdata('level_user') == 'salesspv') {
            $userCity = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->row_array();
            $nama_city = trim(preg_replace("/\\d+/", "", $userCity['nama_city']));
            $data['city'] = $this->db->like('nama_city', $nama_city)->get_where('tb_city', ['id_distributor' => $this->session->userdata('id_distributor')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Reportjadwalvisitbulanan/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function print()
    {
        $year = $_GET['year'];
        $month = $_GET['month'];
        $id_city = $_GET['ct'];

        $data['city'] = $this->MCity->getById($id_city);

        $data['year'] = $year;
        $data['month'] = $month;

        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_jadwal_visit.id_contact');
        $data['jadwalVisits'] = $this->db->order_by('id_jadwal_visit', 'ASC')->get_where('tb_jadwal_visit', ['MONTH(date_jadwal_visit)' => $month, 'YEAR(date_jadwal_visit)' => $year, 'tb_jadwal_visit.id_city' => $id_city])->result_array();

        // $this->load->view('Reportjadwalvisit/Print', $data);

        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Reportjadwalvisitbulanan/Print', $data, true);
        $mpdf->AddPage('P');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }
}
