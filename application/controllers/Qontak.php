<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Qontak extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        // $this->load->model('MQontak');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['title'] = 'Qontak';
        $data['qontak'] = $this->db->get_where('tb_qontak', ['id_distributor' => $this->session->userdata('id_distributor')])->row_array();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Qontak/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function update($id)
    {
        $post = $this->input->post();
        $data = [
            'integration_id' => $post['integration_id'],
            'token' => $post['token']
        ];
        $insert = $this->db->update('tb_qontak', $data, ['id_distributor' => $id]);

        if ($insert) {
            $this->session->set_flashdata('success', "Berhasil mengubah data qontak!");
            redirect('qontak');
        } else {
            $this->session->set_flashdata('failed', "Gagal mengubah data qontak!");
            redirect('qontak');
        }
    }
}
