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
        // if ($this->session->userdata('id_user') == null) {
        //     redirect('login');
        // }
    }

    public function index()
    {
        $data['title'] = 'Piutang';
        $data['menuGroup'] = 'Piutang';
        $data['menu'] = 'Piutang';
        $data['toko'] = $this->MContact->getAllDefault();
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Piutang/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function jatuh_tempo()
    {
        $data['title'] = 'Piutang Jatuh Tempo';
        $data['menuGroup'] = 'Piutang';
        $data['menu'] = 'PiutangJatem';
        $data['toko'] = $this->MContact->getAllDefault();
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('JatuhTempo/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function print()
    {
        $dateRange = $this->input->post("date_range");
        $id_contact = $this->input->post("id_contact");
        $id_city = $this->input->post("id_city");

        if ($dateRange) {
            $dates = explode("-", $dateRange);
            $invoice = $this->MInvoice->getGroupedContactUnpaid(date('Y-m-d H:i:s', strtotime($dates[0] . " 00:00:00")), date('Y-m-d H:i:s', strtotime($dates[1] . " 23:59:59")), $id_contact, $id_city);
        } else {
            // $invoice = $this->MInvoice->getAll();
        }

        // echo json_encode($invoice);
        // die;
        $data['invoice'] = $invoice;
        $data['dateFrom'] = date("Y-m-d H:i:s", strtotime($dates[0] . " 00:00:00"));
        $data['dateTo'] = date("Y-m-d H:i:s", strtotime($dates[1] . " 23:59:59"));

        // $this->load->view('Piutang/Print', $data);

        // PDF
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Piutang/Print', $data, true);
        $mpdf->AddPage('P');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    public function print_jatuh_tempo()
    {
        date_default_timezone_set('Asia/Jakarta');
        $id_city = $this->input->post("id_city");
        $id_distributor = $this->session->userdata('id_distributor');

        // $dates = explode("-", $dateRange);
        $invoice = $this->MInvoice->getInvoiceJatuhTempo($id_city, $id_distributor);

        $data['invoice'] = $invoice;
        if ($id_city != 0) {
            $data['city'] = $this->MCity->getById($id_city);
        } else {
            $data['city'] = ['nama_city' => 'Keseluruhan'];
        }
        // $data['dateFrom'] = date("Y-m-d H:i:s", strtotime($dates[0] . " 00:00:00"));
        // $data['dateTo'] = date("Y-m-d H:i:s", strtotime($dates[1] . " 23:59:59"));
        // PDF
        // echo json_encode($invoice);
        // die;
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        // $this->load->view('JatuhTempo/Print', $data);
        $html = $this->load->view('JatuhTempo/Print', $data, true);
        $mpdf->AddPage('P');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    public function rekap()
    {
        $data['title'] = 'Rekap Piutang';
        $data['menuGroup'] = 'Piutang';
        $data['menu'] = 'RekapPiutang';
        $data['city'] = $this->MCity->getAll();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Piutang/Rekap');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function webhook_tagihan()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $id_city = $_GET['c'];
            $id_distributor = $_GET['dst'];

            // $dates = explode("-", $dateRange);
            if ($id_city == 0) {
                $data['city'] = ['nama_city' => 'Keseluruhan'];
            } else {
                $data['city'] = $this->MCity->getById($id_city);
            }
            $invoice = $this->MInvoice->getInvoiceJatuhTempo($id_city, $id_distributor);

            $data['invoice'] = $invoice;
            // $data['dateFrom'] = date("Y-m-d H:i:s", strtotime($dates[0] . " 00:00:00"));
            // $data['dateTo'] = date("Y-m-d H:i:s", strtotime($dates[1] . " 23:59:59"));
            // PDF
            // $this->load->view('JatuhTempo/Print', $data);
            $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
            $mpdf->SetMargins(0, 0, 5);
            $html = $this->load->view('JatuhTempo/Print', $data, true);
            $mpdf->AddPage('P');
            $mpdf->WriteHTML($html);
            $mpdf->Output();
        }
    }
}
