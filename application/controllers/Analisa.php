<?php

class Analisa extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $this->load->model('MCity');
        $this->load->model('MProduk');
        $this->load->model('MSuratJalan');
        $this->load->model('MDetailSuratJalan');
        $this->load->model('MInvoice');
        $this->load->model('MContact');
        $this->load->model('MKendaraan');
        $this->load->model('MUser');
        $this->load->model('MPayment');
        $this->load->library('form_validation');
    }

    public function passive()
    {
        $data['title'] = 'Toko Passive Dengan Pembayaran Bagus';

        $id_city = $this->input->post("id_city");

        $data['citys'] = $this->MCity->getAll();
        $data['contacts'] = $this->MContact->getAllDefault();

        if ($id_city) {
            $data['contacts'] = $this->MContact->getAllNoFilter($id_city);
        }
        // echo json_encode($data);
        // die;
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Analisa/Passive');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }
}
