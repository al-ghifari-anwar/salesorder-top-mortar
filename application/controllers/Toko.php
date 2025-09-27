<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Toko extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $this->load->model('MContact');
        $this->load->library('form_validation');
        $this->load->model('MCity');
    }

    public function index()
    {
        $post = $this->input->post();
        $data['title'] = 'Toko';
        $data['menuGroup'] = 'Data';
        $data['menu'] = 'Toko';
        $data['cities'] = $this->MCity->getAll();

        if ($this->session->userdata('level_user') == 'admin_c') {
            $id_city = $this->session->userdata('id_city');
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $id_city])->result_array();
            if ($post) {
                $id_city = $post['id_city'];
                $status = $post['status'];
                $data['toko'] = $this->MContact->getByCityStatus($id_city, $status);
            } else {
                $data['toko'] = $this->MContact->getAll($id_city);
            }
        } else {
            $data['city'] = $this->MCity->getAll();
            if ($post) {
                $id_city = $post['id_city'];
                $status = $post['status'];
                $data['toko'] = $this->MContact->getByCityStatus($id_city, $status);
            } else {
                $data['toko'] = $this->MContact->getAllDefault();
            }
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Toko/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function insert()
    {
        $post = $this->input->post();
        $data = [
            'nama' => $post['nama'],
            'nomorhp' => $post['nomorhp'],
            'id_city' => $post['id_city'],
            'termin_payment' => $post['termin_payment'],
            'store_owner' => '',
            'tgl_lahir' => '0000-00-00',
            'maps_url' => '',
            'nomor_cat_1' => ''
        ];

        $insert = $this->db->insert('tb_contact', $data);

        if ($insert) {
            $this->session->set_flashdata('success', "Berhasil menyimpan data toko!");
            redirect('toko');
        } else {
            $this->session->set_flashdata('failed', "Gagal menyimpan data toko!");
            redirect('toko');
        }
    }
}
