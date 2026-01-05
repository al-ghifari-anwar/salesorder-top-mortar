<?php

class Webhookhaloai extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('HTelegram');
    }

    public function save()
    {
        $post = json_decode(file_get_contents('php://input'), true) != null ? json_decode(file_get_contents('php://input'), true) : $this->input->post();

        $webhookData = [
            'type_webhook_haloai' => 'chat',
            'content_webhook_haloai' => json_encode($post),
        ];

        $save = $this->db->insert('tb_webhook_haloai', $webhookData);

        if ($save) {
            $result = [
                'code' => 200,
                'status' => 'ok',
                'msg' => 'Success',
            ];

            return $this->output->set_output(json_encode($result));
        } else {
            $result = [
                'code' => 400,
                'status' => 'failed',
                'msg' => 'Failed',
            ];

            return $this->output->set_output(json_encode($result));
        }
    }

    public function bantuan()
    {
        $post = json_decode(file_get_contents('php://input'), true) != null ? json_decode(file_get_contents('php://input'), true) : $this->input->post();

        $webhookData = [
            'type_webhook_haloai' => 'bantuan',
            'content_webhook_haloai' => json_encode($post),
        ];

        $save = $this->db->insert('tb_webhook_haloai', $webhookData);

        if ($save) {
            $customer = $post['customer'];
            $name = $customer['name'];
            $phone = $customer['phone'];
            $bodyMsg = $post['message']['body'];

            $message = "Halo admin, \nAI Butuh Bantuan \nToko: " . $name . "\nNomor: " . $phone . "\nPesan: " . $bodyMsg;

            $chatId = "-5138247877";

            $this->HTelegram->sendTextPrivate($chatId, $message);

            $result = [
                'code' => 200,
                'status' => 'ok',
                'msg' => 'Success',
            ];

            return $this->output->set_output(json_encode($result));
        } else {
            $result = [
                'code' => 400,
                'status' => 'failed',
                'msg' => 'Failed',
            ];

            return $this->output->set_output(json_encode($result));
        }
    }

    public function sjgagal()
    {
        $post = json_decode(file_get_contents('php://input'), true) != null ? json_decode(file_get_contents('php://input'), true) : $this->input->post();

        $webhookData = [
            'type_webhook_haloai' => 'sj-gagal',
            'content_webhook_haloai' => json_encode($post),
        ];

        $save = $this->db->insert('tb_webhook_haloai', $webhookData);

        if ($save) {
            $customer = $post['customer'];
            $name = $customer['name'];
            $phone = $customer['phone'];
            $bodyMsg = $post['message']['body'];

            $message = "Halo admin, \nSurat Jalan Gagal Terbuat \nToko: " . $name . "\nNomor: " . $phone . "\nPesan: " . $bodyMsg;

            $chatId = "-5138247877";

            $this->HTelegram->sendTextPrivate($chatId, $message);

            $result = [
                'code' => 200,
                'status' => 'ok',
                'msg' => 'Success',
            ];

            return $this->output->set_output(json_encode($result));
        } else {
            $result = [
                'code' => 400,
                'status' => 'failed',
                'msg' => 'Failed',
            ];

            return $this->output->set_output(json_encode($result));
        }
    }
}
