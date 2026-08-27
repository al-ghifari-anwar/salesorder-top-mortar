<?php

class Tokoultah extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $this->load->model('MContact');
        $this->load->library('form_validation');
        $this->load->model('MCity');
    }

    public function index()
    {
        $post = $this->input->post();
        $data['title'] = 'Toko Ultah ' . date('d F Y');
        $data['menuGroup'] = 'Marketing';
        $data['menu'] = 'Tokoultah';

        $data['toko'] = $this->db->where('MONTH(tgl_lahir)', date('m'))->where('DATE(tgl_lahir)', date('d'))->get('tb_contact')->result_array();

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Tokoultah/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }
}
