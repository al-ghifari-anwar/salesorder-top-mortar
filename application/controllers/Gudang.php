<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gudang extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['title'] = 'Gudang';
        $data['gudangs'] = $this->db->get_where('tb_gudang_stok', ['id_distributor' => $this->session->userdata('id_distributor')])->result_array();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Gudang/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function create()
    {
        $this->form_validation->set_rules('name_gudang_stok', 'Nama', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap lengkapi form");
            redirect('gudang');
        } else {
            $post = $this->input->post();
            $dataGudang = [
                'name_gudang_stok' => $post['name_gudang_stok'],
                'id_distributor' => $this->session->userdata('id_distributor'),
                'updated_at' => date("Y-m-d H:i:s")
            ];

            $insert = $this->db->insert('tb_gudang_stok', $dataGudang);

            if ($insert) {
                $this->session->set_flashdata('success', "Berhasil menyimpan data gudang!");
                redirect('gudang');
            } else {
                $this->session->set_flashdata('failed', "Gagal menyimpan data gudang!");
                redirect('gudang');
            }
        }
    }

    public function update($id)
    {
        $this->form_validation->set_rules('name_gudang_stok', 'Nama', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap lengkapi form");
            redirect('gudang');
        } else {
            $post = $this->input->post();
            $dataGudang = [
                'name_gudang_stok' => $post['name_gudang_stok'],
                'id_distributor' => $this->session->userdata('id_distributor'),
                'updated_at' => date("Y-m-d H:i:s")
            ];

            $insert = $this->db->update('tb_gudang_stok', $dataGudang, ['id_gudang_stok' => $id]);

            if ($insert) {
                $this->session->set_flashdata('success', "Berhasil mengubah data gudang!");
                redirect('gudang');
            } else {
                $this->session->set_flashdata('failed', "Gagal mengubah data gudang!");
                redirect('gudang');
            }
        }
    }

    public function delete($id)
    {
        $insert = $this->db->delete('tb_gudang_stok', ['id_gudang_stok' => $id]);

        if ($insert) {
            $this->session->set_flashdata('success', "Berhasil menghapus data gudang!");
            redirect('gudang');
        } else {
            $this->session->set_flashdata('failed', "Gagal menghapus data gudang!");
            redirect('gudang');
        }
    }
}
