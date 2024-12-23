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
        $post = $this->input->post();

        $status_switch_tf = $post['status_switch_tf'] == true ? 1 : 0;

        $data = [
            'status_switch_tf' => $status_switch_tf
        ];

        $insert = $this->db->update('tb_switch_tf', $data, ['id_switch_tf' => $id]);

        if ($insert) {
            $this->session->set_flashdata('success', "Berhasil mengubah status auto transfer!");
            redirect('switchtf');
        } else {
            $this->session->set_flashdata('failed', "Gagal mengubah status auto transfer!");
            redirect('switchtf');
        }
    }
}
