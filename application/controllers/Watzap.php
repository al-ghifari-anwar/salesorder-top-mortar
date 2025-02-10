<?php

class Watzap extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model('MWatzaptukang');
    }

    public function sendTrialTukang()
    {
        $this->output->set_content_type('application/json');
        $watzapTukang = $this->MWatzaptukang->getSingleWaiting();

        if ($watzapTukang) {

            $name = $watzapTukang['name_watzap_tukang'];
            $phone = $watzapTukang['phone_watzap_tukang'];

            $dataSending = array();
            $dataSending["api_key"] = "OG0UMAWHV6SJ0GL2";
            $dataSending["number_key"] = "liVWjKEiV6aImQ5M";
            $dataSending["phone_no"] = $phone;
            $dataSending["message"] = "Halo " . $name . ", ini adalah trial WA";

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.watzap.id/v1/send_message',
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
