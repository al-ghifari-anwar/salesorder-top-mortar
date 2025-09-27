<?php

class MApporder extends CI_Model
{
    public function getById($id_apporder)
    {
        $query = $this->db->get_where('tb_apporder', ['id_apporder' => $id_apporder])->row_array();

        return $query;
    }

    public function get()
    {
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_apporder.id_contact');
        $this->db->join('tb_city', 'tb_city.id_city = tb_contact.id_city');
        $this->db->where('id_apporder NOT IN (SELECT id_apporder FROM tb_surat_jalan)', null, false);
        $this->db->order_by('id_apporder', 'DESC');
        $query = $this->db->get('tb_apporder')->result_array();

        return $query;
    }

    public function getByIdContact($id_contact)
    {
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get_where('tb_apporder', ['id_contact' => $id_contact])->result_array();

        return $query;
    }

    public function delete($id_apporder)
    {
        $query = $this->db->delete('tb_apporder', ['id_apporder' => $id_apporder]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }
}
