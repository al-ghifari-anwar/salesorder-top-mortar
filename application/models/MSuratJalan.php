<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MSuratJalan extends CI_Model
{

    public $no_surat_jalan;
    public $id_contact;
    public $order_number;

    public function getAll()
    {
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_surat_jalan.id_contact');
        $this->db->join('tb_city', 'tb_city.id_city = tb_contact.id_city');
        $query = $this->db->get('tb_surat_jalan')->result_array();
        return $query;
    }

    public function getById($id)
    {
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_surat_jalan.id_contact');
        $query = $this->db->get_where('tb_surat_jalan', ['id_surat_jalan' => $id])->row_array();
        return $query;
    }

    public function insert()
    {
        $post = $this->input->post();
        $this->no_surat_jalan = $post['no_surat_jalan'];
        $this->id_contact = $post['id_contact'];
        $this->order_number = $post['order_number'];

        $query = $this->db->insert('tb_surat_jalan', $this);

        if ($query) {
            redirect('surat-jalan/' . $this->db->insert_id());
        } else {
            $this->session->set_flashdata('failed', "Gagal menyimpan data surat jalan!");
            redirect('surat-jalan');
        }
    }

    public function update($id)
    {
        $post = $this->input->post();
        $this->no_surat_jalan = $post['no_surat_jalan'];
        $this->id_contact = $post['id_contact'];
        $this->order_number = $post['order_number'];

        $query = $this->db->update('tb_surat_jalan', $this, ['id_surat_jalan' => $id]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }
}
