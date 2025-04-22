<?php

class MTagihan extends CI_Model
{
    public function getByIdDistributor($id_distributor)
    {
        $result = $this->db->get_where('tb_tagihan', ['id_distributor' => $id_distributor])->result_array();

        return $result;
    }

    public function getById($id_tagihan)
    {
        $result = $this->db->get_where('tb_tagihan', ['id_tagihan' => $id_tagihan])->row_array();

        return $result;
    }

    public function create($tagihanData)
    {
        $save = $this->db->insert('tb_tagihan', $tagihanData);

        if ($save) {
            return true;
        } else {
            return false;
        }
    }

    public function udpate($id_tagihan, $tagihanData)
    {
        $save = $this->db->update('tb_tagihan', $tagihanData, ['id_tagihan' => $id_tagihan]);

        if ($save) {
            return true;
        } else {
            return false;
        }
    }
}
