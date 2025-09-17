<?php

class MPromoTopseller extends CI_Model
{
    public function get()
    {
        $query = $this->db->get_where('tb_promo_topseller')->result_array();

        return $query;
    }

    public function getById($id_promo_topseller)
    {
        $query = $this->db->get_where('tb_promo_topseller', ['id_promo_topseller' => $id_promo_topseller])->row_array();

        return $query;
    }

    public function create($promoTopsellerData)
    {
        $query = $this->db->insert('tb_promo_topseller', $promoTopsellerData);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function update($id_promo_topseller, $promoTopsellerData)
    {
        $query = $this->db->update('tb_promo_topseller', $promoTopsellerData, ['id_promo_topseller' => $id_promo_topseller]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function delete($id_promo_topseller)
    {
        $query = $this->db->delete('tb_promo_topseller', ['id_promo_topseller' => $id_promo_topseller]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }
}
