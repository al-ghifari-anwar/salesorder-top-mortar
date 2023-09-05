<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MInvoice extends CI_Model
{

    public $is_printed;
    public $date_printed;

    public function getAll()
    {
        $this->db->join('tb_surat_jalan', 'tb_surat_jalan.id_surat_jalan = tb_invoice.id_surat_jalan');
        $this->db->order_by('id_invoice', 'DESC');
        $query = $this->db->get('tb_invoice')->result_array();
        return $query;
    }

    public function getById($id)
    {
        $this->db->join('tb_surat_jalan', 'tb_surat_jalan.id_surat_jalan = tb_invoice.id_surat_jalan');
        $query = $this->db->get_where('tb_invoice', ['id_invoice' => $id])->row_array();
        return $query;
    }

    public function delete($id)
    {
        $query = $this->db->delete('tb_invoice', ['id_invoice' => $id]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }
}
