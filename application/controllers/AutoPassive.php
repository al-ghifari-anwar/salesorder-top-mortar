<?php

class AutoPassive extends CI_Controller
{
    public function top()
    {
        $citys = $this->db->get_where('tb_city', ['id_distributor' => 1])->result_array();

        foreach ($citys as $city) {
            $id_city = $city['id_city'];

            $contacts = $this->db->get_where('tb_contact', ['id_city' => $id_city, 'store_status !=' => 'blacklist'])->result_array();

            foreach ($contacts as $contact) {
                $id_contact = $contact['id_contact'];

                $lastOrder = $this->db->query("SELECT MAX(date_closing) as date_closing, id_contact FROM tb_surat_jalan WHERE id_contact = '$id_contact' AND is_closing = 1 GROUP BY id_contact")->row_array();

                if ($lastOrder != null) {
                    $dateMin6Week = date('Y-m-d', strtotime("-6 week"));
                    $dateMin2Month = date("Y-m-d", strtotime("-2 month"));
                    $dateLastOrder = date("Y-m-d", strtotime($lastOrder['date_closing']));

                    if ($dateLastOrder <= $dateMin6Week && $dateLastOrder >= $dateMin2Month) {
                        $renviPassiveData = [
                            'id_contact' => $id_contact,
                            'id_surat_jalan' => 0,
                            'is_visited' => 0,
                            'type_rencana' => 'passive',
                            'id_distributor' => 1,
                            'id_invoice' => 0,
                        ];

                        // echo json_encode($renviPassiveData);

                        $this->db->insert('tb_rencana_visit', $renviPassiveData);
                    }
                }
            }
        }
    }
}
