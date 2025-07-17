<?php


class Invoicecod extends CI_Controller
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
        $this->load->library('form_validation');
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
    }

    public function waitingCity()
    {
        $data['title'] = 'Invoice COD Belum Lunas';
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Invoicecod/WaitingCity');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function waiting()
    {
        // $city = $this->MCity->getById($id_city);
        $data['title'] = 'Invoice COD Belum Lunas';
        $data['invoice'] = $this->MInvoice->getCodWaiting();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Invoicecod/Waitinglist');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }
}
