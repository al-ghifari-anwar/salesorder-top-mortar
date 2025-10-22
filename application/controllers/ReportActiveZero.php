<?php

class ReportActiveZero extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MContact');
    }

    public function index()
    {
        $data['title'] = 'Toko Aktif Penjualan 0';
        $data['menuGroup'] = 'Analisa';
        $data['menu'] = 'ReportActiveZero';

        $data['contacts'] = $this->MContact->getAllByStatus('passive');

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('ReportActiveZero/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }
}
