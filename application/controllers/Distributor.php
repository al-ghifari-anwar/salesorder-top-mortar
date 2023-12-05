<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Distributor extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $this->load->model('MCity');
        $this->load->model('MDistributor');
        $this->load->model('MUser');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['title'] = 'Distributor';
        $data['dst'] = $this->MDistributor->getAll();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Distributor/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function insert()
    {
        $this->form_validation->set_rules('nama_distributor', 'Nama Distributor', 'required');
        $this->form_validation->set_rules('nomorhp_distributor', 'Nomor HP', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap lengkapi form");
            redirect('distributor');
        } else {
            $insert = $this->MDistributor->insert();

            if ($insert) {
                $this->session->set_flashdata('success', "Berhasil menyimpan data distributor!");
                redirect('distributor');
            } else {
                $this->session->set_flashdata('failed', "Gagal menyimpan data distributor!");
                redirect('distributor');
            }
        }
    }

    public function add_user()
    {
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Gagal menbuat akun admin distributor!");
            redirect('distributor');
        } else {
            $insert = $this->MUser->insert();

            if ($insert) {
                $this->session->set_flashdata('success', "Berhasil menambah akun admin!");
                redirect('distributor');
            } else {
                $this->session->set_flashdata('failed', "Gagal menambah akun admin!");
                redirect('distributor');
            }
        }
    }

    public function update($id)
    {
        $this->form_validation->set_rules('nama_distributor', 'Nama Distributor', 'required');
        $this->form_validation->set_rules('nomorhp_distributor', 'Nomor HP', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap lengkapi form");
            redirect('distributor');
        } else {
            $insert = $this->MDistributor->update($id);

            if ($insert) {
                $this->session->set_flashdata('success', "Berhasil mengubah data distributor!");
                redirect('distributor');
            } else {
                $this->session->set_flashdata('failed', "Gagal mengubah data distributor!");
                redirect('distributor');
            }
        }
    }

    public function delete($id)
    {
        $insert = $this->MDistributor->delete($id);

        if ($insert) {
            $this->session->set_flashdata('success', "Berhasil menghapus data distributor!");
            redirect('distributor');
        } else {
            $this->session->set_flashdata('failed', "Gagal menghapus data distributor!");
            redirect('distributor');
        }
    }
}
