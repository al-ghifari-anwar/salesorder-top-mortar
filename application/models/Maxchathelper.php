<?php

class Maxchathelper extends CI_Model
{
    public function postCurl($id_distributor, $endpoint, $data)
    {
        $maxchat = $this->db->get_where('tb_maxchat', ['id_distributor' => $id_distributor])->row_array();

        $headers = [
            'Authorization: Bearer ' . $maxchat['token_maxchat'],
            'Content-Type: application/json',
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $endpoint,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        return json_decode($response, true);
    }
}
