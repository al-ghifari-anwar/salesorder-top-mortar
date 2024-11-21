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

    public function index()
    {
        $this->session->unset_userdata('locationData');
        $data['title'] = 'Lokasi Penukaran Voucher';

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Penukaranstore/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
        $this->load->view('Penukaranstore/Scripts');
    }

    public function add_latlong()
    {

        $loacationData = [
            'lat' => $_POST['lat'],
            'long' => $_POST['long']
        ];

        $this->session->set_userdata('locationData', $loacationData);

        // $this->session->set_flashdata('success', 'Berhasil mengambil foto');
        echo json_encode(["Sukes mengambil lokasi."]);
        // echo redirect('byrspta/detail');
    }

    public function list()
    {
        // echo json_encode($this->session->userdata());
        // die;
        $data['title'] = 'List Toko Penukaran Voucher';
        $data['loc_user'] = $this->session->userdata('locationData');
        $data['contactPriors'] = $this->MContact->getAllTopSellerNoLogin();

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Penukaranstore/List');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }
}
