<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Toko extends CI_Controller
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
        $data['title'] = 'Toko';

        if ($post) {
            $id_city = $post['id_city'];
            $status = $post['status'];
            $data['toko'] = $this->MContact->getByCityStatus($id_city, $status);
        } else {
            $data['toko'] = $this->MContact->getAllDefault();
        }
        $data['city'] = $this->MCity->getAll();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Toko/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }
}
