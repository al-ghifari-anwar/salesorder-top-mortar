<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MInvoice extends CI_Model
{

    public $is_printed;
    public $date_printed;

    public function getForTagihan($id_distributor, $month)
    {
        $this->db->join('tb_surat_jalan', 'tb_surat_jalan.id_surat_jalan = tb_invoice.id_surat_jalan');
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_surat_jalan.id_contact');
        $this->db->join('tb_city', 'tb_city.id_city = tb_contact.id_city');
        $this->db->where('tb_city.id_distributor', $id_distributor);
        $this->db->where("DATE_FORMAT(tb_invoice.date_invoice, '%Y-%m')", $month);
        $result = $this->db->get('tb_invoice')->result_array();

        return $result;
    }

    public function getAllDefault()
    {
        $this->db->join('tb_surat_jalan', 'tb_surat_jalan.id_surat_jalan = tb_invoice.id_surat_jalan');
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_surat_jalan.id_contact');
        $this->db->join('tb_city', 'tb_city.id_city = tb_contact.id_city');
        $this->db->order_by('id_invoice', 'DESC');
        $query = $this->db->get_where('tb_invoice', ['id_distributor' => $this->session->userdata('id_distributor')])->result_array();
        return $query;
    }

    public function getAll($id_city)
    {
        $this->db->join('tb_surat_jalan', 'tb_surat_jalan.id_surat_jalan = tb_invoice.id_surat_jalan');
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_surat_jalan.id_contact');
        $this->db->order_by('id_invoice', 'DESC');
        $query = $this->db->get_where('tb_invoice', ['tb_contact.id_city' => $id_city, 'tb_invoice.is_printed' => 0])->result_array();
        return $query;
    }

    public function getSentInvoice($id_city)
    {
        $this->db->join('tb_surat_jalan', 'tb_surat_jalan.id_surat_jalan = tb_invoice.id_surat_jalan');
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_surat_jalan.id_contact');
        $this->db->order_by('id_invoice', 'DESC');
        $query = $this->db->get_where('tb_invoice', ['tb_contact.id_city' => $id_city, 'tb_invoice.is_printed' => 1, 'tb_invoice.is_rechieved' => 0])->result_array();
        return $query;
    }

    public function getByStore($dateFrom = null, $dateTo = null, $id_contact = null, $no_invoice = null)
    {
        $this->db->join('tb_surat_jalan', 'tb_surat_jalan.id_surat_jalan = tb_invoice.id_surat_jalan');
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_surat_jalan.id_contact');
        $this->db->order_by('tb_surat_jalan.id_contact', 'ASC');
        if ($no_invoice != 0) {
            $this->db->where('tb_invoice.id_surat_jalan', $no_invoice);
        }
        $query = $this->db->get_where('tb_invoice', ['date_invoice >= ' => $dateFrom, 'date_invoice <=' => $dateTo, 'tb_surat_jalan.id_contact' => $id_contact])->result_array();
        return $query;
    }

    public function getByIdContact($id_contact)
    {
        $this->db->join('tb_surat_jalan', 'tb_surat_jalan.id_surat_jalan = tb_invoice.id_surat_jalan');
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_surat_jalan.id_contact');
        $this->db->order_by('tb_surat_jalan.id_contact', 'ASC');
        $query = $this->db->get_where('tb_invoice', ['tb_surat_jalan.id_contact' => $id_contact])->result_array();
        return $query;
    }

    public function getByIdContactNoMerch($id_contact)
    {
        $this->db->join('tb_surat_jalan', 'tb_surat_jalan.id_surat_jalan = tb_invoice.id_surat_jalan');
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_surat_jalan.id_contact');
        $this->db->order_by('tb_surat_jalan.id_contact', 'ASC');
        $query = $this->db->get_where('tb_invoice', ['tb_surat_jalan.id_contact' => $id_contact, 'tb_invoice.total_invoice >' => 1000])->result_array();
        return $query;
    }

    public function getPaidByIdContactNoMerch($id_contact)
    {
        $this->db->join('tb_surat_jalan', 'tb_surat_jalan.id_surat_jalan = tb_invoice.id_surat_jalan');
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_surat_jalan.id_contact');
        $query = $this->db->get_where('tb_invoice', ['tb_surat_jalan.id_contact' => $id_contact, 'tb_invoice.total_invoice >' => 1000, 'tb_invoice.status_invoice' => 'paid'])->result_array();
        return $query;
    }

    public function getLast3PaidByIdContactNoMerch($id_contact)
    {
        $this->db->join('tb_surat_jalan', 'tb_surat_jalan.id_surat_jalan = tb_invoice.id_surat_jalan');
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_surat_jalan.id_contact');
        $this->db->order_by('tb_invoice.date_invoice', 'DESC');
        $query = $this->db->get_where('tb_invoice', ['tb_surat_jalan.id_contact' => $id_contact, 'tb_invoice.total_invoice >' => 1000, 'tb_invoice.status_invoice' => 'paid'], 3)->result_array();
        return $query;
    }

    public function getByStorePiutang($dateFrom = null, $dateTo = null, $id_contact = null)
    {
        $this->db->join('tb_surat_jalan', 'tb_surat_jalan.id_surat_jalan = tb_invoice.id_surat_jalan');
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_surat_jalan.id_contact');
        $this->db->order_by('tb_surat_jalan.id_contact', 'ASC');
        if ($dateFrom != null) {
            $this->db->where('date_invoice >=', $dateFrom);
        }
        if ($dateTo != null) {
            $this->db->where('date_invoice <=', $dateTo);
        }
        $query = $this->db->get_where('tb_invoice', ['tb_surat_jalan.id_contact' => $id_contact, 'status_invoice' => 'waiting'])->result_array();
        return $query;
    }

    public function getGroupedContact($dateFrom = null, $dateTo = null, $id_contact = null, $no_invoice = null, $id_city = null)
    {
        $this->db->join('tb_surat_jalan', 'tb_surat_jalan.id_surat_jalan = tb_invoice.id_surat_jalan');
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_surat_jalan.id_contact');
        $this->db->join('tb_city', 'tb_contact.id_city = tb_city.id_city');
        $this->db->order_by('tb_surat_jalan.id_contact', 'ASC');
        $this->db->group_by('tb_surat_jalan.id_contact');
        if ($id_contact != 0) {
            $this->db->where('tb_contact.id_contact', $id_contact);
        }
        if ($no_invoice != 0) {
            $this->db->where('tb_invoice.id_surat_jalan', $no_invoice);
        }

        if ($id_city != 0) {
            $this->db->where('tb_city.id_city', $id_city);
        } else {
            if ($this->session->userdata('level_user') == 'admin_c') {
                $this->db->where('tb_city.id_city', $this->session->userdata('id_city'));
            }
        }
        $query = $this->db->get_where('tb_invoice', ['date_invoice >= ' => $dateFrom, 'date_invoice <= ' => $dateTo, 'id_distributor' => $this->session->userdata('id_distributor')])->result_array();
        // echo $this->db->last_query();
        // die;
        return $query;
    }

    public function getGroupedContactUnpaid($dateFrom = null, $dateTo = null, $id_contact = null, $id_city = null)
    {
        $this->db->join('tb_surat_jalan', 'tb_surat_jalan.id_surat_jalan = tb_invoice.id_surat_jalan');
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_surat_jalan.id_contact');
        $this->db->join('tb_city', 'tb_contact.id_city = tb_city.id_city');
        $this->db->order_by('tb_surat_jalan.id_contact', 'ASC');
        $this->db->group_by('tb_surat_jalan.id_contact');
        if ($id_contact != 0) {
            $this->db->where('tb_contact.id_contact', $id_contact);
        }
        if ($dateFrom != null) {
            $this->db->where('date_invoice >=', $dateFrom);
        }
        if ($dateTo != null) {
            $this->db->where('date_invoice <=', $dateTo);
        }
        if ($id_city != 0) {
            $this->db->where('tb_city.id_city', $id_city);
        }
        $query = $this->db->get_where('tb_invoice', ['status_invoice' => 'waiting', 'id_distributor' => $this->session->userdata('id_distributor')])->result_array();
        // echo $this->db->last_query();
        // die;
        return $query;
    }

    public function getInvoiceJatuhTempo($id_city, $id_distributor)
    {
        $this->db->join('tb_surat_jalan', 'tb_surat_jalan.id_surat_jalan = tb_invoice.id_surat_jalan');
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_surat_jalan.id_contact');
        $this->db->join('tb_city', 'tb_city.id_city = tb_contact.id_city');
        $this->db->order_by('tb_surat_jalan.id_contact', 'ASC');
        if ($id_city != 0) {
            $this->db->where('tb_contact.id_city', $id_city);
        }
        $query = $this->db->get_where('tb_invoice', ['status_invoice' => 'waiting', 'id_distributor' => $id_distributor])->result_array();
        // echo $this->db->last_query();
        // die;
        return $query;
    }

    public function getAllByDate($dateFrom = null, $dateTo = null, $id_contact = null, $id_city = null)
    {
        $this->db->join('tb_surat_jalan', 'tb_surat_jalan.id_surat_jalan = tb_invoice.id_surat_jalan');
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_surat_jalan.id_contact');
        $this->db->join('tb_city', 'tb_contact.id_city = tb_city.id_city');
        $this->db->join('tb_payment', 'tb_payment.id_invoice = tb_invoice.id_invoice', 'LEFT');
        $this->db->order_by('tb_surat_jalan.id_contact', 'ASC');
        $this->db->group_by('tb_invoice.id_invoice');
        if ($id_contact != 0) {
            $this->db->where('tb_contact.id_contact', $id_contact);
        }
        if ($this->session->userdata('level_user') == 'admin_c') {
            $this->db->where('tb_city.id_city', $this->session->userdata('id_city'));
        }
        if ($id_city != 0) {
            $this->db->where('tb_city.id_city', $id_city);
        }
        $query = $this->db->get_where('tb_invoice', ['date_payment >= ' => $dateFrom, 'date_payment <= ' => $dateTo, 'id_distributor' => $this->session->userdata('id_distributor')])->result_array();
        // echo $this->db->last_query();
        // die;
        return $query;
    }

    public function getAllByDateUnpaid($dateFrom = null, $dateTo = null)
    {
        $this->db->join('tb_surat_jalan', 'tb_surat_jalan.id_surat_jalan = tb_invoice.id_surat_jalan');
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_surat_jalan.id_contact');
        $this->db->join('tb_city', 'tb_contact.id_city = tb_city.id_city');
        $this->db->join('tb_payment', 'tb_payment.id_invoice = tb_invoice.id_invoice');
        $this->db->order_by('tb_surat_jalan.id_contact', 'ASC');
        $this->db->group_by('tb_invoice.id_invoice');
        $query = $this->db->get_where('tb_invoice', ['date_payment >= ' => $dateFrom, 'date_payment <= ' => $dateTo, 'status_invoice' => 'waiting'])->result_array();
        // echo $this->db->last_query();
        // die;
        // echo $this->db->last_query();
        // die;
        return $query;
    }

    public function getUnpaid()
    {
        $this->db->join('tb_surat_jalan', 'tb_surat_jalan.id_surat_jalan = tb_invoice.id_surat_jalan');
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_surat_jalan.id_contact');
        $this->db->join('tb_city', 'tb_contact.id_city = tb_city.id_city');
        $this->db->order_by('tb_surat_jalan.id_contact', 'ASC');
        $query = $this->db->get_where('tb_invoice', ['status_invoice' => 'waiting', 'tb_city.id_distributor' => $this->session->userdata('id_distributor')])->result_array();
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
