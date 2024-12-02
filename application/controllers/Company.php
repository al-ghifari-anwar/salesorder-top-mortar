<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Company extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $this->load->model('MCompany');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['title'] = 'Company';
        $data['company'] = $this->MCompany->getByIdDistributor($this->session->userdata('id_distributor'));
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Company/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function update($id)
    {
        $insert = $this->MCompany->update($id);

        if ($insert) {
            $this->session->set_flashdata('success', "Berhasil mengubah data perusahaan!");
            redirect('company');
        } else {
            $this->session->set_flashdata('failed', "Gagal mengubah data perusahaan!");
            redirect('company');
        }
    }
}
