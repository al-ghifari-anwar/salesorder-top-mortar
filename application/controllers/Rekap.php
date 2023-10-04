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

    public function index()
    {
        $data['title'] = 'Rekap Invoice';
        $data['toko'] = $this->MContact->getAllDefault();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Rekap/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function rekap()
    {
        $dateRange = $this->input->post("date_range");
        $id_contact = $this->input->post("id_contact");
        if ($dateRange) {
            $dates = explode("-", $dateRange);
            $invoice = $this->MInvoice->getGroupedContact(date('Y-m-d H:i:s', strtotime($dates[0] . " 00:00:00")), date('Y-m-d H:i:s', strtotime($dates[1] . " 23:59:59")), $id_contact);
        } else {
            // $invoice = $this->MInvoice->getAll();
        }

        $data['invoice'] = $invoice;
        $data['dateFrom'] = date("Y-m-d H:i:s", strtotime($dates[0] . " 00:00:00"));
        $data['dateTo'] = date("Y-m-d H:i:s", strtotime($dates[1] . " 23:59:59"));
        // PDF
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Rekap/Print', $data, true);
        $mpdf->AddPage('P');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }
}
