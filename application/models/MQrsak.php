<?php


class MQrsak extends CI_Model
{
    public function get()
    {
        $query = $this->db->get_where('tb_qrsak', ['id_distributor' => $this->session->userdata('id_distributor')])->result_array();

        return $query;
    }

    public function getById($id_qrsak)
    {
        $query = $this->db->get_where('tb_qrsak', ['id_qrsak' => $id_qrsak])->row_array();

        return $query;
    }

    public function create($qrsakData)
    {
        $query = $this->db->insert('tb_qrsak', $qrsakData);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function update($id_qrsak, $qrsakData)
    {
        $query = $this->db->update('tb_qrsak', $qrsakData, ['id_qrsak' => $id_qrsak]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function delete($id_qrsak)
    {
        $query = $this->db->delete('tb_qrsak', ['id_qrsak' => $id_qrsak]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }
}
