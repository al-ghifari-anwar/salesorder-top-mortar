<?php

class TargetVisitTagihan extends CI_Controller
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
        $this->load->model('MVisit');
        $this->load->model('MRenvi');
        $this->load->model('MInvoice');
        $this->load->model('HTelegram');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $post = $this->input->post();

        $date = date('Y-m-d');

        if (isset($post['date'])) {
            $date = date('Y-m-d', strtotime($post['date']));
        }

        $data['title'] = 'Target Visit Tagihan';
        $data['menuGroup'] = 'Pembayaran';
        $data['menu'] = 'TargetVisitTagihan';

        $data['date'] = $date;
        $data['city'] = $this->MCity->getAll();
        $data['jadwalVisits'] = $this->db->select('id_city,date_jadwal_visit,SUM(total_invoice) AS total_invoice')->where('DATE(date_jadwal_visit)', $date)->where('total_invoice >', 0)->group_by('id_city')->get('tb_jadwal_visit')->result_array();

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('TargetVisitTagihan/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }
}
