<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Absen extends CI_Controller
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
        $this->load->model('MVoucher');
        $this->load->library('form_validation');
        date_default_timezone_set('Asia/Jakarta');
    }

    public function index()
    {
        $data['title'] = 'Absensi Harian';
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Absen/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function print()
    {
        $post = $this->input->post();
        $dateRange = $post['date_range'];
        $dates = explode("-", $dateRange);
        $data['dateFrom'] = date("Y-m-d", strtotime($dates[0]));
        $data['dateTo'] = date("Y-m-d", strtotime($dates[1]));
        $data['users'] = $this->MUser->getByIdDist($this->session->userdata('id_distributor'));

        $start = date("Y-m-01");
        $end = date("Y-m-01", strtotime("+1 month"));
        $periods = new DatePeriod(
            new DateTime($start),
            new DateInterval('P1D'),
            new DateTime($end)
        );

        $data['periods'] = $periods;

        // Test
        $this->load->view('Absen/Print', $data);
        // PDF
        // $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        // $mpdf->SetMargins(0, 0, 5);
        // $html = $this->load->view('Absen/Print', $data, true);
        // $mpdf->AddPage('L');
        // $mpdf->WriteHTML($html);
        // $mpdf->Output();
    }
}
