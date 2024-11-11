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
        $query = $this->db->get_where('tb_contact', ['tb_contact.id_city' => $id_city, 'tb_contact.store_status !=' => 'blacklist', 'tb_contact.address !=' => '', 'tb_contact.maps_url !=' => '', 'tb_contact.termin_payment IS NOT NULL' => null])->result_array();
        return $query;
    }

    public function getAllNoFilter($id_city)
    {
        $this->db->join('tb_city', 'tb_city.id_city = tb_contact.id_city');
        $query = $this->db->get_where('tb_contact', ['tb_contact.id_city' => $id_city])->result_array();
        return $query;
    }

    public function getAllForPriority()
    {
        $this->db->join('tb_city', 'tb_city.id_city = tb_contact.id_city');
        $query = $this->db->get_where('tb_contact', ['tb_contact.store_status' => 'active', 'tb_contact.address !=' => '', 'tb_contact.maps_url !=' => '', 'tb_contact.termin_payment IS NOT NULL' => null, 'reputation' => 'good'])->result_array();
        return $query;
    }

    public function getAllPriority()
    {
        $this->db->join('tb_city', 'tb_city.id_city = tb_contact.id_city');
        $query = $this->db->get_where('tb_contact', ['is_priority' => 1, 'is_tokopromo' => 0])->result_array();
        return $query;
    }

    public function getAllTokopromo()
    {
        $this->db->join('tb_city', 'tb_city.id_city = tb_contact.id_city');
        $query = $this->db->get_where('tb_contact', ['is_priority' => 1, 'is_tokopromo' => 1])->result_array();
        return $query;
    }

    public function getAllTopSeller()
    {
        $this->db->join('tb_city', 'tb_city.id_city = tb_contact.id_city');
        $this->db->where('tb_city.id_distributor', $this->session->userdata('id_distributor'));
        $query = $this->db->get_where('tb_contact', ['tb_contact.pass_contact !=' => '0'])->result_array();
        return $query;
    }

    public function getAllTopSellerCity($id_city)
    {
        $this->db->join('tb_city', 'tb_city.id_city = tb_contact.id_city');
        $this->db->where('tb_city.id_distributor', $this->session->userdata('id_distributor'));
        $query = $this->db->get_where('tb_contact', ['tb_contact.pass_contact !=' => '0', 'tb_contact.id_city' => $id_city])->result_array();
        return $query;
    }

    public function getAllForRenvis($id_city)
    {
        $query = $this->db->query("SELECT * FROM tb_contact JOIN tb_city ON tb_city.id_city = tb_contact.id_city WHERE tb_contact.id_city = '$id_city' AND tb_contact.id_contact NOT IN (SELECT id_contact FROM tb_antrian_renvis)")->result_array();
        return $query;
    }

    public function getAllForVouchers($id_city)
    {
        $this->db->join('tb_city', 'tb_city.id_city = tb_contact.id_city');
        $query = $this->db->get_where('tb_contact', ['tb_contact.id_city' => $id_city, 'store_status !=' => 'active'])->result_array();
        return $query;
    }

    public function getAllDefault()
    {
        $this->db->join('tb_city', 'tb_city.id_city = tb_contact.id_city', 'LEFT');
        $query = $this->db->get_where('tb_contact', ['id_distributor' => $this->session->userdata('id_distributor')])->result_array();
        return $query;
    }

    public function getByCityStatus($id_city, $status)
    {
        $this->db->join('tb_city', 'tb_city.id_city = tb_contact.id_city');
        if ($id_city != 0) {
            $this->db->where('tb_city.id_city', $id_city);
        }
        $query = $this->db->get_where('tb_contact', ['store_status' => $status])->result_array();
        return $query;
    }

    public function getById($id)
    {
        $this->db->join('tb_city', 'tb_city.id_city = tb_contact.id_city');
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
