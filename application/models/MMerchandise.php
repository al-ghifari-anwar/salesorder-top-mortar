<?php

class MMerchandise extends CI_Model
{
    public function get()
    {
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get('tb_merchandise')->result_array();

        return $query;
    }

    public function getById($id_merchandise)
    {
        $query = $this->db->get_where('tb_merchandise', ['id_merchandise' => $id_merchandise])->row_array();

        return $query;
    }

    public function create($merchandiseData)
    {
        $query = $this->db->insert('tb_merchandise', $merchandiseData);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function update($id_merchandise, $merchandiseData)
    {
        $query = $this->db->update('tb_merchandise', $merchandiseData, ['id_merchandise', $id_merchandise]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function delete($id_merchandise)
    {
        $query = $this->db->delete('tb_merchandise', ['id_merchandise', $id_merchandise]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }
}
