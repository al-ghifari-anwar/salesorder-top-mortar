<?php
defined('BASEPATH') or exit('No direct script access allowed');

class City extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $this->load->model('MCity');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['title'] = 'Kota';
        $data['menuGroup'] = 'Data';
        $data['menu'] = 'Kota';
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $data['gudangs'] = $this->db->get_where('tb_gudang_stok', ['id_distributor' => $this->session->userdata('id_distributor')])->result_array();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('City/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function insert()
    {
        $this->form_validation->set_rules('nama_city', 'Nama Kota', 'required');
        $this->form_validation->set_rules('kode_city', 'Kode Kota', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap lengkapi form");
            redirect('city');
        } else {
            $insert = $this->MCity->insert();

            if ($insert) {
                $this->session->set_flashdata('success', "Berhasil menyimpan data kota!");
                redirect('city');
            } else {
                $this->session->set_flashdata('failed', "Gagal menyimpan data kota!");
                redirect('city');
            }
        }
    }

    public function update($id)
    {
        $this->form_validation->set_rules('nama_city', 'Nama Kota', 'required');
        $this->form_validation->set_rules('kode_city', 'Kode Kota', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap lengkapi form");
            redirect('city');
        } else {
            $insert = $this->MCity->update($id);

            if ($insert) {
                $this->session->set_flashdata('success', "Berhasil mengubah data kota!");
                redirect('city');
            } else {
                $this->session->set_flashdata('failed', "Gagal mengubah data kota!");
                redirect('city');
            }
        }
    }

    public function delete($id)
    {
        $insert = $this->MCity->delete($id);

        if ($insert) {
            $this->session->set_flashdata('success', "Berhasil menghapus data kota!");
            redirect('city');
        } else {
            $this->session->set_flashdata('failed', "Gagal menghapus data kota!");
            redirect('city');
        }
    }
}
