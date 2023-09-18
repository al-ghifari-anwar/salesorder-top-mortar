<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Piutang extends CI_Controller
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
        $this->load->model('MPayment');
        $this->load->library('form_validation');
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
    }

    public function index()
    {
        $data['title'] = 'Rincian Pembayaran';
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Piutang/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function print()
    {
        $dateRange = $this->input->post("date_range");
        if ($dateRange) {
            $dates = explode("-", $dateRange);
            $invoice = $this->MInvoice->getAllByDateUnpaid(date('Y-m-d H:i:s', strtotime($dates[0] . " 00:00:00")), date('Y-m-d H:i:s', strtotime($dates[1] . " 23:59:59")));
        } else {
            // $invoice = $this->MInvoice->getAll();
        }

        // echo json_encode($invoice);
        $data['invoice'] = $invoice;
        $data['dateFrom'] = date("Y-m-d H:i:s", strtotime($dates[0] . " 00:00:00"));
        $data['dateTo'] = date("Y-m-d H:i:s", strtotime($dates[1] . " 23:59:59"));
        // PDF
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Piutang/Print', $data, true);
        $mpdf->AddPage('P');
        echo json_encode($invoice);
        // $mpdf->WriteHTML($html);
        // $mpdf->Output();
    }
}
