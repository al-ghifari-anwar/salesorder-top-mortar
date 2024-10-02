<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MVoucherTukang extends CI_Model
{
    public $id_tukang;
    public $id_contact;
    public $no_seri;
    public $updated_at;
    public $exp_at;
    public $id_md5;
    public $type_voucher;

    public function getForPenukaran($dateFrom, $dateTo)
    {
        $this->db->join('tb_tukang', 'tb_tukang.id_tukang = tb_voucher_tukang.id_tukang');
        $this->db->order_by('tb_voucher_tukang.claim_date', 'DESC');
        $result = $this->db->get_where('tb_voucher_tukang', ['is_claimed' => 1, 'DATE(claim_date) >=' => $dateFrom,  'DATE(claim_date) <=' => $dateTo])->result_array();

        return $result;
    }

    public function create($id_tukang, $no_seri)
    {
        $this->id_tukang = $id_tukang;
        $this->id_contact = 0;
        $this->no_seri = $no_seri;
        $this->updated_at = date("Y-m-d H:i:s");
        $this->exp_at = date("Y-m-d", strtotime("+1 week"));
        $this->id_md5 = md5("Top" . md5($id_tukang));
        $this->type_voucher = 'voucher';

        $query = $this->db->insert('tb_voucher_tukang', $this);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function createPriority($id_tukang, $no_seri, $id_contact, $nominal, $nota)
    {
        $data = [
            'id_tukang' => $id_tukang,
            'id_contact' => $id_contact,
            'no_seri' => $no_seri,
            'updated_at' => date("Y-m-d H:i:s"),
            'exp_at' => date("Y-m-d", strtotime("+1 week")),
            'id_md5' => md5("Top" . md5($id_tukang)),
            'is_priority' => 1,
            'nota_pembelian' => $nota,
            'nominal_pembelian' => $nominal,
            'type_voucher' => 'priority'
        ];

        $query = $this->db->insert('tb_voucher_tukang', $data);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function createTokopromo($id_tukang, $no_seri, $id_contact, $nominal, $nota)
    {
        $data = [
            'id_tukang' => $id_tukang,
            'id_contact' => $id_contact,
            'no_seri' => $no_seri,
            'updated_at' => date("Y-m-d H:i:s"),
            'exp_at' => date("Y-m-d", strtotime("+1 week")),
            'id_md5' => md5("Top" . md5($id_tukang)),
            'is_priority' => 1,
            'nota_pembelian' => $nota,
            'nominal_pembelian' => $nominal,
            'type_voucher' => 'tokopromo'
        ];

        $query = $this->db->insert('tb_voucher_tukang', $data);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }
}
