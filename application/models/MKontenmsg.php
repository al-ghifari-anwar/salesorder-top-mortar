<?php

class MKontenmsg extends CI_Model
{
    public function get()
    {
        $this->db->order_by('tb_kontenmsg.created_at', 'DESC');
        $query = $this->db->get('tb_kontenmsg')->result_array();

        return $query;
    }

    public function getByCat($cat_kontenmsg)
    {
        $this->db->order_by('tb_kontenmsg.created_at', 'DESC');
        $query = $this->db->get_where('tb_kontenmsg', ['cat_kontenmsg' => $cat_kontenmsg])->result_array();

        return $query;
    }

    public function getByCatAndHobby($cat_kontenmsg, $hobby_customer)
    {
        $this->db->select('name_kontenmsg AS judul, thumbnail_kontenmsg AS link_thumbnail, link_kontenmsg AS link_konten, body_kontenmsg AS deskripsi_konten');
        $this->db->where('topic_kontenmsg REGEXP', $hobby_customer);
        $this->db->order_by('tb_kontenmsg.created_at', 'DESC');
        $query = $this->db->get_where('tb_kontenmsg', ['cat_kontenmsg' => $cat_kontenmsg])->result_array();

        return $query;
    }

    public function getById($id_kontenmsg)
    {
        $query = $this->db->get_where('tb_kontenmsg', ['id_kontenmsg' => $id_kontenmsg])->row_array();

        return $query;
    }

    public function create($kontenmsgData)
    {
        $query = $this->db->insert('tb_kontenmsg', $kontenmsgData);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function update($id_kontenmsg, $kontenmsgData)
    {
        $query = $this->db->update('tb_kontenmsg', $kontenmsgData, ['id_kontenmsg' => $id_kontenmsg]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function delete($id_kontenmsg)
    {
        $query = $this->db->delete('tb_kontenmsg', ['id_kontenmsg' => $id_kontenmsg]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }
}
