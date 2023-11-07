<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MVisit extends CI_Model
{
    public $is_approved;

    public function getAll()
    {
        $now = date("Y-m-d");
        $from_date = date("Y-m-d", strtotime($now . " 00:00:00"));
        $to_date = date("Y-m-d", strtotime($now . " 23:59:59"));
        $query = $this->db->get_where('tb_visit', ['date_visit >=' => $from_date, 'date_visit <=' => $to_date])->result_array();

        return $query;
    }

    public function getAllByCity($id_city)
    {
        $from_date = date("Y-m-d 00:00:00");
        $to_date = date("Y-m-d 23:59:59");

        $this->db->join('tb_user', 'tb_user.id_user = tb_visit.id_user');
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_visit.id_contact');
        $query = $this->db->get_where('tb_visit', ['date_visit >=' => $from_date, 'date_visit <=' => $to_date, 'tb_user.id_city' => $id_city, 'is_approved' => 0, 'is_deleted' => 0])->result_array();

        return $query;
    }

    public function approve($id)
    {
        $this->is_approved = 1;

        $query = $this->db->update('tb_visit', $this, ['id_visit' => $id]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }
}
