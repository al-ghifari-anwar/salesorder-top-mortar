<?php

class Tebusmurah extends CI_Controller
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
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['contactTebusmurahs'] = $this->db->join('tb_city', 'tb_city.id_city = tb_contact.id_city')->where('tb_contact.store_status', 'active')->where('tb_contact.id_contact IN (SELECT id_contact FROM tb_surat_jalan WHERE is_tebus_murah = 1)', null, false)->order_by('tb_city.nama_city', 'ASC')->get('tb_contact')->result_array();
        // $data = 1;
        // $html = $this->load->view('Notif/PrintPassive', $data);
        // PDF
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Tebusmurah/Print', $data, true);
        $mpdf->AddPage('P');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    public function cityList()
    {
        $data['title'] = 'Laporan Toko Tebusmurah';
        $data['menuGroup'] = 'Analisa';
        $data['menu'] = 'Tebusmurah';
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Tebusmurah/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function print()
    {
        $id_city = $_GET['ct'];

        $data['city'] = $this->db->get_where('tb_city', ['id_city' => $id_city])->row_array();
        $data['contacts'] = $this->db->where('tb_contact.id_city', $id_city)->where('tb_contact.id_contact IN (SELECT id_contact FROM tb_surat_jalan WHERE is_tebus_murah = 1)', null, false)->order_by('tb_contact.nama', 'ASC')->get('tb_contact')->result_array();

        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Tebusmurah/PrintRekap', $data, true);
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }
}
