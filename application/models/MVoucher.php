<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MVoucher extends CI_Model
{
    public $id_contact;
    public $no_voucher;
    public $point_voucher;

    public function insert()
    {
        $post = $this->input->post();
        
        $no_vouchers = explode(",", $post['no_voucher']);
        
        $no = 0;
        foreach($no_vouchers as $no_voucher){
            $this->id_contact = $post['id_contact'];
            $this->point_voucher = $post['point_voucher'];
            $this->no_voucher = $no_voucher;

            $query = $this->db->insert('tb_voucher', $this);
        }


        return true;
    }

    public function getByNomor()
    {
        $post = $this->input->post();

        $count_vouchers = count(explode(",", $post['no_voucher']));
        $no_vouchers = array_map('intval', explode(",", $post['no_voucher']));
        $no_vouchers = implode("','", $no_vouchers);

        $query = $this->db->query("SELECT tb_contact.id_contact, SUM(point_voucher) as point_voucher FROM tb_voucher JOIN tb_contact ON tb_contact.id_contact = tb_voucher.id_contact WHERE tb_voucher.no_voucher IN ('" . $no_vouchers . "')")->row_array();

        echo $this->db->last_query();
        die;

        $data = [
            'count_vouchers' => $count_vouchers,
            'actual_vouchers' => $query['point_voucher'],
            'id_contact' => $query['id_contact'],
            'invalid_voucher' => $count_vouchers - $query['point_voucher']
        ];

        return $data;
    }
}