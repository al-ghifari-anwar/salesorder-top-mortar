<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Rekap extends CI_Controller
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

    public function city_list()
    {
        $data['title'] = 'Rekap Invoice';
        $data['menuGroup'] = 'Invoice';
        $data['menu'] = 'RekapInvoice';
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Rekap/CityList');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function index($id_city)
    {
        $data['title'] = 'Rekap Invoice';
        $data['menuGroup'] = 'Invoice';
        $data['menu'] = 'RekapInvoice';
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['toko'] = $this->MContact->getAll($this->session->userdata('id_city'));
            $data['city'] = $this->MCity->getById($this->session->userdata('id_city'));
        } else {
            $data['toko'] = $this->MContact->getAllDefault();
            $data['city'] = $this->MCity->getAll();
        }
        $data['id_city'] = $id_city;
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Rekap/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function rekap()
    {
        ini_set('pcre.backtrack_limit', '5000000');
        ini_set('memory_limit', '512M');
        $dateRange = $this->input->post("date_range");
        $id_contact = $this->input->post("id_contact");
        $no_invoice = $this->input->post("no_invoice");
        $id_city = $this->input->post("id_city");
        if ($no_invoice == null) {
            $no_invoice = 0;
        }
        if ($dateRange) {
            $dates = explode("-", $dateRange);
            $invoice = $this->MInvoice->getGroupedContact(date('Y-m-d H:i:s', strtotime($dates[0] . " 00:00:00")), date('Y-m-d H:i:s', strtotime($dates[1] . " 23:59:59")), $id_contact, $no_invoice, $id_city);
        } else {
            // $invoice = $this->MInvoice->getAll();
        }

        $data['invoice'] = $invoice;
        $data['dateFrom'] = date("Y-m-d H:i:s", strtotime($dates[0] . " 00:00:00"));
        $data['dateTo'] = date("Y-m-d H:i:s", strtotime($dates[1] . " 23:59:59"));
        $data['no_invoice'] = $no_invoice;
        // PDF
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Rekap/Print', $data, true);
        $mpdf->AddPage('P');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }
}
