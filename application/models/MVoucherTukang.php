<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MVoucherTukang extends CI_Model
{
    public $id_tukang;
    public $id_contact;
    public $no_seri;
    public $updated_at;
    public $exp_at;

    public function create($id_tukang, $no_seri)
    {
        $this->id_tukang = $id_tukang;
        $this->id_contact = 0;
        $this->no_seri = $no_seri;
        $this->updated_at = date("Y-m-d H:i:s");
        $this->exp_at = date("Y-m-d", strtotime("+1 week"));

        $query = $this->db->insert('tb_voucher_tukang', $this);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }
}
