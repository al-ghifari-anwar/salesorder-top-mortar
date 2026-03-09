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

        // Contact
        $contact = $this->MContact->getByNomorhp($nomorhp);

        $id_contact = $contact['id_contact'];
        $id_distributor = $contact['id_distributor'];

        // Last Visit
        $lastVisit = $this->db->get_where('tb_visit', ['id_contact' => $id_contact, 'DATE(date_visit)' => date('Y-m-d')])->row_array();

        // AI Agent
        $aiAgent = $this->db->get_where('tb_ai_agent', ['id_distributor' => $id_distributor])->row_array();

        // Voucher
        $vouchers = $this->MVoucher->getByIdContactForHaloAI($id_contact);

        $vouchersStr = "";
        $voucherExp = "";
        foreach ($vouchers as $voucher) {
            $vouchersStr .= $voucher['no_voucher'] . ",";
            // if (!empty($voucherExp)) {
            $voucherExp = date('d F Y', strtotime($voucher['exp_date']));
            // }
            // $voucherExp = date('d F Y', strtotime($voucher['exp_date']));
        }

        $jmlVoucher = count($vouchers) . "";

        $postData = [
            'model-ai' => 'gpt-40-mini',
            'temperature_ai' => 0.4,
            'max_output_token' => 400,
            'toko' => [
                'nama' => $contact['nama'],
                'pemilik' => $contact['store_owner'],
                'tanggal_terakhir_dikunjungi' => date("Y-m-d", strtotime($lastVisit['date_visit'])),
                'status' => $contact['store_status'],
                'jml_voucher' => $jmlVoucher,
                'voucher_expired' => $voucherExp,
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
                $giveVoucher = isset($responseData['give_voucher']) ? $responseData['give_voucher'] : null;

                if ($giveVoucher != null) {
                    if ($giveVoucher == 'yes') {
                        // Kasih voucher
                        $curl = curl_init();
                        $id_contact = $contact['id_contact'];
                        $jml_voucher = 1;

                        curl_setopt_array($curl, array(
                            CURLOPT_URL => 'https://saleswa.topmortarindonesia.com/insertVoucher.php?j=' . $jml_voucher . '&s=' . $id_contact . '&t=m',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'GET',
                        ));

                        $response = curl_exec($curl);

                        curl_close($curl);

                        $res = json_decode($response, true);

                        $statusVc = $res['status'];

                        if ($statusVc == 'ok') {
                            // Get HaloAI
                            $id_distributor = $this->session->userdata('id_distributor');
                            $haloai = $this->db->get_where('tb_haloai', ['id_distributor' => $id_distributor])->row_array();
                            $wa_token = $haloai['token_haloai'];
                            $business_id = $haloai['business_id_haloai'];
                            $channel_id = $haloai['channel_id_haloai'];
                            $template = 'notif_voucher_1';

                            // Get Vouchers
                            $dateNow = date("Y-m-d");
                            $getVoucher = $this->db->query("SELECT * FROM tb_voucher WHERE id_contact = '$id_contact' AND is_claimed = 0 AND date_voucher LIKE '%$dateNow%' ")->result_array();
                            $vouchers = "";
                            foreach ($getVoucher as $voucherArr) {
                                $vouchers .= $voucherArr['no_voucher'] . ",";
                            }

                            $message = "Hallo " . $contact['nama'] . " Selamat bergabung kembali di keluarga besar Top Mortar! Nikmati layanan 'Pesan Hari Ini, Kirim Hari Ini', dan promo-promo member ekslusif lainnya. Bersama Top Mortar, mari kita maju bersama! Anda mendapatkan " . $jml_voucher . " buah Voucher no seri: " . $vouchers . ".  Tukarkan voucher anda dengan gratis Perekat Bata Ringan sebelum tanggal *" .  date("d M, Y", strtotime("+30 days")) . "* ";

                            $haloaiPayload = [
                                'activate_ai_after_send' => false,
                                'channel_id' => $channel_id,
                                "fallback_template_header" => [
                                    'filename' => "send_voucher.mp4",
                                    'type' => 'video',
                                    'url' => "https://saleswa.topmortarindonesia.com/vids/send_voucher.mp4",
                                ],
                                'fallback_template_message' => $template,
                                'fallback_template_variables' => [
                                    $contact['nama'],
                                    trim(preg_replace('/\s+/', ' ', $jml_voucher)),
                                    $vouchers,
                                    date("d M, Y", strtotime("+30 days")),
                                ],
                                "media" => [
                                    'filename' => "send_voucher.mp4",
                                    'type' => 'video',
                                    'url' => "https://saleswa.topmortarindonesia.com/vids/send_voucher.mp4",
                                ],
                                'phone_number' => $contact['nomorhp'],
                                'text' => trim(preg_replace('/\s+/', ' ', $message)),
                            ];

                            $curl = curl_init();

                            curl_setopt_array($curl, array(
                                CURLOPT_URL => 'https://www.haloai.co.id/api/open/channel/whatsapp/v1/sendMessageByPhoneSync',
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => '',
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 0,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => 'POST',
                                CURLOPT_POSTFIELDS => json_encode($haloaiPayload),
                                CURLOPT_HTTPHEADER => array(
                                    'Authorization: Bearer ' . $wa_token,
                                    'X-HaloAI-Business-Id: ' . $business_id,
                                    'Content-Type: application/json'
                                ),
                            ));

                            $response = curl_exec($curl);

                            curl_close($curl);

                            $res = json_decode($response, true);

                            $status = $res['status'];

                            if ($status == 'success') {
                                $result = [
                                    'code' => 200,
                                    'status' => 'success',
                                    'msg' => 'AI Success response saved, voucher yes, notif success',
                                    'notif' => $res,
                                ];

                                return $this->output->set_output(json_encode($result));
                            } else {
                                $result = [
                                    'code' => 400,
                                    'status' => 'failed',
                                    'msg' => 'AI Success response saved, voucher yes failed, notif failed',
                                    'notif' => $res,
                                ];

                                return $this->output->set_output(json_encode($result));
                            }
                        } else {
                            $result = [
                                'code' => 400,
                                'status' => 'failed',
                                'msg' => 'AI Success response saved, voucher failed, notif success',
                                'notif' => $res,
                            ];

                            return $this->output->set_output(json_encode($result));
                        }
                    } else {
                        // Get HaloAI
                        $id_distributor = $contact['id_distributor'];
                        $haloai = $this->db->get_where('tb_haloai', ['id_distributor' => $id_distributor])->row_array();
                        $wa_token = $haloai['token_haloai'];
                        $business_id = $haloai['business_id_haloai'];
                        $channel_id = $haloai['channel_id_haloai'];
                        $template = 'info_meeting_baru';

                        $message = $responseData['rekomendasi_wa'];

                        $haloaiPayload = [
                            'activate_ai_after_send' => false,
                            'channel_id' => $channel_id,
                            'fallback_template_message' => $template,
                            'fallback_template_variables' => [
                                $contact['nama'],
                                trim(preg_replace('/\s+/', ' ', $message)),
                                "PT Top Mortar Indonesia",
                            ],
                            'phone_number' => $contact['nomorhp'],
                            'text' => trim(preg_replace('/\s+/', ' ', $message)),
                        ];

                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                            CURLOPT_URL => 'https://www.haloai.co.id/api/open/channel/whatsapp/v1/sendMessageByPhoneSync',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => json_encode($haloaiPayload),
                            CURLOPT_HTTPHEADER => array(
                                'Authorization: Bearer ' . $wa_token,
                                'X-HaloAI-Business-Id: ' . $business_id,
                                'Content-Type: application/json'
                            ),
                        ));

                        $response = curl_exec($curl);

                        curl_close($curl);

                        $res = json_decode($response, true);

                        $status = $res['status'];

                        if ($status == 'success') {
                            $result = [
                                'code' => 200,
                                'status' => 'success',
                                'msg' => 'AI Success response saved, voucher no, notif success',
                                'notif' => $res,
                            ];

                            return $this->output->set_output(json_encode($result));
                        } else {
                            $result = [
                                'code' => 400,
                                'status' => 'failed',
                                'msg' => 'AI Success response saved, voucher no, notif failed',
                                'notif' => $res,
                            ];

                            return $this->output->set_output(json_encode($result));
                        }
                    }
                } else {
                    // Get HaloAI
                    $id_distributor = $contact['id_distributor'];
                    $haloai = $this->db->get_where('tb_haloai', ['id_distributor' => $id_distributor])->row_array();
                    $wa_token = $haloai['token_haloai'];
                    $business_id = $haloai['business_id_haloai'];
                    $channel_id = $haloai['channel_id_haloai'];
                    $template = 'info_meeting_baru';

                    $message = $responseData['rekomendasi_wa'];

                    $haloaiPayload = [
                        'activate_ai_after_send' => false,
                        'channel_id' => $channel_id,
                        'fallback_template_message' => $template,
                        'fallback_template_variables' => [
                            $contact['nama'],
                            trim(preg_replace('/\s+/', ' ', $message)),
                            "PT Top Mortar Indonesia",
                        ],
                        'phone_number' => $contact['nomorhp'],
                        'text' => trim(preg_replace('/\s+/', ' ', $message)),
                    ];

                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://www.haloai.co.id/api/open/channel/whatsapp/v1/sendMessageByPhoneSync',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => json_encode($haloaiPayload),
                        CURLOPT_HTTPHEADER => array(
                            'Authorization: Bearer ' . $wa_token,
                            'X-HaloAI-Business-Id: ' . $business_id,
                            'Content-Type: application/json'
                        ),
                    ));

                    $response = curl_exec($curl);

                    curl_close($curl);

                    $res = json_decode($response, true);

                    $status = $res['status'];

                    if ($status == 'success') {
                        $result = [
                            'code' => 200,
                            'status' => 'success',
                            'msg' => 'AI Success response saved, voucher null, notif success',
                            'notif' => $res,
                        ];

                        return $this->output->set_output(json_encode($result));
                    } else {
                        $result = [
                            'code' => 400,
                            'status' => 'failed',
                            'msg' => 'AI Success response saved, voucher null, notif failed',
                            'notif' => $res,
                        ];

                        return $this->output->set_output(json_encode($result));
                    }
                }
                // $result = [
                //     'code' => 200,
                //     'status' => 'success',
                //     'msg' => 'AI Success response saved',
                // ];

                // return $this->output->set_output(json_encode($result));
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
                'detail' => json_encode($res),
            ];

            return $this->output->set_output(json_encode($result));
        }
    }
}
