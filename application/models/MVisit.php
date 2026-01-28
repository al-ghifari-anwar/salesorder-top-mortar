<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MVisit extends CI_Model
{
    public $is_approved;
    public $approve_message;

    public function getAll()
    {
        $now = date("Y-m-d");
        $from_date = date("Y-m-d", strtotime($now . " 00:00:00"));
        $to_date = date("Y-m-d", strtotime($now . " 23:59:59"));
        $query = $this->db->get_where('tb_visit', ['date_visit >=' => $from_date, 'date_visit <=' => $to_date])->result_array();

        return $query;
    }

    public function getGroupedContact($id_user, $id_city, $month)
    {
        $this->db->join('tb_user', 'tb_user.id_user = tb_visit.id_user');
        $this->db->join('tb_city', 'tb_city.id_city = tb_user.id_city');
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_visit.id_contact');
        $this->db->where('MONTH(date_visit)', $month);
        $this->db->where('YEAR(date_visit)', date("Y"));
        if ($id_user != 0) {
            $this->db->where('tb_visit.id_user', $id_user);
        }
        $this->db->where('tb_contact.id_city', $id_city);
        $this->db->group_by('tb_user.id_user');
        $query = $this->db->get('tb_visit')->result_array();
        // echo $this->db->last_query();
        // die;

        return $query;
    }

    public function getGroupedContactGlobal($id_city, $type, $dateFrom, $dateTo)
    {
        $this->db->join('tb_user', 'tb_user.id_user = tb_visit.id_user');
        $this->db->join('tb_city', 'tb_city.id_city = tb_user.id_city');
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_visit.id_contact');
        $this->db->where('DATE(date_visit) >=', $dateFrom);
        $this->db->where('DATE(date_visit) <=', $dateTo);
        // $this->db->where('YEAR(date_visit)', date("Y"));
        if ($type != 'sales') {
            $this->db->where('tb_user.level_user', $type);
        } else {
            $this->db->where_in('tb_user.level_user', ['sales', 'penagihan', 'marketing']);
        }
        $this->db->not_like('tb_visit.source_visit', 'absen');
        $this->db->where('tb_contact.id_city', $id_city);
        $this->db->group_by('tb_user.id_user');
        $query = $this->db->get('tb_visit')->result_array();
        // echo $this->db->last_query();
        // die;

        return $query;
    }

    public function getGroupedContactGlobalMonth($id_city, $type, $month)
    {
        $this->db->join('tb_user', 'tb_user.id_user = tb_visit.id_user');
        $this->db->join('tb_city', 'tb_city.id_city = tb_user.id_city');
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_visit.id_contact');
        $this->db->where('MONTH(date_visit)', $month);
        $this->db->where('YEAR(date_visit)', date("Y"));
        if ($type != 'sales') {
            $this->db->where('tb_user.level_user', $type);
        } else {
            $this->db->where_in('tb_user.level_user', ['sales', 'penagihan', 'marketing']);
        }
        $this->db->where('tb_contact.id_city', $id_city);
        $this->db->group_by('tb_user.id_user');
        $query = $this->db->get('tb_visit')->result_array();
        // echo $this->db->last_query();
        // die;

        return $query;
    }

    public function getGroupedCourierGlobal($id_city, $month, $type, $year)
    {
        $this->db->join('tb_user', 'tb_user.id_user = tb_visit.id_user');
        $this->db->join('tb_city', 'tb_city.id_city = tb_user.id_city');
        $this->db->join('tb_gudang', 'tb_gudang.id_gudang = tb_visit.id_contact');
        $this->db->where('MONTH(date_visit)', $month);
        $this->db->where('YEAR(date_visit)', $year);
        if ($type != 'sales') {
            $this->db->where('tb_user.level_user', $type);
        } else {
            $this->db->where_in('tb_user.level_user', ['sales', 'penagihan']);
        }
        $this->db->where('tb_user.id_city', $id_city);
        $this->db->group_by('tb_user.id_user');
        $query = $this->db->get('tb_visit')->result_array();
        // echo $this->db->last_query();
        // die;

        return $query;
    }

    public function getAllContactGlobal($id_city, $month, $type)
    {
        $this->db->join('tb_user', 'tb_user.id_user = tb_visit.id_user');
        $this->db->join('tb_city', 'tb_city.id_city = tb_user.id_city');
        $this->db->where('MONTH(date_visit)', $month);
        $this->db->where('tb_user.level_user', $type);
        $this->db->where('tb_user.id_city', $id_city);
        $this->db->group_by('tb_user.id_user');
        $query = $this->db->get('tb_visit')->result_array();
        // echo $this->db->last_query();
        // die;

        return $query;
    }

    public function getAllByCity($id_city)
    {
        $from_date = date("Y-m-d 00:00:00");
        $to_date = date("Y-m-d 23:59:59");

        $this->db->join('tb_user', 'tb_user.id_user = tb_visit.id_user');
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_visit.id_contact');
        $this->db->where_in('tb_user.level_user', ['sales', 'penagihan', 'mg', 'marketing']);
        $this->db->not_like('tb_visit.source_visit', 'absen');
        if ($this->session->userdata('id_distributor') == 4) {
            $query = $this->db->get_where('tb_visit', ['tb_contact.id_city' => $id_city, 'is_approved_2' => 0, 'tb_visit.is_deleted' => 0])->result_array();
        } else {
            $query = $this->db->get_where('tb_visit', ['tb_contact.id_city' => $id_city, 'is_approved' => 0, 'tb_visit.is_deleted' => 0])->result_array();
        }

        return $query;
    }

    public function getAllByCityAndBulan($id_city, $bulan)
    {
        $from_date = date("Y-m-d 00:00:00");
        $to_date = date("Y-m-d 23:59:59");

        $this->db->join('tb_user', 'tb_user.id_user = tb_visit.id_user');
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_visit.id_contact');
        $this->db->not_like('tb_visit.source_visit', 'absen');
        $query = $this->db->get_where('tb_visit', ['tb_user.id_city' => $id_city, 'is_approved' => 0, 'is_deleted' => 0, 'tb_user.level_user' => 'sales'])->result_array();

        return $query;
    }

    public function getKurirByCity($id_city)
    {
        $from_date = date("Y-m-d 00:00:00");
        $to_date = date("Y-m-d 23:59:59");

        $this->db->join('tb_user', 'tb_user.id_user = tb_visit.id_user');
        $this->db->join('tb_gudang', 'tb_gudang.id_gudang = tb_visit.id_contact');
        $query = $this->db->get_where('tb_visit', ['tb_user.id_city' => $id_city, 'is_approved' => 0, 'is_deleted' => 0, 'tb_user.level_user' => 'courier'])->result_array();

        return $query;
    }

    public function getByCityAndDate($id_city, $id_user, $dateFrom, $dateTo)
    {
        $this->db->join('tb_user', 'tb_user.id_user = tb_visit.id_user');
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_visit.id_contact');
        $this->db->where('DATE(date_visit) >=', $dateFrom);
        $this->db->where('DATE(date_visit) <=', $dateTo);
        // $this->db->where('YEAR(date_visit) =', date("Y"));
        $this->db->group_by('tb_visit.id_contact');
        if ($this->session->userdata('id_distributor') == 4) {
            $query = $this->db->get_where('tb_visit', ['tb_contact.id_city' => $id_city, 'tb_user.id_user' => $id_user, 'is_approved_2' => 0, 'tb_visit.is_deleted' => 0,])->result_array();
        } else {
            $query = $this->db->get_where('tb_visit', ['tb_contact.id_city' => $id_city, 'tb_user.id_user' => $id_user, 'is_approved' => 0, 'tb_visit.is_deleted' => 0,])->result_array();
        }

        return $query;
    }

    public function getByCityAndDateCourier($id_city, $id_user, $bulan)
    {
        $this->db->join('tb_user', 'tb_user.id_user = tb_visit.id_user');
        $this->db->join('tb_gudang', 'tb_gudang.id_gudang = tb_visit.id_contact');
        $this->db->where('MONTH(date_visit) =', $bulan);
        $this->db->group_by('tb_visit.id_gudang');
        $query = $this->db->get_where('tb_visit', ['tb_user.id_city' => $id_city, 'tb_user.id_user' => $id_user, 'is_approved' => 0, 'is_deleted' => 0,])->result_array();

        return $query;
    }

    public function approve($id)
    {
        $post = $this->input->post();
        $this->is_approved = 1;
        $this->approve_message = $post['approve_message'];

        $query = $this->db->update('tb_visit', $this, ['id_visit' => $id]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }
}
