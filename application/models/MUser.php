<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MUser extends CI_Model
{

    public $username;
    public $full_name;
    public $password;
    public $level_user;
    public $id_city;
    public $phone_user;
    public $id_distributor;

    public function getAll($id_city)
    {
        $query = $this->db->get_where('tb_user', ['level_user' => 'courier', 'id_city' => $id_city])->result_array();
        return $query;
    }

    public function getCourierByIdCity($id_city)
    {
        $query = $this->db->get_where('tb_user', ['id_city' => $id_city, 'level_user' => 'courier', 'password !=' => '0'])->row_array();

        return $query;
    }

    public function getCourierByCityGroup($nama_city)
    {
        $this->db->join('tb_city', 'tb_city.id_city = tb_user.id_city');
        $this->db->like('tb_city.nama_city', $nama_city);
        $query = $this->db->get_where('tb_user', ['level_user' => 'courier', 'password !=' => '0'])->row_array();

        return $query;
    }

    public function getByIdDist($id_distributor)
    {
        if ($this->session->userdata('level_user') == 'admin_c') {
            $this->db->where('tb_user.id_city', $this->session->userdata('id_city'));
        }
        $query = $this->db->get_where('tb_user', ['id_distributor' => $id_distributor, 'password !=' => '0', 'is_absen' => 1])->result_array();
        return $query;
    }

    public function getByDissindo()
    {
        $this->db->where_in('tb_user.id_city', [32, 14]);
        $this->db->where('tb_user.level_user', 'courier');
        $query = $this->db->get_where('tb_user', ['password !=' => '0', 'is_absen' => 1])->result_array();
        return $query;
    }

    public function getAllForManualRenvi($id_city)
    {
        $query = $this->db->get_where('tb_user', ['id_city' => $id_city])->result_array();
        return $query;
    }

    public function getAllDefault()
    {
        $this->db->join('tb_city', 'tb_city.id_city = tb_user.id_city');
        $query = $this->db->get_where('tb_user', ['level_user' => 'courier', 'tb_city.id_distributor' => $this->session->userdata('id_distributor')])->result_array();
        return $query;
    }

    public function getById($id)
    {
        $query = $this->db->get_where('tb_user', ['id_user' => $id])->row_array();
        return $query;
    }

    public function insert()
    {
        $post = $this->input->post();

        if (strlen($post['password']) < 8) {
            $this->session->set_flashdata('failed', "Password minimal 8 karakter!");
            redirect('distributor');
        }
        $this->username = $post['username'];
        $this->full_name = $post['full_name'];
        $this->password = md5($post['password']);
        $this->level_user = 'admin';
        $this->id_city = 0;
        $this->phone_user = $post['phone_user'];
        $this->id_distributor = $post['id_distributor'];

        $query = $this->db->insert('tb_user', $this);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }
}
