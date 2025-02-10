<?php

class MWatzaptukang extends CI_Model
{
    public function getSingleWaiting()
    {
        $this->db->order_by('send_at', 'DESC');
        $result = $this->db->get_where('tb_watzap_tukang', ['status_watzap_tukang' => 'waiting'], 1)->row_array();

        return $result;
    }

    public function updateFromArray($id_watzap_tukang, $arrayWatzapTukang)
    {
        $query = $this->db->update('tb_watzap_tukang', $arrayWatzapTukang, ['id_watzap_tukang' => $id_watzap_tukang]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }
}
