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
        $this->id_contact = $post['id_contact'];
        $this->no_voucher = $post['no_voucher'];
        $this->point_voucher = $post['point_voucher'];

        $query = $this->db->insert('tb_voucher', $this);

        if($query){
            return true;
        } else {
            return false;
        }
    }

    public function getByNomor()
    {
        $post = $this->input->post();
        $no_voucher = $post['no_voucher'];
        $nomorhp = $post['nomorhp'];

        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_voucher.id_contact');
        $query = $this->db->get_where('tb_voucher', ['no_voucher' => $no_voucher])->row_array();

        $date1 = new DateTime(date("Y-m-d"));
        $date2 = new DateTime(date("Y-m-d", strtotime($query['date_voucher'])));
        $days  = $date2->diff($date1)->format('%a');
        $operan = "";
        if ($date1 < $date2) {
            $operan = "-";
        }
        $days = $operan . $days;

        if($query == null){
            $this->session->set_flashdata('failed', "Voucher tidak valid!");
            redirect('claim');
        } else {
            if($query['nomorhp'] != $nomorhp){
                $this->session->set_flashdata('failed', "Nomot hp tidak sesuai!");
                redirect('claim');
            } else {
                if($days > 30){
                    $this->session->set_flashdata('failed', "Voucher sudah kadaluarsa!");
                    redirect('claim');
                } else {
                    if($query['is_claimed'] == 1){
                        $this->session->set_flashdata('failed', "Voucher sudah pernah di-claim!");
                        redirect('claim');
                    } else {
                        return "OK";
                    }
                }
            }
        }

    }
}