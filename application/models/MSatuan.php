<?php

class MSatuan extends CI_Model
{
    public $name_satuan;
    public $updated_at;

    public function get()
    {
        $result = $this->db->get('tb_satuan')->result_array();

        return $result;
    }

    public function getById($id)
    {
        $result = $this->db->get_where('tb_satuan', ['id_satuan' => $id])->row_array();

        return $result;
    }

    public function create()
    {
        $post = $this->input->post();
        $this->name_satuan = $post['name_satuan'];
        $this->updated_at = date("Y-m-d H:i:s");

        $query = $this->db->insert('tb_satuan', $this);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function update($id)
    {
        $post = $this->input->post();
        $this->name_satuan = $post['name_satuan'];
        $this->updated_at = date("Y-m-d H:i:s");

        $query = $this->db->update('tb_satuan', $this, ['id_satuan' => $id]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function destroy($id)
    {
        $query = $this->db->delete('tb_satuan', ['id_satuan' => $id]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }
}
