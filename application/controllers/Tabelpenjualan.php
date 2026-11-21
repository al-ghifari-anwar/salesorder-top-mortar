<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Penjualan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $this->load->model('MSuratJalan');
        $this->load->model('MContact');
        $this->load->model('MProduk');
        $this->load->model('MDetailSuratJalan');
        $this->load->model('MUser');
        $this->load->model('MCity');
        $this->load->model('MKendaraan');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['title'] = 'Tabel Penjualan';
        $data['menuGroup'] = 'Penjualan';
        $data['menu'] = 'TabelPenjualan';

        $id_city = $this->input->get('ct');
        $daterange = $this->input->get('daterange');

        $data['citys'] = $this->MCity->getAll();

        $dateFrom = date('Y-m-01 00:00:00');
        $dateTo = date('Y-m-d 23:59:59');

        if ($daterange) {
            $dates = explode("-", $daterange);
            $dateFrom = date('Y-m-d 00:00:00', strtotime($dates[0]));
            $dateTo = date('Y-m-d 23:59:59', strtotime($dates[1]));
        }

        $data['items'] = $this->MDetailSuratJalan->getSoldItemsByDate($id_city, $dateFrom, $dateTo);
        $data['items_total'] = $this->MDetailSuratJalan->getSoldItemsByDateGlobalForTotal($id_city, $dateFrom, $dateTo);
        $data['dateFrom'] = $dateFrom;
        $data['dateTo'] = $dateTo;

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Tabelpenjualan/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }
}
