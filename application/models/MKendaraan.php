<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MKendaraan extends CI_Model
{

    // public $id_courier;
    public $nama_kendaraan;
    public $nopol_kendaraan;
    public $id_distributor;

    public function getAll()
    {
        $query = $this->db->get_where('tb_kendaraan', ['id_distributor' => $this->session->userdata('id_distributor')])->result_array();
        return $query;
    }

    public function getById($id)
    {
        $query = $this->db->get_where('tb_kendaraan', ['id_kendaraan' => $id])->row_array();
        return $query;
    }

    public function insert()
    {
        $post = $this->input->post();
        // $this->id_courier = $post['id_courier'];
        $this->nama_kendaraan = $post['nama_kendaraan'];
        $this->nopol_kendaraan = $post['nopol_kendaraan'];
        $this->id_distributor = $this->session->userdata('id_distributor');

        $query = $this->db->insert('tb_kendaraan', $this);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function update($id)
    {
        $post = $this->input->post();
        // $this->id_courier = $post['id_courier'];
        $this->nama_kendaraan = $post['nama_kendaraan'];
        $this->nopol_kendaraan = $post['nopol_kendaraan'];
        $this->id_distributor = $this->session->userdata('id_distributor');

        $query = $this->db->update('tb_kendaraan', $this, ['id_kendaraan' => $id]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function delete($id)
    {
        $query = $this->db->delete('tb_kendaraan', ['id_kendaraan' => $id]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }
}
