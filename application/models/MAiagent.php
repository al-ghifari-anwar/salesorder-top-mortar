<?php


class MAiagent extends CI_Model
{
    public function getByIdDistributor($id_distributor)
    {
        $query = $this->db->get_where('tb_ai_agent', ['id_distributor' => $id_distributor])->result_array();

        return $query;
    }

    public function getById($id_ai_agent)
    {
        $query = $this->db->get_where('tb_ai_agent', ['id_ai_agent' => $id_ai_agent])->row_array();

        return $query;
    }
}
