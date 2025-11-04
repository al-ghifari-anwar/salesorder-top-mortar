<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cusvisit extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('MSuratJalan');
        $this->load->model('MContact');
        $this->load->model('MProduk');
        $this->load->model('MDetailSuratJalan');
        $this->load->model('MUser');
        $this->load->model('MCity');
        $this->load->model('MKendaraan');
        $this->load->model('MVoucher');
        $this->load->model('MUser');
        $this->load->library('form_validation');
        date_default_timezone_set('Asia/Jakarta');
    }

    public function city_list()
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Customer Visit';
        $data['menuGroup'] = 'Visit';
        $data['menu'] = 'CusVisit';
        if ($this->session->userdata('level_user') == 'admin_c' || $this->session->userdata('level_user') == 'sales') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Cusvisit/CityList');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function index($id_city)
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Customer Visit';
        $data['menuGroup'] = 'Visit';
        $data['menu'] = 'CusVisit';
        $data['id_city'] = $id_city;
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Cusvisit/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function print($id_city)
    {
        // $post = $this->input->post();
        $dateRange = $_GET['date_range'];
        $dates = explode("-", $dateRange);
        $data['city'] = $this->MCity->getById($id_city);
        $data['contacts'] = $this->MContact->getAllNoFilter($id_city);
        $data['dateFrom'] = date("Y-m-d", strtotime($dates[0]));
        $data['dateTo'] = date("Y-m-d", strtotime($dates[1]));

        // Test
        // $this->load->view('Cusvisit/Print', $data);
        // PDF
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Cusvisit/Print', $data, true);
        $mpdf->AddPage('P');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }
}
