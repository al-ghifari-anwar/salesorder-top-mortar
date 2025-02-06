<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Status extends CI_Controller
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
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Rekap Status';
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Status/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function rekap_fee($id_city)
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Rekap Status';
        $data['cities'] = $this->MCity->getAll();
        $data['city'] = $this->MCity->getById($id_city);
        $data['id_city'] = $id_city;
        // echo json_encode($data);
        // die;
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Status/List');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function print_rekap()
    {
        $post = $this->input->post();
        $month = $post['bulan'];
        $year = $post['tahun'];
        $id_city = $post['id_city'];

        $data['city'] = $this->MCity->getById($id_city);
        // $data['sales'] = $this->MVisit->getGroupedContact($id_user, $id_city, $month);
        $this->db->select("MAX(id_status_change) AS id_status_change, tb_status_change.id_contact, MAX(tb_status_change.created_at) AS created_at");
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_status_change.id_contact');
        $this->db->join('tb_city', 'tb_city.id_city = tb_contact.id_city');
        $this->db->group_by('tb_status_change.id_contact');
        $data['store'] = $this->db->get_where('tb_status_change', ['MONTH(tb_status_change.created_at) ' => $month, 'YEAR(tb_status_change.created_at) ' => $year, 'tb_contact.id_city' => $id_city])->result_array();
        // echo $this->db->last_query();
        // die;
        $data['month'] = $month;
        $data['year'] = $year;
        // PDF
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Status/Print', $data, true);
        $mpdf->AddPage('P');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }
}
