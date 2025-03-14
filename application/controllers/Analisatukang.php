<?php


class Analisatukang extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
    }

    public function user()
    {
        $data['title'] = 'Admin Lapangan / SPG untuk Tukang';

        $this->db->where("level_user IN ('penagihan', 'sales', 'marketing')", NULL, FALSE);
        $data['users'] = $this->db->get_where('tb_user', ['password !=' => '0'])->result_array();

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Analisatukang/User');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function tampil($id_user)
    {
        $save = $this->db->update('tb_user', ['is_add_tukang' => 1], ['id_user' => $id_user]);

        if ($save) {
            $this->session->set_flashdata('success', "Berhasil merubah data user!");
            redirect('analisatukang/user');
        } else {
            $this->session->set_flashdata('failed', "Gagal merubah data user!");
            redirect('analisatukang/user');
        }
    }

    public function matikan($id_user)
    {
        $save = $this->db->update('tb_user', ['is_add_tukang' => 0], ['id_user' => $id_user]);

        if ($save) {
            $this->session->set_flashdata('success', "Berhasil merubah data user!");
            redirect('analisatukang/user');
        } else {
            $this->session->set_flashdata('failed', "Gagal merubah data user!");
            redirect('analisatukang/user');
        }
    }
}
