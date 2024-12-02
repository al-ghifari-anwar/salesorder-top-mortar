<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MCompany extends CI_Model
{
    public $nama_company;
    public $address_company;
    public $phone_company;
    public $norek_company;
    public $img_company;
    public $updated_at;

    public function get()
    {
        $query = $this->db->get_where('tb_company')->result_array();
        return $query;
    }

    public function getById($id)
    {
        $query = $this->db->get_where('tb_company', ['id_company' => $id])->row_array();
        return $query;
    }

    public function insert()
    {
        $post = $this->input->post();
        $this->nama_company = $post['nama_company'];
        $this->address_company = $post['address_company'];
        $this->phone_company = $post['phone_company'];
        $this->norek_company = $post['norek_company'];
        $this->updated_at = date("Y-m-d H:i:s");

        $query = $this->db->insert('tb_company', $this);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function update($id)
    {
        $post = $this->input->post();
        $this->nama_company = $post['nama_company'];
        $this->address_company = $post['address_company'];
        $this->phone_company = $post['phone_company'];
        $this->norek_company = $post['norek_company'];
        $this->updated_at = date("Y-m-d H:i:s");

        $query = $this->db->update('tb_company', $this, ['id_city' => $id]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function delete($id)
    {
        $query = $this->db->delete('tb_company', ['id_company' => $id]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }
}
