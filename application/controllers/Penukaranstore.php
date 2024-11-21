<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Penukaranstore extends CI_Controller
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
        // if ($this->session->userdata('id_user') == null) {
        //     redirect('login');
        // }
    }

    public function city_list()
    {
        $data['title'] = 'Lokasi Penukaran Voucher';
        $data['city'] = $this->MCity->getAllNoLogin();

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Penukaranstore/CityList');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function index($id_city)
    {
        $data['title'] = 'Lokasi Penukaran Voucher';
        // $data['contacts'] = $this->MContact->getAllForPriority($id_city);
        $data['contactPriors'] = $this->MContact->getAllTopSellerCity($id_city);
        // $data['city'] = $this->MCity->getById($id_city);
        // $data['id_city'] = $id_city;
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Penukaranstore/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }
}
