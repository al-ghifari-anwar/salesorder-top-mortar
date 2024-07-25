<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AutoTransferTest extends CI_Controller
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
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['title'] = 'Laporan Auto Transfer Test';

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('AutoTransferTest/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function print()
    {
        $post = $this->input->post();
        $dateRange = $post["date_range"];
        $id_city = $post["id_city"];

        $data['city'] = $this->MCity->getById($id_city);
        $data['produk'] = $this->db->get_where("tb_produk", ['id_city' => $id_city])->result_array();
        $data['dates'] = explode("-", $dateRange);
        // $this->load->view('Stok/Print', $data);
        // PDF
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Stok/Print', $data, true);
        $mpdf->AddPage('L');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }
}
