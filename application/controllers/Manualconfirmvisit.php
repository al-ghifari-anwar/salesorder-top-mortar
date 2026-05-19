<?php

class Manualconfirmvisit extends CI_Controller
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
        $this->load->model('MRenvi');
        $this->load->model('MInvoice');
        $this->load->model('HTelegram');
        $this->load->library('form_validation');
    }

    public function index()
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Manual Confirm Visit';
        $data['menuGroup'] = 'Visit';
        $data['menu'] = 'ManualConfirmVisit';
        if ($this->session->userdata('level_user') == 'admin_c' || $this->session->userdata('level_user') == 'sales') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else if ($this->session->userdata('level_user') == 'salesspv') {
            $userCity = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->row_array();
            $nama_city = trim(preg_replace("/\\d+/", "", $userCity['nama_city']));
            $data['city'] = $this->db->like('nama_city', $nama_city)->get_where('tb_city', ['id_distributor' => $this->session->userdata('id_distributor')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Manualconfirmvisit/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function list($id_city)
    {
        $date = date('Y-m-d');
        if (isset($_GET['date'])) {
            $date = date('Y-m-d', strtotime($_GET['date']));
        }

        $data['title'] = 'Manual Confirm Visit';
        $data['menuGroup'] = 'Visit';
        $data['menu'] = 'ManualConfirmVisit';

        $data['city'] = $this->MCity->getById($id_city);
        $data['jadwalvisits'] = $this->db->join('tb_contact', 'tb_contact.id_contact = tb_jadwal_visit.id_contact')->where('date_jadwal_visit', $date)->where('tb_jadwal_visit.id_city', $id_city)->get('tb_jadwal_visit')->result_array();

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Manualconfirmvisit/List');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function confirm($id_jadwal_visit)
    {
        $jadwalVisit = $this->db->get_where('tb_jadwal_visit', ['id_jadwal_visit' => $id_jadwal_visit])->row_array();

        $save = $this->db->update('tb_jadwal_visit', ['is_yes' => 1], ['id_jadwal_visit' => $id_jadwal_visit]);

        if ($save) {
            $this->session->set_flashdata('success', 'Berhasil konfirm visit');
            redirect('manualconfirmvisit/' . $jadwalVisit['id_city']);
        } else {
            $this->session->set_flashdata('failed', 'Gagal konfirm visit');
            redirect('manualconfirmvisit/' . $jadwalVisit['id_city']);
        }
    }
}
