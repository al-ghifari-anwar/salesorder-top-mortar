<?php


class MQrsakDetail extends CI_Model
{
    public function get()
    {
        $query = $this->db->get_where('tb_qrsak_detail', ['id_distributor' => $this->session->userdata('id_distributor')])->result_array();

        return $query;
    }

    public function getById($id_qrsak)
    {
        $query = $this->db->get_where('tb_qrsak_detail', ['id_qrsak' => $id_qrsak])->row_array();

        return $query;
    }

    public function create($qrsakDetailData)
    {
        $query = $this->db->insert('tb_qrsak_detail', $qrsakDetailData);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function update($id_qrsak, $qrsakDetailData)
    {
        $query = $this->db->update('tb_qrsak_detail', $qrsakDetailData, ['id_qrsak' => $id_qrsak]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    // public function delete($id_qrsak)
    // {
    //     $query = $this->db->delete('tb_qrsak_detail', ['id_qrsak' => $id_qrsak]);

    //     if ($query) {
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }
}
