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
        $query = $this->db->get_where('tb_voucher', ['tb_voucher.id_contact' => $id_contact, 'is_claimed' => 1])->result_array();

        return $query;
    }

    public function getByNomor()
    {
        $post = $this->input->post();

        $vouchersArr[] = $post['no_voucher1'];
        $vouchersOri = $post['no_voucher1'];
        if ($post['no_voucher2'] != null) {
            $vouchersArr[] = $post['no_voucher2'];
            $vouchersOri .= ", " . $post['no_voucher2'];
        }
        if ($post['no_voucher3'] != null) {
            $vouchersArr[] = $post['no_voucher3'];
            $vouchersOri .= ", " . $post['no_voucher3'];
        }
        if ($post['no_voucher4'] != null) {
            $vouchersArr[] = $post['no_voucher4'];
            $vouchersOri .= $post['no_voucher4'];
        }
        if ($post['no_voucher5'] != null) {
            $vouchersArr[] = $post['no_voucher5'];
            $vouchersOri .= ", " . $post['no_voucher5'];
        }

        // echo json_encode($vouchersArr);
        // die;

        $count_vouchers = count($vouchersArr);
        $no_vouchers = $vouchersArr;
        $no_vouchers = implode("','", $no_vouchers);
        $vouchers_ori = "";
        $dateNow = date("Y-m-d H:i:s");

        $query = $this->db->query("SELECT tb_contact.id_contact, SUM(point_voucher) as point_voucher FROM tb_voucher JOIN tb_contact ON tb_contact.id_contact = tb_voucher.id_contact WHERE tb_voucher.is_claimed = 0 AND tb_voucher.no_voucher IN ('" . $no_vouchers . "') AND DATE(exp_date) >= DATE(NOW())")->row_array();

        $getKode = $this->db->query("SELECT * FROM tb_voucher JOIN tb_contact ON tb_contact.id_contact = tb_voucher.id_contact WHERE tb_voucher.is_claimed = 0 AND tb_voucher.no_voucher IN ('" . $no_vouchers . "') AND DATE(exp_date) >= DATE(NOW())")->result_array();

        foreach ($getKode as $getKode) {
            $vouchers_ori .= $getKode['no_voucher'] . ",";
        }

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
            $this->session->set_flashdata('failed', "Kode voucher sudah pernah di claim atau expired!");
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
