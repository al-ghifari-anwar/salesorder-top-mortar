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
}
