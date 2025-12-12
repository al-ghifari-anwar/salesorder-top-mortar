<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Feerenvi extends CI_Controller
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
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Rekap Fee Renvi';
        $data['menuGroup'] = 'Visit';
        $data['menu'] = 'TotalRenvi';
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
        $this->load->view('Feerenvirekap/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function rekap_fee($id_city)
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Rekap Fee Renvi';
        $data['menuGroup'] = 'Visit';
        $data['menu'] = 'TotalRenvi';
        $data['cities'] = $this->MCity->getAll();
        $data['city'] = $this->MCity->getById($id_city);
        $data['id_city'] = $id_city;
        // echo json_encode($data);
        // die;
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Feerenvirekap/List');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function print_rekap()
    {
        $post = $this->input->post();
        $month = $post['bulan'];
        $id_user = 0;
        $id_city = $post['id_city'];

        $data['city'] = $this->MCity->getById($id_city);
        $data['sales'] = $this->MVisit->getGroupedContact($id_user, $id_city, $month);
        $data['month'] = $month;
        // PDF
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Feerenvirekap/Print', $data, true);
        $mpdf->AddPage('P');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }
}
