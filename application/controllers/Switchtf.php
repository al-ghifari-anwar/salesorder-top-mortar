<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Switchtf extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        // $this->load->model('MSwitchtf');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['title'] = 'Switch Auto Transfer';
        $this->db->join('tb_distributor', 'tb_distributor.id_distributor = tb_switch_tf.id_distributor');
        $data['switchtf'] = $this->db->get('tb_switch_tf')->result_array();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Switchtf/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function update($id)
    {
        $this->form_validation->set_rules('nama_switchtf', 'Nama Kota', 'required');
        $this->form_validation->set_rules('kode_switchtf', 'Kode Kota', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap lengkapi form");
            redirect('switchtf');
        } else {
            $insert = $this->MSwitchtf->update($id);

            if ($insert) {
                $this->session->set_flashdata('success', "Berhasil mengubah data kota!");
                redirect('switchtf');
            } else {
                $this->session->set_flashdata('failed', "Gagal mengubah data kota!");
                redirect('switchtf');
            }
        }
    }
}
