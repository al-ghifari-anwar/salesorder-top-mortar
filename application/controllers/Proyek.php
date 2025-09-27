<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Proyek extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $this->load->model('MProyek');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['title'] = 'Proyek';
        $data['menuGroup'] = '';
        $data['menu'] = '';
        $data['proyeks'] = $this->MProyek->getAll();

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Proyek/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function insert()
    {
        $this->form_validation->set_rules('name_proyek', 'Nama Proyek', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap lengkapi form");
            redirect('proyek');
        } else {
            $insert = $this->MProyek->insert();

            if ($insert) {
                $this->session->set_flashdata('success', "Berhasil menyimpan data proyek!");
                redirect('proyek');
            } else {
                $this->session->set_flashdata('failed', "Gagal menyimpan data proyek!");
                redirect('proyek');
            }
        }
    }

    public function update($id)
    {
        $this->form_validation->set_rules('name_proyek', 'Nama Proyek', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap lengkapi form");
            redirect('proyek');
        } else {
            $insert = $this->MProyek->update($id);

            if ($insert) {
                $this->session->set_flashdata('success', "Berhasil mengubah data proyek!");
                redirect('proyek');
            } else {
                $this->session->set_flashdata('failed', "Gagal mengubah data proyek!");
                redirect('proyek');
            }
        }
    }

    public function delete($id)
    {
        $insert = $this->MProyek->delete($id);

        if ($insert) {
            $this->session->set_flashdata('success', "Berhasil menghapus data proyek!");
            redirect('proyek');
        } else {
            $this->session->set_flashdata('failed', "Gagal menghapus data proyek!");
            redirect('proyek');
        }
    }
}
