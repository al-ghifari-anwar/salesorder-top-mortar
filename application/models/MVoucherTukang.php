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
        $id_distributor = $this->session->userdata('id_distributor');
        $this->db->join('tb_tukang', 'tb_tukang.id_tukang = tb_voucher_tukang.id_tukang', 'LEFT');
        $this->db->join('tb_city', 'tb_city.id_city = tb_tukang.id_city', 'LEFT');
        $this->db->join('tb_skill', 'tb_skill.id_skill = tb_tukang.id_skill', 'LEFT');
        $this->db->order_by('tb_voucher_tukang.claim_date', 'DESC');
        $result = $this->db->get_where('tb_voucher_tukang', ['is_claimed' => 1, 'DATE(claim_date) >=' => $dateFrom,  'DATE(claim_date) <=' => $dateTo, 'id_distributor' => $id_distributor])->result_array();

        // echo $this->db->last_query();
        // die;

        return $result;
    }

    public function getByIdMd5($id_md5)
    {
        $result = $this->db->get_where('tb_voucher_tukang', ['id_md5' => $id_md5])->row_array();

        return $result;
    }

    public function getVoucherDigital($id_city)
    {
        $this->db->join('tb_tukang', 'tb_tukang.id_tukang = tb_voucher_tukang.id_tukang', 'LEFT');
        $this->db->join('tb_skill', 'tb_skill.id_skill = tb_tukang.id_skill', 'LEFT');
        $this->db->order_by('tb_voucher_tukang.created_at', 'DESC');
        $result = $this->db->get_where('tb_voucher_tukang', ['type_voucher' => 'digi_voucher', 'id_city' => $id_city])->result_array();

        return $result;
    }

    public function getForValidasi()
    {
        $this->db->join('tb_skill', 'tb_skill.id_skill = tb_tukang.id_skill');
        $this->db->join('tb_city', 'tb_city.id_city = tb_tukang.id_city');
        $this->db->order_by('tb_tukang.id_tukang', 'DESC');
        $this->db->where('tb_city.id_distributor', $this->session->userdata('id_distributor'));
        $result = $this->db->get_where('tb_tukang', ['is_valid' => 0])->result_array();

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

    public function createVoucherDigital($id_tukang, $no_seri, $id_contact, $nominal, $idMd5)
    {
        $data = [
            'id_tukang' => $id_tukang,
            'id_contact' => $id_contact,
            'no_seri' => $no_seri,
            'updated_at' => date("Y-m-d H:i:s"),
            'exp_at' => date("Y-m-d", strtotime("+1 week")),
            'id_md5' => $idMd5,
            'is_priority' => 1,
            'nominal_pembelian' => $nominal,
            'type_voucher' => 'digi_voucher'
        ];

        $query = $this->db->insert('tb_voucher_tukang', $data);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }
}
