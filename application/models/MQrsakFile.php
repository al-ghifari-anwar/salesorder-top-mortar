<?php

class MQrsakFile extends CI_Model
{
    public function getByIdQrsak($id_qrsak)
    {
        $query = $this->db->get_where('tb_qrsak_file', ['id_qrsak' => $id_qrsak])->result_array();

        return $query;
    }

    public function getById($id_qrsak_file)
    {
        $query = $this->db->get_where('tb_qrsak_file', ['id_qrsak_file' => $id_qrsak_file])->row_array();

        return $query;
    }

    public function create($qrsakData)
    {
        $query = $this->db->insert('tb_qrsak_file', $qrsakData);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function update($id_qrsak_file, $qrsakData)
    {
        $query = $this->db->update('tb_qrsak_file', $qrsakData, ['id_qrsak_file' => $id_qrsak_file]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function delete($id_qrsak_file)
    {
        $query = $this->db->delete('tb_qrsak_file', ['id_qrsak_file' => $id_qrsak_file]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }
}
