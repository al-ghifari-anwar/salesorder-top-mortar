<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MContact extends CI_Model
{

    public $nama;
    public $nomorhp;
    public $store_owner;
    public $id_city;
    public $maps_url;
    public $address;
    public $store_status;

    public function getAll($id_city)
    {
        $this->db->join('tb_city', 'tb_city.id_city = tb_contact.id_city');
        $query = $this->db->get_where('tb_contact', ['tb_contact.id_city' => $id_city])->result_array();
        return $query;
    }

    public function getAllDefault()
    {
        $this->db->join('tb_city', 'tb_city.id_city = tb_contact.id_city', 'LEFT');
        $query = $this->db->get('tb_contact')->result_array();
        return $query;
    }

    public function getById($id)
    {
        $query = $this->db->get_where('tb_contact', ['id_contact' => $id])->row_array();
        return $query;
    }

    public function insert()
    {
        $post = $this->input->post();
        $this->nama = $post['nama'];
        $this->nomorhp = $post['nomorhp'];
        $this->store_owner = $post['store_owner'];
        $this->id_city = $post['id_city'];
        $this->maps_url = $post['maps_url'];
        $this->address = $post['address'];
        $this->store_status = $post['store_status'];

        $query = $this->db->insert('tb_contact', $this);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function update($id)
    {
        $post = $this->input->post();
        $this->nama = $post['nama'];
        $this->nomorhp = $post['nomorhp'];
        $this->store_owner = $post['store_owner'];
        $this->id_city = $post['id_city'];
        $this->maps_url = $post['maps_url'];
        $this->address = $post['address'];
        $this->store_status = $post['store_status'];

        $query = $this->db->update('tb_contact', $this, ['id_contact' => $id]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }
}