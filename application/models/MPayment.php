<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MPayment extends CI_Model
{
    public $id_invoice;

    public function getAll()
    {
        $this->db->join('tb_invoice', 'tb_invoice.id_invoice = tb_payment.id_invoice');
        $this->db->order_by('date_payment', 'DESC');
        $query = $this->db->get('tb_payment')->result_array();

        return $query;
    }

    public function getByIdInvoice($id_invoice, $dateFrom, $dateTo)
    {
        $query = $this->db->get_where('tb_payment', ['id_invoice' => $id_invoice, 'date_payment >= ' => $dateFrom, 'date_payment <= ' => $dateTo])->result_array();

        return $query;
    }

    public function getUnmatch()
    {
        $this->db->order_by('date_payment', 'DESC');
        $query = $this->db->get_where('tb_payment', ['id_invoice' => 0, 'is_removed' => 0])->result_array();

        return $query;
    }

    public function unassign($id)
    {
        $query = $this->db->update('tb_payment', ['id_invoice' => 0], ['id_payment' => $id]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function setPaymentInv($id)
    {
        $post = $this->input->post();
        $this->id_invoice = $post['id_invoice'];
        $id_invoice = $post['id_invoice'];

        $query = $this->db->update('tb_payment', $this, ['id_payment' => $id]);

        if ($query) {
            $getInv = $this->db->get_where('tb_invoice', ['id_invoice' => $this->id_invoice])->row_array();
            $getTotalPayment = $this->db->query("SELECT SUM(amount_payment) AS amount_total FROM tb_payment WHERE id_invoice = '$id_invoice'")->row_array();

            if ($getInv['total_invoice'] == $getTotalPayment['amount_total']) {

                $setInvStatus = $this->db->update('tb_invoice', ['status_invoice' => 'paid'], ['id_invoice' => $id_invoice]);

                if ($setInvStatus) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    public function remove($id)
    {
        $query = $this->db->update('tb_payment', ['is_removed' => 1], ['id_payment' => $id]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }
}
