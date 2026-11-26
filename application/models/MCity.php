<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MCity extends CI_Model
{

    public $nama_city;
    public $kode_city;
    public $id_distributor;
    public $id_gudang_stok;

    public function getAll()
    {
        $query = $this->db->get_where('tb_city', ['id_distributor' => $this->session->userdata('id_distributor')])->result_array();
        return $query;
    }

    public function getAllNoLogin()
    {
        $this->db->join('tb_distributor', 'tb_distributor.id_distributor = tb_city.id_distributor');
        $query = $this->db->get_where('tb_city', ['jenis_distributor !=' => 'dist'])->result_array();
        return $query;
    }

    public function getAllGlobal()
    {
        $this->db->join('tb_distributor', 'tb_distributor.id_distributor = tb_city.id_distributor');
        $query = $this->db->get_where('tb_city', ['jenis_distributor !=' => 'dist'])->result_array();
        return $query;
    }



    public function getById($id)
    {
        $query = $this->db->get_where('tb_city', ['id_city' => $id])->row_array();
        return $query;
    }

    public function insert()
    {
        $post = $this->input->post();
        $this->nama_city = $post['nama_city'];
        $this->kode_city = $post['kode_city'];
        $this->id_distributor = $this->session->userdata('id_distributor');
        $this->id_gudang_stok = $post['id_gudang_stok'];

        $query = $this->db->insert('tb_city', $this);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function update($id)
    {
        $post = $this->input->post();
        $this->nama_city = $post['nama_city'];
        $this->kode_city = $post['kode_city'];
        $this->id_distributor = $this->session->userdata('id_distributor');
        $this->id_gudang_stok = $post['id_gudang_stok'];

        $query = $this->db->update('tb_city', $this, ['id_city' => $id]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function delete($id)
    {
        $query = $this->db->delete('tb_city', ['id_city' => $id]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }
}
