<?php

class MTagihandetail extends CI_Model
{
    public function getByIdTagihan($id_tagihan)
    {
        $result = $this->db->get_where('tb_tagihan_detail', ['id_tagihan' => $id_tagihan])->result_array();

        return $result;
    }

    public function getById($id_tagihan_detail)
    {
        $result = $this->db->get_where('tb_tagihan_detail', ['id_tagihan_detail' => $id_tagihan_detail])->result_array();

        return $result;
    }

    public function create($tagihanDetailData)
    {
        $save = $this->db->insert('tb_tagihan', $tagihanDetailData);

        if ($save) {
            return true;
        } else {
            return false;
        }
    }
}
