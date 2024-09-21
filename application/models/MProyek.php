<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MProyek extends CI_Model
{

    public $name_proyek;
    public $alamat_proyek;
    public $updated_at;

    public function getAll()
    {
        $query = $this->db->get_where('tb_proyek', ['is_deleted' => 0])->result_array();
        return $query;
    }

    public function getById($id)
    {
        $query = $this->db->get_where('tb_proyek', ['id_proyek' => $id])->row_array();
        return $query;
    }

    public function insert()
    {
        $post = $this->input->post();
        $this->name_proyek = $post['name_proyek'];
        $this->alamat_proyek = $post['alamat_proyek'];
        $this->updated_at = date("Y-m-d H:i:s");

        $query = $this->db->insert('tb_proyek', $this);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function update($id)
    {
        $post = $this->input->post();
        $this->name_proyek = $post['name_proyek'];
        $this->alamat_proyek = $post['alamat_proyek'];
        $this->updated_at = date("Y-m-d H:i:s");

        $query = $this->db->update('tb_proyek', $this, ['id_proyek' => $id]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function delete($id)
    {
        $query = $this->db->update('tb_proyek', ['is_deleted' => 1], ['id_proyek' => $id]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }
}
