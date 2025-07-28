<?php

class MDiscountApp extends CI_Model
{
    public function get()
    {
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get('tb_discount_app', 1)->row_array();

        return $query;
    }

    public function update($discountAppData, $id_dicount_app)
    {
        $query = $this->db->update('tb_discount_app', $discountAppData, ['id_discount_app' => $id_dicount_app]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }
}
