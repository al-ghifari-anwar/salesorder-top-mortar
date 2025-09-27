<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Konten extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $this->load->model('MKonten');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['title'] = 'Konten';
        $data['menuGroup'] = 'TopSeller';
        $data['menu'] = 'Konten';
        $data['konten'] = $this->MKonten->getAll();

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Konten/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function insert()
    {
        $this->form_validation->set_rules('title_konten', 'Title Konten', 'required');
        $this->form_validation->set_rules('link_konten', 'Link Konten', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap lengkapi form");
            redirect('konten');
        } else {
            $insert = $this->MKonten->insert();

            if ($insert) {
                $this->session->set_flashdata('success', "Berhasil menyimpan data konten!");
                redirect('konten');
            } else {
                $this->session->set_flashdata('failed', "Gagal menyimpan data konten!");
                redirect('konten');
            }
        }
    }

    public function update($id)
    {
        $this->form_validation->set_rules('title_konten', 'Title Konten', 'required');
        $this->form_validation->set_rules('link_konten', 'Link Konten', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap lengkapi form");
            redirect('konten');
        } else {
            $insert = $this->MKonten->update($id);

            if ($insert) {
                $this->session->set_flashdata('success', "Berhasil mengubah data konten!");
                redirect('konten');
            } else {
                $this->session->set_flashdata('failed', "Gagal mengubah data konten!");
                redirect('konten');
            }
        }
    }

    public function delete($id)
    {
        $insert = $this->MKonten->delete($id);

        if ($insert) {
            $this->session->set_flashdata('success', "Berhasil menghapus data konten!");
            redirect('konten');
        } else {
            $this->session->set_flashdata('failed', "Gagal menghapus data konten!");
            redirect('konten');
        }
    }
}
