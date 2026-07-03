<?php

class Transittoko extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MCity');
        $this->load->model('MContact');
    }

    public function index()
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Transit Toko X';
        $data['menuGroup'] = 'Analisa';
        $data['menu'] = 'TransitToko';
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Transittoko/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function list($id_city)
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Transit Toko X';
        $data['menuGroup'] = 'Analisa';
        $data['menu'] = 'TransitToko';

        $data['city'] = $this->MCity->getById($id_city);
        $data['transits'] = $this->db->get_where('tb_transit_toko', ['id_city_from' => $id_city])->result_array();

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Transittoko/List');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function approve($id_transit_toko)
    {
        $transit = $this->db->get_where('tb_transit_toko', ['id_transit_toko' => $id_transit_toko])->row_array();

        $id_city = $transit['id_city_from'];
        $id_contact = $transit['id_contact'];

        $contactData = [
            'id_city' => $transit['id_city_to'],
            'id_city_old' => $transit['id_city_from'],
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $save = $this->db->update('tb_contact', $contactData, ['id_contact' => $id_contact]);

        if ($save) {
            $this->db->delete('tb_transit_toko', ['id_transit_toko' => $id_transit_toko]);

            $this->session->set_flashdata('success', "Berhasil masukkan toko ke kota X");
            redirect('transittoko/' . $id_city);
        } else {
            $this->session->set_flashdata('failed', "Gagal, harap coba lagi");
            redirect('transittoko/' . $id_city);
        }
    }

    public function kembalikan($id_transit_toko)
    {
        $transit = $this->db->get_where('tb_transit_toko', ['id_transit_toko' => $id_transit_toko])->row_array();

        $id_city = $transit['id_city_from'];
        $id_contact = $transit['id_contact'];

        $cutoffVisitData = [
            'id_city' => $transit['id_city_from'],
            'id_contact' => $id_contact,
            'date_cutoff_visit' => date('Y-m-d'),
        ];

        $save = $this->db->insert('tb_cutoff_visit', $cutoffVisitData);

        if ($save) {
            $this->db->delete('tb_transit_toko', ['id_transit_toko' => $id_transit_toko]);

            $this->session->set_flashdata('success', "Berhasil kembalikan toko ke Kota Asal");
            redirect('transittoko/' . $id_city);
        } else {
            $this->session->set_flashdata('failed', "Gagal, harap coba lagi");
            redirect('transittoko/' . $id_city);
        }
    }
}
