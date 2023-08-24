<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MUser extends CI_Model
{

    public $full_name;

    public function getAll($id_city)
    {
        $query = $this->db->get_where('tb_user', ['level_user' => 'courier', 'id_city' => $id_city])->result_array();
        return $query;
    }

    public function getAllDefault()
    {
        $this->db->join('tb_city', 'tb_city.id_city = tb_user.id_city');
        $query = $this->db->get_where('tb_user', ['level_user' => 'courier'])->result_array();
        return $query;
    }

    public function getById($id)
    {
        $query = $this->db->get_where('tb_city', ['id_city' => $id])->row_array();
        return $query;
    }
}
