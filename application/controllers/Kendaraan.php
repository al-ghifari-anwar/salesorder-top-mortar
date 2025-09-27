<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kendaraan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $this->load->model('MUser');
        $this->load->model('MKendaraan');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['title'] = 'Kendaraan';
        $data['menuGroup'] = 'Data';
        $data['menu'] = 'Kendaraan';
        $data['kurir'] = $this->MUser->getAllDefault();
        $data['kendaraan'] = $this->MKendaraan->getAll();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Kendaraan/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function insert()
    {
        $this->form_validation->set_rules('nama_kendaraan', 'Nama Kendaraan', 'required');
        $this->form_validation->set_rules('nopol_kendaraan', 'Nopol', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap lengkapi form");
            redirect('kendaraan');
        } else {
            $insert = $this->MKendaraan->insert();

            if ($insert) {
                $this->session->set_flashdata('success', "Berhasil menyimpan data kendaraan!");
                redirect('kendaraan');
            } else {
                $this->session->set_flashdata('failed', "Gagal menyimpan data kendaraan!");
                redirect('kendaraan');
            }
        }
    }

    public function update($id)
    {
        $this->form_validation->set_rules('nama_kendaraan', 'Nama Kendaraan', 'required');
        $this->form_validation->set_rules('nopol_kendaraan', 'Nopol', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap lengkapi form");
            redirect('kendaraan');
        } else {
            $insert = $this->MKendaraan->update($id);

            if ($insert) {
                $this->session->set_flashdata('success', "Berhasil mengubah data kendaraan!");
                redirect('kendaraan');
            } else {
                $this->session->set_flashdata('failed', "Gagal mengubah data kendaraan!");
                redirect('kendaraan');
            }
        }
    }

    public function delete($id)
    {
        $insert = $this->MKendaraan->delete($id);

        if ($insert) {
            $this->session->set_flashdata('success', "Berhasil menghapus data kendaraan!");
            redirect('kendaraan');
        } else {
            $this->session->set_flashdata('failed', "Gagal menghapus data kendaraan!");
            redirect('kendaraan');
        }
    }
}
