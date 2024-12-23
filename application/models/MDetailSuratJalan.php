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
    public $no_voucher;

    public function getAll($id_surat_jalan)
    {
        $this->db->join('tb_produk', 'tb_produk.id_produk = tb_detail_surat_jalan.id_produk');
        $this->db->join('tb_satuan', 'tb_satuan.id_satuan = tb_produk.id_satuan');
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
        $this->db->join('tb_city', 'tb_city.id_city = tb_produk.id_city');
        if ($id_city != null) {
            $this->db->where('tb_city.id_city', $id_city);
        }
        if ($this->session->userdata('level_user') == 'admin_c') {
            $this->db->where('tb_city.id_city', $this->session->userdata('id_city'));
        }
        $this->db->group_by('tb_produk.id_produk');
        $query = $this->db->get_where('tb_detail_surat_jalan', ['tb_surat_jalan.is_closing' => 1, 'id_distributor' => $this->session->userdata('id_distributor')])->result_array();

        return $query;
    }

    public function getSoldItemsByDate($id_city = null, $dateFrom = null, $dateTo = null)
    {
        $this->db->select("nama_produk, SUM(qty_produk) AS qty_produk, tb_produk.id_produk");
        $this->db->join('tb_produk', 'tb_produk.id_produk = tb_detail_surat_jalan.id_produk');
        $this->db->join('tb_surat_jalan', 'tb_surat_jalan.id_surat_jalan = tb_detail_surat_jalan.id_surat_jalan');
        $this->db->join('tb_city', 'tb_city.id_city = tb_produk.id_city');
        if ($this->session->userdata('level_user') == 'admin_c') {
            $this->db->where('tb_city.id_city', $this->session->userdata('id_city'));
        }
        if ($id_city != null) {
            $this->db->where('tb_city.id_city', $id_city);
        }
        $this->db->group_by('tb_produk.id_produk');
        $query = $this->db->get_where('tb_detail_surat_jalan', ['tb_surat_jalan.is_closing' => 1, 'tb_surat_jalan.date_closing >= ' => $dateFrom, 'tb_surat_jalan.date_closing <= ' => $dateTo, 'id_distributor' => $this->session->userdata('id_distributor')])->result_array();

        return $query;
    }

    public function getSoldItemsGlobal($id_city = null)
    {
        $this->db->select("nama_produk, SUM(qty_produk) AS qty_produk, tb_produk.id_produk");
        $this->db->join('tb_produk', 'tb_produk.id_produk = tb_detail_surat_jalan.id_produk');
        $this->db->join('tb_surat_jalan', 'tb_surat_jalan.id_surat_jalan = tb_detail_surat_jalan.id_surat_jalan');
        $this->db->join('tb_city', 'tb_city.id_city = tb_produk.id_city');
        if ($id_city != null) {
            $this->db->where('tb_city.id_city', $id_city);
        }
        if ($this->session->userdata('level_user') == 'admin_c') {
            $this->db->where('tb_city.id_city', $this->session->userdata('id_city'));
        }
        $this->db->group_by('tb_produk.nama_produk');
        $query = $this->db->get_where('tb_detail_surat_jalan', ['tb_surat_jalan.is_closing' => 1, 'id_distributor' => $this->session->userdata('id_distributor')])->result_array();

        return $query;
    }

    public function getSoldItemsByDateGlobal($id_city = null, $dateFrom = null, $dateTo = null)
    {
        $this->db->select("nama_produk, SUM(qty_produk) AS qty_produk, tb_produk.id_produk");
        $this->db->join('tb_produk', 'tb_produk.id_produk = tb_detail_surat_jalan.id_produk');
        $this->db->join('tb_surat_jalan', 'tb_surat_jalan.id_surat_jalan = tb_detail_surat_jalan.id_surat_jalan');
        $this->db->join('tb_city', 'tb_city.id_city = tb_produk.id_city');
        if ($this->session->userdata('level_user') == 'admin_c') {
            $this->db->where('tb_city.id_city', $this->session->userdata('id_city'));
        }
        if ($id_city != null) {
            $this->db->where('tb_city.id_city', $id_city);
        }
        $this->db->group_by('tb_produk.nama_produk');
        $query = $this->db->get_where('tb_detail_surat_jalan', ['tb_surat_jalan.is_closing' => 1, 'tb_surat_jalan.date_closing >= ' => $dateFrom, 'tb_surat_jalan.date_closing <= ' => $dateTo, 'id_distributor' => $this->session->userdata('id_distributor')])->result_array();

        return $query;
    }

    public function setBonusItem($id_surat_jalan, $id_promo)
    {
        if ($id_promo != 0) {
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
    }

    public function insert()
    {
        $post = $this->input->post();
        $this->id_surat_jalan = $post['id_surat_jalan'];
        $this->id_produk = $post['id_produk'];
        $this->qty_produk = $post['qty_produk'];
        $produk = $this->db->get_where('tb_produk', ['id_produk' => $post['id_produk']])->row_array();
        $this->price = $post['harga_produk'];
        $retur = $post['is_retur'];
        $is_voucher = $post['is_voucher'];

        if ($this->price == 0) {
            $this->session->set_flashdata('failed', "Terjadi kesalahan, harap input ulang produk");
            redirect('surat-jalan/' . $post['id_surat_jalan']);
        }

        if ($produk['harga_produk'] ==  0) {
            $this->session->set_flashdata('failed', "Terjadi kesalahan, harap input ulang produk");
            redirect('surat-jalan/' . $post['id_surat_jalan']);
        }

        if ($retur == false) {
            $this->amount = $post['harga_produk'] * $post['qty_produk'];
            $this->is_bonus = 0;
        } else {
            $this->amount = 0;
            $this->is_bonus = 2;
        }

        if ($is_voucher == true) {
            $jmlVoucher = $post['jml_voucher'];
            if ($this->qty_produk > $jmlVoucher) {
                $this->session->set_flashdata('failed', "Jumlah item tidak sesuai dengan jumlah voucher!");
                redirect('surat-jalan/' . $this->id_surat_jalan);
            } else {
                $this->amount = 0;
                $this->no_voucher = $post['no_vouchers'];
                $this->is_bonus = 1;
                $id_surat_jalan = $this->id_surat_jalan;
                $no_voucher = $this->no_voucher;

                $cekProdukVc = $this->db->query("SELECT * FROM tb_detail_surat_jalan WHERE id_surat_jalan = '$id_surat_jalan' AND no_voucher IN ('" . $no_voucher . "')")->result_array();

                // echo json_encode($cekProdukVc);
                // die;
                if ($cekProdukVc != null) {
                    $this->session->set_flashdata('failed', "Produk dengan voucher telah ditambahkan, tidak bisa menambahkan lagi!");
                    redirect('surat-jalan/' . $this->id_surat_jalan);
                }
            }
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
