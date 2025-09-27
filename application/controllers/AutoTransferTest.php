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
        $data['menuGroup'] = '';
        $data['menu'] = '';

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

        $dates = explode("-", $dateRange);
        $dateFrom = date("Y-m-d", strtotime($dates[0]));
        $dateTo = date("Y-m-d", strtotime($dates[1]));
        $data['dates'] = $dates;
        $this->db->join('tb_city', 'tb_city.id_city = tb_log_bca_test.id_city');
        $this->db->group_by('tb_log_bca_test.id_city');
        $data['city'] = $this->db->get_where('tb_log_bca_test', ['DATE(transaction_date) >=' => $dateFrom, 'DATE(transaction_date) <= ' => $dateTo])->result_array();
        $data['dateFrom'] = $dateFrom;
        $data['dateTo'] = $dateTo;
        // $this->load->view('AutoTransferTest/Print', $data);
        // PDF
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('AutoTransferTest/Print', $data, true);
        $mpdf->AddPage('L');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }
}
