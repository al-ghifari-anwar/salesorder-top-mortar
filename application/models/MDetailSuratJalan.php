<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MDetailSuratJalan extends CI_Model
{

    public $id_surat_jalan;
    public $id_produk;
    public $price;
    public $qty_produk;
    public $amount;
    public $is_bonus;

    public function getAll($id_surat_jalan)
    {
        $this->db->join('tb_produk', 'tb_produk.id_produk = tb_detail_surat_jalan.id_produk');
        $query = $this->db->get_where('tb_detail_surat_jalan', ['id_surat_jalan' => $id_surat_jalan])->result_array();
        return $query;
    }

    public function getById($id)
    {
        $query = $this->db->get_where('tb_detail_surat_jalan', ['id_detail_surat_jalan' => $id])->row_array();
        return $query;
    }

    public function getSoldItems($id_city = null)
    {
        $this->db->select("nama_produk, SUM(qty_produk) AS qty_produk, tb_produk.id_produk");
        $this->db->join('tb_produk', 'tb_produk.id_produk = tb_detail_surat_jalan.id_produk');
        $this->db->join('tb_surat_jalan', 'tb_surat_jalan.id_surat_jalan = tb_detail_surat_jalan.id_surat_jalan');
        $this->db->group_by('tb_produk.id_produk');
        $query = $this->db->get_where('tb_detail_surat_jalan', ['tb_surat_jalan.is_closing' => 1, 'tb_produk.id_city' => $id_city])->result_array();

        return $query;
    }

    public function getSoldItemsByDate($id_city = null, $dateFrom, $dateTo)
    {
        $this->db->select("nama_produk, SUM(qty_produk) AS qty_produk, tb_produk.id_produk");
        $this->db->join('tb_produk', 'tb_produk.id_produk = tb_detail_surat_jalan.id_produk');
        $this->db->join('tb_surat_jalan', 'tb_surat_jalan.id_surat_jalan = tb_detail_surat_jalan.id_surat_jalan');
        $this->db->group_by('tb_produk.id_produk');
        $query = $this->db->get_where('tb_detail_surat_jalan', ['tb_surat_jalan.is_closing' => 1, 'tb_produk.id_city' => $id_city, 'tb_surat_jalan.date_closing >= ' => $dateFrom, 'tb_surat_jalan.date_closing <= ' => $dateTo])->result_array();

        return $query;
    }

    public function setBonusItem($id_surat_jalan, $id_promo)
    {
        $promo = $this->db->get_where('tb_promo', ['id_promo' => $id_promo])->row_array();
        $this->db->select("tb_produk.id_produk, SUM(qty_produk) AS qty_produk, harga_produk ");
        $this->db->join('tb_produk', 'tb_produk.id_produk = tb_detail_surat_jalan.id_produk');
        $this->db->group_by("id_produk");
        $items = $this->db->get_where('tb_detail_surat_jalan', ['id_surat_jalan' => $id_surat_jalan])->result_array();

        foreach ($items as $item) {
            $multiplier = $item['qty_produk'] / $promo['kelipatan_promo'];

            if (floor($multiplier) > 0) {
                $this->id_surat_jalan = $id_surat_jalan;
                $this->id_produk = $item['id_produk'];
                $this->qty_produk = floor($multiplier) * $promo['bonus_promo'];
                $this->price = $item['harga_produk'];
                $this->amount = 0;
                $this->is_bonus = 1;

                $this->db->insert('tb_detail_surat_jalan', $this);
            }
        }
    }

    public function insert()
    {
        $post = $this->input->post();
        $this->id_surat_jalan = $post['id_surat_jalan'];
        $this->id_produk = $post['id_produk'];
        $this->qty_produk = $post['qty_produk'];
        $produk = $this->db->get_where('tb_produk', ['id_produk' => $post['id_produk']])->row_array();
        $this->price = $produk['harga_produk'];
        $retur = $post['is_retur'];
        if ($retur == false) {
            $this->amount = $produk['harga_produk'] * $post['qty_produk'];
            $this->is_bonus = 0;
        } else {
            $this->amount = 0;
            $this->is_bonus = 2;
        }

        $query = $this->db->insert('tb_detail_surat_jalan', $this);

        if ($query) {
            redirect('surat-jalan/' . $this->id_surat_jalan);
        } else {
            $this->session->set_flashdata('failed', "Gagal menyimpan data surat jalan!");
            redirect('surat-jalan');
        }
    }

    public function update($id)
    {
        $post = $this->input->post();
        $this->id_surat_jalan = $post['id_surat_jalan'];
        $this->id_produk = $post['id_produk'];
        $this->qty_produk = $post['qty_produk'];
        if ($post['is_bonus'] == true) {
            $this->is_bonus = 1;
        } else {
            $this->is_bonus = 0;
        }

        $query = $this->db->update('tb_detail_surat_jalan', $this, ['id_detail_surat_jalan' => $id]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function delete($id)
    {
        $query = $this->db->delete('tb_detail_surat_jalan', ['id_detail_surat_jalan' => $id]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }
}
