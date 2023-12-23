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

        $voucherBermasalah = '';
        $status = 'good';

        foreach ($no_vouchers as $cekLenght) {
            $cekLenght = str_replace(' ', '', $cekLenght);
            if (strlen($cekLenght) != 5) {
                $voucherBermasalah .= $cekLenght . ", ";
                $status = 'problem';
            }
        }

        $no = 0;
        foreach ($no_vouchers as $no_voucher) {
            $no_voucher = str_replace(' ', '', $no_voucher);
            if (strlen($no_voucher) == 5) {
                $this->id_contact = $post['id_contact'];
                $this->point_voucher = $post['point_voucher'];
                $this->no_voucher = $no_voucher;

                $cek = $this->db->get_where('tb_voucher', ['no_voucher' => $no_voucher])->result_array();

                if ($cek == null) {
                    $query = $this->db->insert('tb_voucher', $this);
                }
            }
        }

        return array("status" => $status, "voucher_bermasalah" => $voucherBermasalah);
    }

    public function getByIdContact($id_contact)
    {
        // $this->db->join('tb_contact', 'tb_contact.id_contact = tb_voucher.id_contact');
        $this->db->order_by('tb_voucher.date_voucher', 'DESC');
        $query = $this->db->get_where('tb_voucher', ['tb_voucher.id_contact' => $id_contact])->result_array();

        return $query;
    }

    public function getByNomor()
    {
        $post = $this->input->post();

        $count_vouchers = count(explode(",", $post['no_voucher']));
        $no_vouchers = array_map('strval', explode(",", $post['no_voucher']));
        $no_vouchers = implode("','", $no_vouchers);
        $vouchers_ori = $post['no_voucher'];

        $query = $this->db->query("SELECT tb_contact.id_contact, SUM(point_voucher) as point_voucher FROM tb_voucher JOIN tb_contact ON tb_contact.id_contact = tb_voucher.id_contact WHERE tb_voucher.is_claimed = 0 AND tb_voucher.no_voucher IN ('" . $no_vouchers . "')")->row_array();

        // echo json_encode($query);
        // die;
        if ($query['point_voucher'] != null) {
            $data = [
                'count_vouchers' => $count_vouchers,
                'actual_vouchers' => $query['point_voucher'],
                'id_contact' => $query['id_contact'],
                'invalid_voucher' => $count_vouchers - $query['point_voucher'],
                'voucher_ori' => $vouchers_ori
            ];

            return $data;
        } else {
            $this->session->set_flashdata('failed', "Kode voucher sudah pernah di claim!");
            redirect('claim');
        }
    }

    public function update_claim($vouchers)
    {
        $count_vouchers = count(explode(",", $vouchers));
        $no_vouchers = array_map('strval', explode(",", $vouchers));
        $no_vouchers = implode("','", $no_vouchers);

        $query = $this->db->query("UPDATE tb_voucher SET is_claimed = 1 WHERE no_voucher IN ('" . $no_vouchers . "')");
    }

    public function getByCity($id_city)
    {
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_voucher.id_contact');
        $this->db->order_by('tb_voucher.date_voucher', 'DESC');
        $query = $this->db->get_where('tb_voucher', ['tb_contact.id_city' => $id_city])->result_array();

        return $query;
    }
}
