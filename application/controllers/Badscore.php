<?php

class Badscore extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('MCity');
        $this->load->model('MContact');
        $this->load->model('MInvoice');
        $this->load->model('MPayment');
        $this->load->model('MSuratJalan');
        $this->load->model('MDetailSuratJalan');
    }

    public function city_list()
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Toko Skor Jelek';
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Badscore/CityList');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function list($id_city)
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Toko Skor Jelek';
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_bad_score.id_contact');
        $data['contacts'] = $this->db->get_where('tb_bad_score', ['id_city' => $id_city])->result_array();

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Badscore/List');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function approve($id_contact)
    {
        $badscoreData = [
            'is_approved' => 1,
            'type_approval' => 'approved_by_' . $this->session->userdata('username'),
            'updated_at' => date("Y-m-d H:i:s"),
        ];

        $save = $this->db->update('tb_bad_score', $badscoreData, ['id_contact' => $id_contact]);

        if ($save) {
            $this->session->set_flashdata('success', "Berhasil approve!");
            redirect('badscore');
        } else {
            $this->session->set_flashdata('failed', "Gagal approve!");
            redirect('badscore');
        }
    }

    public function tampilkan($id_contact)
    {
        $badscoreData = [
            'is_approved' => 0,
            'type_approval' => 'pending_by_' . $this->session->userdata('username'),
            'updated_at' => date("Y-m-d H:i:s"),
        ];

        $save = $this->db->update('tb_bad_score', $badscoreData, ['id_contact' => $id_contact]);

        if ($save) {
            $this->session->set_flashdata('success', "Berhasil menampilkan toko!");
            redirect('badscore');
        } else {
            $this->session->set_flashdata('failed', "Gagal menampilkan toko!");
            redirect('badscore');
        }
    }
}
