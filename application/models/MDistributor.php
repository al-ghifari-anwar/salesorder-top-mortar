<?php
defined('BASEPATH') or exit('No direct script allowed');

class MDistributor extends CI_Model
{
    public $nama_distributor;
    public $nomorhp_distributor;
    public $alamat_distributor;
    public $jenis_distributor;

    public function getAll()
    {
        $query = $this->db->get('tb_distributor')->result_array();

        return $query;
    }

    public function getById($id)
    {
        $query = $this->db->get_where('tb_distributor', ['id_distributor' => $id])->row_array();

        return $query;
    }

    public function insert()
    {
        $post = $this->input->post();

        $this->nama_distributor = $post['nama_distributor'];
        $this->nomorhp_distributor = $post['nomorhp_distributor'];
        $this->alamat_distributor = $post['alamat_distributor'];
        $this->jenis_distributor = $post['jenis_distributor'];

        $query = $this->db->insert('tb_distributor', $this);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function update($id)
    {
        $post = $this->input->post();

        $this->nama_distributor = $post['nama_distributor'];
        $this->nomorhp_distributor = $post['nomorhp_distributor'];
        $this->alamat_distributor = $post['alamat_distributor'];
        $this->jenis_distributor = $post['jenis_distributor'];

        $query = $this->db->update('tb_distributor', $this, ['id_distributor' => $id]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function delete($id)
    {
        $query = $this->db->delete('tb_distributor', ['id_distributor' => $id]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }
}
