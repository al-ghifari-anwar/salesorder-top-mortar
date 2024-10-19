<?php

class VisitQuestion extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MVisitQuestion');

        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
    }

    public function index()
    {
        $data['title'] = 'Checklist Visit';
        $data['menuGroup'] = 'sales';
        $data['menu'] = 'ckecklist-visit';
        $data['questions'] = $this->MVisitQuestion->get();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('VisitQuestion/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function create()
    {
        $query = $this->MVisitQuestion->create();

        if ($query) {
            $this->session->set_flashdata('success', 'Berhasil menambah data pertanyaan');
            redirect('checklist-visit');
        } else {
            $this->session->set_flashdata('failed', 'Gagal menambah data pertanyaan');
            redirect('checklist-visit');
        }
    }

    public function update($id)
    {
        $query = $this->MVisitQuestion->update($id);

        if ($query) {
            $this->session->set_flashdata('success', 'Berhasil update data pertanyaan');
            redirect('checklist-visit');
        } else {
            $this->session->set_flashdata('failed', 'Gagal update data pertanyaan');
            redirect('checklist-visit');
        }
    }

    public function destroy($id)
    {
        $query = $this->MVisitQuestion->destroy($id);

        if ($query) {
            $this->session->set_flashdata('success', 'Berhasil menghapus data pertanyaan');
            redirect('checklist-visit');
        } else {
            $this->session->set_flashdata('failed', 'Gagal menghapus data pertanyaan');
            redirect('checklist-visit');
        }
    }
}
