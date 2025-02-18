<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Stok extends CI_Controller
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
        $data['title'] = 'Stok Produk';
        // if ($this->session->userdata('level_user') == 'admin_c') {
        //     $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        // } else {
        $data['gudangs'] = $this->db->get('tb_gudang_stok')->result_array();
        // }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Stok/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function list($id_gudang_stok)
    {
        $data['title'] = 'Stok Produk';
        $data['id_gudang_stok'] = $id_gudang_stok;

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Stok/List');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function lap_stok()
    {
        $post = $this->input->post();
        $dateRange = $post["date_range"];
        $id_gudang_stok = $post["id_gudang_stok"];

        $data['gudang'] = $this->db->get_where('tb_gudang_stok', ['id_gudang_stok' => $id_gudang_stok])->row_array();
        $data['masterProduks'] = $this->db->get_where("tb_master_produk", ['id_distributor' => $this->session->userdata('id_distributor')])->result_array();
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
