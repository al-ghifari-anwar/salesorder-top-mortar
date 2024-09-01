<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MProduk extends CI_Model
{

    public $nama_produk;
    public $id_city;
    public $harga_produk;

    public function getAll()
    {
        $query = $this->db->get('tb_produk')->result_array();
        return $query;
    }

    public function getByCity($id_city)
    {
        $query = $this->db->get_where('tb_produk', ['id_city' => $id_city])->result_array();
        return $query;
    }

    public function getById($id_produk)
    {
        $query = $this->db->get_where('tb_produk', ['id_produk' => $id_produk])->row_array();
        return $query;
    }

    public function insert()
    {
        $post = $this->input->post();
        $this->nama_produk = $post['nama_produk'];
        $this->id_city = $post['id_city'];
        $this->harga_produk = $post['harga_produk'];

        $query = $this->db->insert('tb_produk', $this);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function update($id)
    {
        $post = $this->input->post();
        $this->nama_produk = $post['nama_produk'];
        $this->id_city = $post['id_city'];
        $this->harga_produk = $post['harga_produk'];

        $query = $this->db->update('tb_produk', $this, ['id_produk' => $id]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }
}
