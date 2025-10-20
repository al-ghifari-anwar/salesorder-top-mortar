<?php


class MQrsakDetail extends CI_Model
{
    public function get()
    {
        $query = $this->db->get_where('tb_qrsak_detail', ['id_distributor' => $this->session->userdata('id_distributor')])->result_array();

        return $query;
    }

    public function getActiveGroupedBatch($id_qrsak)
    {
        $this->db->where('id_qrsak', $id_qrsak);
        $this->db->where('is_active', 1);
        $this->db->group_by('batch_qrsak_detail');
        $this->db->order_by('batch_qrsak_detail', 'ASC');
        $query = $this->db->get('tb_qrsak_detail')->result_array();

        return $query;
    }

    public function getByIdQrsak($id_qrsak)
    {
        $this->db->order_by('batch_qrsak_detail', 'DESC');
        $query = $this->db->get_where('tb_qrsak_detail', ['id_qrsak' => $id_qrsak])->result_array();

        return $query;
    }

    public function getById($id_qrsak_detail)
    {
        $query = $this->db->get_where('tb_qrsak_detail', ['id_qrsak_detail' => $id_qrsak_detail])->row_array();

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

    public function update($id_qrsak_detail, $qrsakDetailData)
    {
        $query = $this->db->update('tb_qrsak_detail', $qrsakDetailData, ['id_qrsak_detail' => $id_qrsak_detail]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function updateByBatch($id_qrsak, $batch_qrsak_detail, $qrsakDetailData)
    {
        $query = $this->db->update('tb_qrsak_detail', $qrsakDetailData, ['id_qrsak' => $id_qrsak, 'batch_qrsak_detail' => $batch_qrsak_detail]);

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
