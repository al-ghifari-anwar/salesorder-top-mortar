<?php


class MRenvi extends CI_Model
{
    public function getJatem1($id_city)
    {
        $query = $this->db->query("SELECT tb_renvis_jatem.*, tb_contact.nama, tb_contact.nomorhp, tb_contact.id_city, tb_contact.store_status, tb_contact.store_owner, tb_contact.maps_url, tb_contact.created_at AS created_at_store, tb_contact.reputation, tb_invoice.status_invoice, date_invoice, termin_payment, tb_contact.pass_contact, tb_contact.hari_bayar FROM tb_renvis_jatem JOIN tb_contact ON tb_contact.id_contact = tb_renvis_jatem.id_contact JOIN tb_invoice ON tb_invoice.id_invoice = tb_renvis_jatem.id_invoice WHERE type_renvis = 'jatem1' AND tb_contact.id_city = '$id_city' AND is_visited = 0 AND visit_date IS NULL")->result_array();

        return $query;
    }

    public function getJatem2($id_city)
    {
        $query = $this->db->query("SELECT tb_renvis_jatem.*, tb_contact.nama, tb_contact.nomorhp, tb_contact.id_city, tb_contact.store_status, tb_contact.store_owner, tb_contact.maps_url, tb_contact.created_at AS created_at_store, tb_contact.reputation, tb_invoice.status_invoice, date_invoice, termin_payment, tb_contact.pass_contact, tb_contact.hari_bayar FROM tb_renvis_jatem JOIN tb_contact ON tb_contact.id_contact = tb_renvis_jatem.id_contact JOIN tb_invoice ON tb_invoice.id_invoice = tb_renvis_jatem.id_invoice WHERE type_renvis = 'jatem2' AND tb_contact.id_city = '$id_city' AND is_visited = 0 AND visit_date IS NULL")->result_array();

        return $query;
    }

    public function getJatem3($id_city)
    {
        $query = $this->db->query("SELECT tb_renvis_jatem.*, tb_contact.nama, tb_contact.nomorhp, tb_contact.id_city, tb_contact.store_status, tb_contact.store_owner, tb_contact.maps_url, tb_contact.created_at AS created_at_store, tb_contact.reputation, tb_invoice.status_invoice, date_invoice, termin_payment, tb_contact.pass_contact, tb_contact.hari_bayar FROM tb_renvis_jatem JOIN tb_contact ON tb_contact.id_contact = tb_renvis_jatem.id_contact JOIN tb_invoice ON tb_invoice.id_invoice = tb_renvis_jatem.id_invoice WHERE type_renvis = 'jatem3' AND tb_contact.id_city = '$id_city' AND is_visited = 0 AND visit_date IS NULL")->result_array();

        return $query;
    }
}
