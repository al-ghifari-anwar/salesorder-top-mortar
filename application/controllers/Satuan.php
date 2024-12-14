<?php

class Satuan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MSatuan');
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
    }

    public function index()
    {
        $data['title'] = 'Satuan';
        $data['menuGroup'] = 'master';
        $data['menu'] = 'satuan';
        $data['satuans'] = $this->MSatuan->get();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Satuan/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function create()
    {
        $query = $this->MSatuan->create();

        if ($query) {
            $this->session->set_flashdata('success', 'Berhasil menambah data satuan');
            redirect('satuan');
        } else {
            $this->session->set_flashdata('failed', 'Gagal menambah data satuan');
            redirect('satuan');
        }
    }

    public function update($id)
    {
        $query = $this->MSatuan->update($id);

        if ($query) {
            $this->session->set_flashdata('success', 'Berhasil update data satuan');
            redirect('satuan');
        } else {
            $this->session->set_flashdata('failed', 'Gagal update data satuan');
            redirect('satuan');
        }
    }

    public function destroy($id)
    {
        $query = $this->MSatuan->destroy($id);

        if ($query) {
            $this->session->set_flashdata('success', 'Berhasil menghapus data satuan');
            redirect('satuan');
        } else {
            $this->session->set_flashdata('failed', 'Gagal menghapus data satuan');
            redirect('satuan');
        }
    }
}
