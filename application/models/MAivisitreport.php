<?php


class MAivisitreport extends CI_Model
{
    public function getById($id_ai_reportvisit)
    {
        $query = $this->db->get_where('tb_ai_reportvisit', ['id_ai_reportvisit' => $id_ai_reportvisit])->row_array();

        return $query;
    }


    public function getByDateRange($dateFrom, $dateTo)
    {
        $this->db->where('DATE(tb_ai_reportvisit.created_at) >=', $dateFrom);
        $this->db->where('DATE(tb_ai_reportvisit.created_at) <=', $dateTo);

        $query = $this->db->get('tb_ai_reportvisit')->result_array();

        return $query;
    }
}
