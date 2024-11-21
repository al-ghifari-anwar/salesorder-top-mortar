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

    public function index($id_city = null)
    {
        $data['title'] = 'Lokasi Penukaran Voucher';

        $data['contactPriors'] = $this->MContact->getAllTopSellerCityNoLogin($id_city);

        $PublicIP = $this->get_client_ip();
        // $PublicIP = getenv('REMOTE_ADDR');
        $json     = file_get_contents("http://ipinfo.io/$PublicIP/geo");
        $json     = json_decode($json, true);
        // $country  = $json['country'];
        // $region   = $json['region'];
        // $city     = $json['city'];

        echo json_encode($json);
        die;

        // $this->load->view('Theme/Header', $data);
        // $this->load->view('Theme/Menu');
        // $this->load->view('Penukaranstore/Index');
        // $this->load->view('Theme/Footer');
        // $this->load->view('Theme/Scripts');
    }

    function get_client_ip()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } else if (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
        }

        return $ipaddress;
    }
}
