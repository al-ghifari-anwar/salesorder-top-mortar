<?php

class Watzap extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model('MWatzaptukang');
    }

    public function insertTukangToWaitlist()
    {
        $tukangs = $this->db->get('tb_tukang')->result_array();

        foreach ($tukangs as $tukang) {
            $id_tukang = $tukang['id_tukang'];

            $this->db->order_by('created_at', 'DESC');
            $getWaitingTukang = $this->db->get_where('tb_watzap_tukang', ['id_tukang' => $id_tukang], 1)->row_array();

            if ($getWaitingTukang == null) {
                $getFirstMessage = $this->db->get_where('tb_marketing_message', ['urutan_marketing_message' => 1, 'target_marketing_message' => 'tukang'])->row_array();

                $dataWaiting = [
                    'id_tukang' => $id_tukang,
                    'id_marketing_message' => $getFirstMessage['id_marketing_message'],
                    'name_watzap_tukang' => $tukang['nama'],
                    'phone_watzap_tukang' => $tukang['nomorhp'],
                    'status_watzap_tukang' => 'waiting',
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $insert = $this->db->insert('tb_watzap_tukang', $dataWaiting);
            } else {
                $lastSentMessageId = $getWaitingTukang['id_marketing_message'];
                $lastMessage = $this->db->get_where('tb_marketing_message', ['id_marketing_message' => $lastSentMessageId])->row_array();
                $lastUrutan = $lastMessage['urutan_marketing_message'];
                $newUrutan = $lastUrutan + 1;

                if ($getWaitingTukang['status_watzap_tukang'] == 'sent') {
                    $newMessage = $this->db->get_where('tb_marketing_message', ['urutan_marketing_message' => $newUrutan, 'target_marketing_message' => 'tukang'])->row_array();

                    if ($newMessage) {
                        $dataWaiting = [
                            'id_tukang' => $id_tukang,
                            'id_marketing_message' => $newMessage['id_marketing_message'],
                            'name_watzap_tukang' => $tukang['nama'],
                            'phone_watzap_tukang' => $tukang['nomorhp'],
                            'status_watzap_tukang' => 'waiting',
                            'updated_at' => date('Y-m-d H:i:s')
                        ];

                        $insert = $this->db->insert('tb_watzap_tukang', $dataWaiting);
                    }
                }
            }
        }
    }

    public function sendTrialTukang()
    {
        // https://order.topmortarindonesia.com/assets/vids/test_video.mp4
        $this->output->set_content_type('application/json');
        $watzapTukang = $this->MWatzaptukang->getSingleWaiting();

        if ($watzapTukang) {

            $name = $watzapTukang['name_watzap_tukang'];
            $phone = $watzapTukang['phone_watzap_tukang'];
            $id_marketing_message = $watzapTukang['id_marketing_message'];

            $marketingMessage = $this->db->get_where('tb_marketing_message', ['id_marketing_message' => $id_marketing_message])->row_array();

            $dataSending = array();
            $dataSending["api_key"] = "OG0UMAWHV6SJ0GL2";
            $dataSending["number_key"] = "liVWjKEiV6aImQ5M";
            $dataSending["phone_no"] = $phone;
            $dataSending["message"] = $marketingMessage['body_marekting_message'];
            $dataSending["url"] = "https://order.topmortarindonesia.com/assets/content_img/" . $marketingMessage['image_marketing_message'];
            $dataSending["separate_caption"] = "0";

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.watzap.id/v1/send_image_url',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($dataSending),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);

            $resArray = json_decode($response, true);

            if ($resArray['status'] == 200) {
                $arrayWatzapTukang = [
                    'send_at' => date('Y-m-d H:i:s'),
                    'status_watzap_tukang' => 'sent',
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $updateStatus = $this->MWatzaptukang->updateFromArray($watzapTukang['id_watzap_tukang'], $arrayWatzapTukang);

                if ($updateStatus) {
                    $dataResponse = [
                        'status' => 'ok',
                        'msg' => 'Succes sent and upadate status'
                    ];

                    $this->output->set_output(json_encode($dataResponse));
                } else {
                    $dataResponse = [
                        'status' => 'failed',
                        'msg' => 'Failed to update status'
                    ];

                    $this->output->set_output(json_encode($dataResponse));
                }
            } else {
                $dataResponse = [
                    'status' => 'failed',
                    'response' => $resArray
                ];

                $this->output->set_output(json_encode($dataResponse));
            }
        } else {
            $dataResponse = [
                'status' => 'failed',
                'msg' => 'No numbers to send'
            ];

            $this->output->set_output(json_encode($dataResponse));
        }
    }
}
