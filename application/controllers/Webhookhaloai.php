<?php

class Webhookhaloai extends CI_Controller
{
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
