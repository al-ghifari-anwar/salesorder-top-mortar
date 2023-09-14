<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MPayment extends CI_Model
{
    public $id_invoice;

    public function getAll()
    {
        $query = $this->db->get('tb_payment')->result_array();

        return $query;
    }

    public function getByIdInvoice($id_invoice)
    {
        $query = $this->db->get_where('tb_payment', ['id_invoice' => $id_invoice])->result_array();

        return $query;
    }

    public function getUnmatch()
    {
        $query = $this->db->get_where('tb_payment', ['id_invoice' => 0])->result_array();

        return $query;
    }

    public function setPaymentInv($id)
    {
        $post = $this->input->post();
        $this->id_invoice = $post['id_invoice'];

        $query = $this->db->update('tb_payment', $this, ['id_payment' => $id]);

        if ($query) {
            $setInvStatus = $this->db->update('tb_invoice', ['status_invoice' => 'paid'], ['id_invoice' => $this->id_invoice]);
            if ($setInvStatus) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
