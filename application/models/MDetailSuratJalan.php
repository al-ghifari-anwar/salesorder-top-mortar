<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MDetailSuratJalan extends CI_Model
{

    public $id_surat_jalan;
    public $id_produk;
    public $qty_produk;
    public $is_bonus;

    public function getAll($id_surat_jalan)
    {
        $this->db->join('tb_produk', 'tb_produk.id_produk = tb_detail_surat_jalan.id_produk');
        $query = $this->db->get_where('tb_detail_surat_jalan', ['id_surat_jalan' => $id_surat_jalan])->result_array();
        return $query;
    }

    public function getById($id)
    {
        $query = $this->db->get_where('tb_detail_surat_jalan', ['id_detail_surat_jalan' => $id])->row_array();
        return $query;
    }

    public function insert()
    {
        $post = $this->input->post();
        $this->id_surat_jalan = $post['id_surat_jalan'];
        $this->id_produk = $post['id_produk'];
        $this->qty_produk = $post['qty_produk'];
        if ($post['is_bonus'] == true) {
            $this->is_bonus = 1;
        } else {
            $this->is_bonus = 0;
        }

        $query = $this->db->insert('tb_detail_surat_jalan', $this);

        if ($query) {
            redirect('surat-jalan/' . $this->id_surat_jalan);
        } else {
            $this->session->set_flashdata('failed', "Gagal menyimpan data surat jalan!");
            redirect('surat-jalan');
        }
    }

    public function update($id)
    {
        $post = $this->input->post();
        $this->id_surat_jalan = $post['id_surat_jalan'];
        $this->id_produk = $post['id_produk'];
        $this->qty_produk = $post['qty_produk'];
        if ($post['is_bonus'] == true) {
            $this->is_bonus = 1;
        } else {
            $this->is_bonus = 0;
        }

        $query = $this->db->update('tb_detail_surat_jalan', $this, ['id_detail_surat_jalan' => $id]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function delete($id)
    {
        $query = $this->db->delete('tb_detail_surat_jalan', ['id_detail_surat_jalan' => $id]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }
}
