<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Produk extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $this->load->model('MCity');
        $this->load->model('MProduk');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['title'] = 'Produk';
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Produk/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function produk_by_city($id_city)
    {
        $data['title'] = 'Produk';
        $data['cities'] = $this->MCity->getAll();
        $data['city'] = $this->MCity->getById($id_city);
        $data['produk'] = $this->MProduk->getByCity($id_city);
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Produk/List');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function insert()
    {
        $this->form_validation->set_rules('nama_produk', 'Nama Produk', 'required');
        $this->form_validation->set_rules('harga_produk', 'Harga Produk', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap lengkapi form");
            redirect('produk');
        } else {
            $insert = $this->MProduk->insert();

            if ($insert) {
                $this->session->set_flashdata('success', "Berhasil menyimpan data produk!");
                redirect('produk');
            } else {
                $this->session->set_flashdata('failed', "Gagal menyimpan data produk!");
                redirect('produk');
            }
        }
    }

    public function update($id)
    {
        $this->form_validation->set_rules('nama_produk', 'Nama Produk', 'required');
        $this->form_validation->set_rules('harga_produk', 'Harga Produk', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap lengkapi form");
            redirect('produk');
        } else {
            $insert = $this->MProduk->update($id);

            if ($insert) {
                $this->session->set_flashdata('success', "Berhasil mengubah data produk!");
                redirect('produk');
            } else {
                $this->session->set_flashdata('failed', "Gagal mengubah data produk!");
                redirect('produk');
            }
        }
    }
}
