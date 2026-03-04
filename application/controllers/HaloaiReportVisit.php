<?php

class HaloaiReportVisit extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->output->set_content_type('application/json');
        $this->load->model('MContact');
        $this->load->model('MUser');
        $this->load->model('MSuratJalan');
        $this->load->model('MDetailSuratJalan');
        $this->load->model('MInvoice');
        $this->load->model('MPayment');
        $this->load->model('MVoucher');
        $this->load->model('HTelegram');
    }

    public function process()
    {
        $post = json_decode(file_get_contents('php://input'), true) != null ? json_decode(file_get_contents('php://input'), true) : $this->input->post();

        $nomorhp = $post['nomorhp'];

        $contact = $this->MContact->getByNomorhp($nomorhp);

        $id_contact = $contact['id_contact'];
        $id_distributor = $contact['id_distributor'];

        $lastVisit = $this->db->get_where('tb_visit', ['id_contact' => $id_contact, 'DATE(date_visit)' => date('Y-m-d')])->row_array();

        $aiAgent = $this->db->get_where('tb_ai_agent', ['id_distributor' => $id_distributor])->row_array();

        $postData = [
            'temperature_ai' => 0.4,
            'max_output_token' => 400,
            'toko' => [
                'nama' => $contact['nama'],
                'pemilik' => $contact['store_owner'],
                'tanggal_terakhir_dikunjungi' => date("Y-m-d", $lastVisit['date_visit']),
            ],
            'laporan_sales' => $lastVisit['laporan_visit'],
            'base64_system_prompt' => $aiAgent['base64_prompt'],
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://topmortaropenaibridge.vercel.app/analyze',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $res = json_decode($response, true);

        if ($res['code'] == 200) {
            $responseData = $res['data'];

            $aiReportvisitData = [
                'id_contact' => $contact['id_contact'],
                'id_visit' => $lastVisit['id_visit'],
                'id_user' => $lastVisit['id_user'],
                'analisis_spv' => $responseData['analisis_spv'],
                'saran_strategi' => $responseData['saran_strategi'],
                'rekomendasi_wa' => $responseData['rekomendasi_wa'],
                'raw' => $responseData['raw'],
                'model_ai' => $responseData['model_ai'],
                'temperature_ai' => $responseData['temperature_ai'],
                'max_output_token' => $responseData['max_output_token'],
                'response_duration' => $responseData['response_duration'],
            ];

            $save = $this->db->insert('tb_ai_reportvisit', $aiReportvisitData);

            if ($save) {
                $result = [
                    'code' => 200,
                    'status' => 'success',
                    'msg' => 'AI Success response saved',
                ];

                return $this->output->set_output(json_encode($result));
            } else {
                $result = [
                    'code' => 400,
                    'status' => 'failed',
                    'msg' => 'AI response not saved',
                    'detail' => $this->db->error(),
                ];

                return $this->output->set_output(json_encode($result));
            }
        } else {
            $result = [
                'code' => 400,
                'status' => 'failed',
                'msg' => 'AI failed',
            ];

            return $this->output->set_output(json_encode($result));
        }
    }
}
