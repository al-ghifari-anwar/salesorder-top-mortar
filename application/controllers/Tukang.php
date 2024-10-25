<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tukang extends CI_Controller
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
        $this->load->model('MVoucherTukang');
        $this->load->library('form_validation');
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
    }

    public function index()
    {
        $data['title'] = 'Data Tukang Top Mortar';
        $data['tukangs'] = $this->db->get('tb_tukang')->result_array();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Tukang/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }
}
