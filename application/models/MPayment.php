<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MPayment extends CI_Model
{
    public $id_invoice;
    public $potongan_payment;
    public $adjustment_payment;

    public function getAll()
    {
        $this->db->join('tb_invoice', 'tb_invoice.id_invoice = tb_payment.id_invoice');
        $this->db->order_by('date_payment', 'DESC');
        if ($this->session->userdata('id_distributor') == 1) {
            $this->db->where('source', '8880762231');
        } else {
            $this->db->where('source', '8880964519');
        }
        $query = $this->db->get('tb_payment')->result_array();

        return $query;
    }

    public function getByIdInvoice($id_invoice, $dateFrom, $dateTo)
    {
        $query = $this->db->get_where('tb_payment', ['id_invoice' => $id_invoice, 'date_payment >= ' => $dateFrom, 'date_payment <= ' => $dateTo])->result_array();

        return $query;
    }

    public function getTotalPaymentInv($id_invoice)
    {
        $this->db->select("SUM(amount_payment) AS amount_payment");
        $query = $this->db->get_where('tb_payment', ['id_invoice' => $id_invoice])->row_array();
        return $query;
    }

    public function getUnmatch()
    {
        $this->db->order_by('date_payment', 'DESC');
        if ($this->session->userdata('id_distributor') == 1) {
            $this->db->where('source', '8880762231');
        } else {
            $this->db->where('source', '8880964519');
        }
        $query = $this->db->get_where('tb_payment', ['id_invoice' => 0, 'is_removed' => 0])->result_array();

        return $query;
    }

    public function unassign($id)
    {
        $getPayment = $this->db->get_where('tb_payment', ['id_payment' => $id])->row_array();

        $query = $this->db->update('tb_payment', ['id_invoice' => 0], ['id_payment' => $id]);

        if ($query) {
            $unpaid = $this->db->update('tb_invoice', ['status_invoice' => 'waiting'], ['id_invoice' => $getPayment['id_invoice']]);

            if ($unpaid) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function setPaymentInv($id)
    {
        $post = $this->input->post();
        $id_invoice = $post['id_invoice'];
        $getInv = $this->db->get_where('tb_invoice', ['id_invoice' => $id_invoice])->row_array();
        $id_surat_jalan = $getInv['id_surat_jalan'];
        $getItem = $this->db->query("SELECT SUM(qty_produk) AS qty_total FROM tb_detail_surat_jalan WHERE id_surat_jalan = '$id_surat_jalan' AND is_bonus = 0")->row_array();

        $dateInvoice = $getInv['date_invoice'];

        $date1 = new DateTime(date("Y-m-d"));
        $date2 = new DateTime($dateInvoice);
        $days  = $date2->diff($date1)->format('%a');
        $operan = "";
        if ($date1 < $date2) {
            $operan = "-";
        }
        $days = $operan . $days;


        $this->id_invoice = $post['id_invoice'];
        $this->potongan_payment = $post['potongan'] * $getItem['qty_total'];
        $this->adjustment_payment = $post['adjustment'];

        if ($post['potongan'] == "1000") {
            if ($days > 35) {
                $this->potongan_payment = 0;
            }
        }

        $query = $this->db->update('tb_payment', $this, ['id_payment' => $id]);

        if ($query) {
            $this->db->join('tb_surat_jalan', 'tb_surat_jalan.id_surat_jalan = tb_invoice.id_surat_jalan');
            $getInv = $this->db->get_where('tb_invoice', ['id_invoice' => $this->id_invoice])->row_array();
            $getTotalPayment = $this->db->query("SELECT SUM(amount_payment + potongan_payment + adjustment_payment) AS amount_total FROM tb_payment WHERE id_invoice = '$id_invoice'")->row_array();

            $date = date("Y-m-d H:i:s");

            if ($getInv['total_invoice'] == $getTotalPayment['amount_total']) {

                $setInvStatus = $this->db->update('tb_invoice', ['status_invoice' => 'paid'], ['id_invoice' => $id_invoice]);

                if ($setInvStatus) {
                    $removeRenvis = $this->db->update('tb_rencana_visit', ['is_visited' => 1, 'visit_date' => $date], ['id_contact' => $getInv['id_contact'], 'type_rencana' => 'jatem']);

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
