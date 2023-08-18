<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MCity extends CI_Model
{

    public $nama_city;
    public $kode_city;

    public function getAll()
    {
        $query = $this->db->get('tb_city')->result_array();
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

        $query = $this->db->update('tb_city', $this, ['id_city' => $id]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }
}
