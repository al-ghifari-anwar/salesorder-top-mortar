<?php

class Maxchat extends CI_Controller
{
    public function inbound()
    {
        $json = file_get_contents("php://input");

        $vars = json_decode($json, true);

        $dataInbound = [
            'from_inbound_msg' => $vars['from'],
            'type_inbound_msg' => $vars['msgType'],
            'serviceid_inbound_msg' => $vars['serviceId'],
            'timestamp_inbound_msg' => $vars['timestamp'],
            'text_inbound_msg' => $vars['text'],
            'username_inbound_msg' => $vars['username'],
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ];

        $this->db->insert('tb_inbound_msg', $dataInbound);
    }
}
