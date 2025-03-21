<?php

class Scoring extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $this->load->model('MCity');
        $this->load->model('MContact');
        $this->load->model('MInvoice');
        $this->load->model('MPayment');
    }

    public function city_list()
    {
        $data['title'] = 'Scoring Toko';
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Scroring/CityList');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function list($id_city)
    {
        $post = $this->input->post();

        $city = $this->MCity->getById($id_city);

        if (!$post) {
            $data['title'] = 'Scoring Toko - Kota ' . $city['nama_city'];
            $data['city'] = $city;
            $data['contacts'] = $this->db->get_where('tb_contact', ['id_city' => $id_city])->result_array();
            $data['selected_contact'] = ['id_contact' => 0];
            $data['is_score'] = 0;
        } else {
            $id_contact = $post['id_contact'];
            $selected_contact = $this->db->get_where('tb_contact', ['id_contact' => $id_contact])->row_array();

            $data['title'] = 'Scoring Toko ' . $selected_contact['nama'];
            $data['city'] = $city;
            $data['contacts'] = $this->db->get_where('tb_contact', ['id_city' => $id_city])->result_array();
            $data['selected_contact'] = $selected_contact;
            $data['is_score'] = 1;
        }

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Scroring/List');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }
}
