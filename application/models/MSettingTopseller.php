<?php

class MSettingTopseller extends CI_Model
{
    public function get()
    {
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get('tb_setting_topseller', 1)->row_array();

        return $query;
    }

    public function update($settingTopsellerData, $id_setting_topseller)
    {
        $query = $this->db->update('tb_setting_topseller', $settingTopsellerData, ['id_setting_topseller' => $id_setting_topseller]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }
}
