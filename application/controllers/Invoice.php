<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Invoice extends CI_Controller
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
        $this->load->library('form_validation');
        // $this->load->library('phpqrcode/qrlib');
    }

    public function index()
    {
        $data['title'] = 'Invoice';
        $data['city'] = $this->MCity->getAll();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Invoice/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function invoice_by_city($id_city)
    {
        $data['title'] = 'Invoice';
        $data['cities'] = $this->MCity->getAll();
        $data['city'] = $this->MCity->getById($id_city);
        $data['invoice'] = $this->MInvoice->getAll();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Invoice/List');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function print($id)
    {
        $invoice = $this->MInvoice->getById($id);
        $data['invoice'] = $invoice;
        $data['store'] = $this->MContact->getById($invoice['id_contact']);
        $data['kendaraan'] = $this->MKendaraan->getById($invoice['id_kendaraan']);
        $data['courier'] = $this->MUser->getById($invoice['id_courier']);
        $data['produk'] = $this->MDetailSuratJalan->getAll($invoice['id_surat_jalan']);
        // echo json_encode($this->MDetailSuratJalan->getAll($invoice['id_surat_jalan']));
        // die;
        $mpdf = new \Mpdf\Mpdf(['format' => 'A5']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Invoice/Print', $data, true);
        $mpdf->AddPage('L');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }
}
