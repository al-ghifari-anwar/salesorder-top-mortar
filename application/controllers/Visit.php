<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Visit extends CI_Controller
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
        // $this->load->library('phpqrcode/qrlib');
    }

    public function index()
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Visit';
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Visit/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function visit_by_city($id_city)
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }

        $dateRange = $this->input->post("date_range");
        $id_user = $this->input->post("id_user");
        $bulan = $this->input->post("bulan");

        if ($bulan) {
            $dates = explode("-", $dateRange);
            $data['visit'] = $this->MVisit->getByCityAndDate($id_city, $id_user, $bulan);
        } else {
            // $invoice = $this->MInvoice->getAll();
            $data['visit'] = $this->MVisit->getAllByCity($id_city);
        }
        $data['title'] = 'Visit';
        $data['cities'] = $this->MCity->getAll();
        $data['city'] = $this->MCity->getById($id_city);
        $data['id_city'] = $id_city;
        // echo json_encode($data);
        // die;
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Visit/List');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function approve($id)
    {
        $approve = $this->MVisit->approve($id);

        if ($approve) {
            $this->session->set_flashdata('success', "Berhasil approve visit!");
            redirect('visit');
        } else {
            $this->session->set_flashdata('failed', "Gagal approve visit!");
            redirect('visit');
        }
    }

    public function lapkurir_city_list()
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Visit';
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Lapkurir/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function lapkurir_by_city($id_city)
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }

        $dateRange = $this->input->post("date_range");
        $id_user = $this->input->post("id_user");

        if ($dateRange) {
            $dates = explode("-", $dateRange);
            $data['visit'] = $this->MVisit->getByCityAndDate($id_city, date('Y-m-d H:i:s', strtotime($dates[0] . " 00:00:00")), date('Y-m-d H:i:s', strtotime($dates[1] . " 23:59:59")), $id_user);
        } else {
            // $invoice = $this->MInvoice->getAll();
            $data['visit'] = $this->MVisit->getKurirByCity($id_city);
        }
        $data['title'] = 'Visit';
        $data['cities'] = $this->MCity->getAll();
        $data['city'] = $this->MCity->getById($id_city);
        $data['id_city'] = $id_city;
        // echo json_encode($data);
        // die;
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Lapkurir/List');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function lap_absen($id_city, $type)
    {
        $post = $this->input->post();
        $month = $post['bulan'];

        $data['city'] = $this->MCity->getById($id_city);
        $data['user'] = $this->MVisit->getGroupedContactGlobal($id_city, $month, $type);
        $data['month'] = $month;
        $data['type'] = $type;
        // PDF
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Visit/Print', $data, true);
        $mpdf->AddPage('L');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }
}
