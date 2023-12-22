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
        $data['city'] = $this->MCity->getAll();
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
        $id_city = $post['id_city'];

        $data['city'] = $this->MCity->getById($id_city);
        // $data['sales'] = $this->MVisit->getGroupedContact($id_user, $id_city, $month);
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_status_change.id_contact');
        $this->db->join('tb_city', 'tb_city.id_city = tb_contact.id_city');
        $this->db->group_by('tb_status_change.id_contact');
        $data['store'] = $this->db->get_where('tb_status_change', ['MONTH(tb_status_change.created_at) ' => $month, 'tb_contact.id_city' => $id_city])->result_array();
        $data['month'] = $month;
        // PDF
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Status/Print', $data, true);
        $mpdf->AddPage('P');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }
}
