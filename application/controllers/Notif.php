<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Notif extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MCity');
        $this->load->model('MProduk');
        $this->load->model('MSuratJalan');
        $this->load->model('MDetailSuratJalan');
        $this->load->model('MInvoice');
        $this->load->model('MContact');
        $this->load->model('MKendaraan');
        $this->load->model('MUser');
        $this->load->model('MVisit');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['title'] = 'Laporan Toko Passive';
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Notif/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function notif_passive()
    {
        $id_city = $_GET['ct'];

        $data['city'] = $this->MCity->getById($id_city);
        $data['contact_passive'] = $this->db->get_where('tb_contact', ['id_city' => $id_city, 'store_status' => 'passive'])->result_array();
        $data['contact_active'] = $this->db->get_where('tb_contact', ['id_city' => $id_city])->result_array();
        // $html = $this->load->view('Notif/PrintPassive', $data);
        // PDF
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Notif/PrintPassive', $data, true);
        $mpdf->AddPage('P');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    public function send_invoice()
    {
        $this->output->set_content_type('application/json');

        $post = json_decode(file_get_contents('php://input'), true) != null ? json_decode(file_get_contents('php://input'), true) : $this->input->post();

        $id_invoice = $post['id_invoice'];

        $invoice = $this->MInvoice->getById($id_invoice);
        $contact = $this->MContact->getById($invoice['id_contact']);
        $data['invoice'] = $invoice;
        $data['store'] = $this->MContact->getById($invoice['id_contact']);
        $data['kendaraan'] = $this->MKendaraan->getById($invoice['id_kendaraan']);
        $data['courier'] = $this->MUser->getById($invoice['id_courier']);
        $data['produk'] = $this->MDetailSuratJalan->getAll($invoice['id_surat_jalan']);
        $data['id_distributor'] = $contact['id_distributor'];

        $proofClosing = "https://saleswa.topmortarindonesia.com/img/" . $invoice['proof_closing'];

        // Buat direktori penyimpanan sementara
        $folderPath = FCPATH . 'assets/tmp/inv/';
        // Nama file berdasarkan invoice ID + timestamp
        $fileName = 'inv_' . $invoice['id_surat_jalan'] . '_' . time() . '.pdf';
        $filePath = $folderPath . $fileName;

        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Invoice/PrintNotif', $data, true);
        $mpdf->AddPage('P');
        $mpdf->WriteHTML($html);
        $mpdf->Output($filePath, \Mpdf\Output\Destination::FILE);

        // Send Message
        $id_distributor = $contact['id_distributor'];
        $nomorhp = $contact['nomorhp'];
        $nama = $contact['nama'];
        $template_id = "bd507a74-4fdf-4692-8199-eb4ed8864bc7";
        $message = "Berikut adalah invoice pembelian anda.";
        $full_name = "-";
        $templateSj = "7bf2d2a0-bdd5-4c70-ba9f-a9665f66a841";
        $messageSj = "Berikut adalah surat jalan anda";

        $qontak = $this->db->get_where('tb_qontak', ['id_distributor' => $id_distributor])->row_array();

        $wa_token = $qontak['token'];
        $integration_id = $qontak['integration_id'];

        // Send SJ
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://service-chat.qontak.com/api/open/v1/broadcasts/whatsapp/direct',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                        "to_number": "' . $nomorhp . '",
                        "to_name": "' . $nama . '",
                        "message_template_id": "' . $templateSj . '",
                        "channel_integration_id": "' . $integration_id . '",
                        "language": {
                            "code": "id"
                        },
                        "parameters": {
                            "header":{
                                "format":"IMAGE",
                                "params": [
                                    {
                                        "key":"url",
                                        "value":"' . $proofClosing . '"
                                    },
                                    {
                                        "key":"filename",
                                        "value":"' . $invoice['proof_closing'] . '"
                                    }
                                ]
                            },
                            "body": [
                            {
                                "key": "1",
                                "value": "message",
                                "value_text": "' . trim(preg_replace('/\s+/', ' ', $messageSj)) . '"
                            }
                            ]
                        }
                        }',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $wa_token,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $resSj = json_decode($response, true);

        $resDataSj = $resSj['data'];

        $id_msgSj = $resDataSj['id'];

        // Cek Log SJ 5f70dd63-7959-4a1c-8e52-e65a1eb40487
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://service-chat.qontak.com/api/open/v1/broadcasts/' . $id_msgSj . '/whatsapp/log',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $wa_token,
                'Cookie: incap_ses_1756_2992082=Ox9FXS1ko3Vikf0LFJFeGKGyt2gAAAAAQXScjKXeLICe/UQF78vzGQ==; incap_ses_219_2992082=4GjPNG8+XzA1Rt4quwsKA4G1u2gAAAAAWfhLh+XsD0Bo64qAFthTLg==; nlbi_2992082=EiQRTKjoCUbRUjeX3B9AyAAAAAAMWeh7AVkdVtlwZ+4p2rGi; visid_incap_2992082=loW+JnDtRgOZqqa55tsRH55YmWgAAAAAQUIPAAAAAADOFD/DW2Yv8YwghY/luI5g'
            ),
        ));

        $responseLog = curl_exec($curl);

        curl_close($curl);

        $resLogSj = json_decode($responseLog, true);
        $logSjData = $resLogSj['data'];

        if ($logSjData['status'] == 'failed') {
            $dataNotif = [
                'id_surat_jalan' => $invoice['id_surat_jalan'],
                'type_notif_invoice' => 'sj',
                'id_msg' => $id_msgSj,
                'is_sent' => 0
            ];

            $this->db->insert('tb_notif_invoice', $dataNotif);
        }

        // Send Invoice
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://service-chat.qontak.com/api/open/v1/broadcasts/whatsapp/direct',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                        "to_number": "' . $nomorhp . '",
                        "to_name": "' . $nama . '",
                        "message_template_id": "' . $template_id . '",
                        "channel_integration_id": "' . $integration_id . '",
                        "language": {
                            "code": "id"
                        },
                        "parameters": {
                            "header":{
                                "format":"DOCUMENT",
                                "params": [
                                    {
                                        "key":"url",
                                        "value":"https://order.topmortarindonesia.com/assets/tmp/inv/' . $fileName . '"
                                    },
                                    {
                                        "key":"filename",
                                        "value":"' . $fileName . '"
                                    }
                                ]
                            },
                            "body": [
                            {
                                "key": "1",
                                "value": "nama",
                                "value_text": "' . $nama . '"
                            },
                            {
                                "key": "2",
                                "value": "message",
                                "value_text": "' . trim(preg_replace('/\s+/', ' ', $message)) . '"
                            },
                            {
                                "key": "3",
                                "value": "sales",
                                "value_text": "' . $full_name . '"
                            }
                            ]
                        }
                        }',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $wa_token,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $res = json_decode($response, true);

        if ($res['status'] == 'success') {
            $data = $res['data'];
            $id_msgInv = $data['id'];

            // Cek Log 5f70dd63-7959-4a1c-8e52-e65a1eb40487
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://service-chat.qontak.com/api/open/v1/broadcasts/' . $id_msgInv . '/whatsapp/log',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $wa_token,
                    'Cookie: incap_ses_1756_2992082=Ox9FXS1ko3Vikf0LFJFeGKGyt2gAAAAAQXScjKXeLICe/UQF78vzGQ==; incap_ses_219_2992082=4GjPNG8+XzA1Rt4quwsKA4G1u2gAAAAAWfhLh+XsD0Bo64qAFthTLg==; nlbi_2992082=EiQRTKjoCUbRUjeX3B9AyAAAAAAMWeh7AVkdVtlwZ+4p2rGi; visid_incap_2992082=loW+JnDtRgOZqqa55tsRH55YmWgAAAAAQUIPAAAAAADOFD/DW2Yv8YwghY/luI5g'
                ),
            ));

            $responseLog = curl_exec($curl);

            curl_close($curl);

            $resLog = json_decode($responseLog, true);
            $logData = $resLog['data'];

            if ($logData['status'] == 'failed') {
                $dataNotif = [
                    'id_surat_jalan' => $invoice['id_surat_jalan'],
                    'type_notif_invoice' => 'inv',
                    'id_msg' => $id_msgInv,
                    'is_sent' => 0
                ];

                $this->db->insert('tb_notif_invoice', $dataNotif);
            }

            $result = [
                'code' => 200,
                'status' => 'ok',
                'detail' => $res,
                'detailSj' => $resSj
            ];

            return $this->output->set_output(json_encode($result));
        } else {
            $result = [
                'code' => 400,
                'status' => 'failed',
                'detail' => $res,
                'detailSj' => $resSj
            ];

            return $this->output->set_output(json_encode($result));
        }
    }

    public function send_invoice_backup()
    {
        $this->output->set_content_type('application/json');

        $post = json_decode(file_get_contents('php://input'), true) != null ? json_decode(file_get_contents('php://input'), true) : $this->input->post();

        $id_invoice = $post['id_invoice'];

        $invoice = $this->MInvoice->getById($id_invoice);
        $contact = $this->MContact->getById($invoice['id_contact']);
        $data['invoice'] = $invoice;
        $data['store'] = $this->MContact->getById($invoice['id_contact']);
        $data['kendaraan'] = $this->MKendaraan->getById($invoice['id_kendaraan']);
        $data['courier'] = $this->MUser->getById($invoice['id_courier']);
        $data['produk'] = $this->MDetailSuratJalan->getAll($invoice['id_surat_jalan']);
        $data['id_distributor'] = $contact['id_distributor'];

        $proofClosing = "https://saleswa.topmortarindonesia.com/img/" . $invoice['proof_closing'];

        // Buat direktori penyimpanan sementara
        // $folderPath = FCPATH . 'assets/tmp/inv/';
        // Nama file berdasarkan invoice ID + timestamp
        // $fileName = 'inv_' . $invoice['id_surat_jalan'] . '_' . time() . '.pdf';
        // $filePath = $folderPath . $fileName;

        // $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        // $mpdf->SetMargins(0, 0, 5);
        // $html = $this->load->view('Invoice/PrintNotif', $data, true);
        // $mpdf->AddPage('P');
        // $mpdf->WriteHTML($html);
        // $mpdf->Output($filePath, \Mpdf\Output\Destination::FILE);

        // Send Message
        $id_distributor = $contact['id_distributor'];
        $nomorhp = $contact['nomorhp'];
        $nama = $contact['nama'];
        $template_id = "bd507a74-4fdf-4692-8199-eb4ed8864bc7";
        $message = "Berikut adalah invoice pembelian anda.";
        $full_name = "-";
        $templateSj = "7bf2d2a0-bdd5-4c70-ba9f-a9665f66a841";
        $messageSj = "Berikut adalah surat jalan anda";

        $qontak = $this->db->get_where('tb_qontak', ['id_distributor' => $id_distributor])->row_array();

        $wa_token = $qontak['token'];
        $integration_id = $qontak['integration_id'];

        $fileNameSearch = 'inv_' . $invoice['id_surat_jalan'];
        $files = glob(FCPATH . "assets/tmp/inv/" . $fileNameSearch . "*");

        if ($files) {
            $replaceFilePath = str_replace("/home/admin2/web/order.topmortarindonesia.com/public_html/", "https://order.topmortarindonesia.com/", $files[0]);
            echo json_encode($replaceFilePath);

            // Send Invoice
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://service-chat.qontak.com/api/open/v1/broadcasts/whatsapp/direct',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
                            "to_number": "' . $nomorhp . '",
                            "to_name": "' . $nama . '",
                            "message_template_id": "' . $template_id . '",
                            "channel_integration_id": "' . $integration_id . '",
                            "language": {
                                "code": "id"
                            },
                            "parameters": {
                                "header":{
                                    "format":"DOCUMENT",
                                    "params": [
                                        {
                                            "key":"url",
                                            "value":"' . $replaceFilePath . '"
                                        },
                                        {
                                            "key":"filename",
                                            "value":"' . $fileNameSearch . '"
                                        }
                                    ]
                                },
                                "body": [
                                {
                                    "key": "1",
                                    "value": "nama",
                                    "value_text": "' . $nama . '"
                                },
                                {
                                    "key": "2",
                                    "value": "message",
                                    "value_text": "' . trim(preg_replace('/\s+/', ' ', $message)) . '"
                                },
                                {
                                    "key": "3",
                                    "value": "sales",
                                    "value_text": "' . $full_name . '"
                                }
                                ]
                            }
                            }',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $wa_token,
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            $res = json_decode($response, true);

            if ($res['status'] == 'success') {
                $result = [
                    'code' => 200,
                    'status' => 'ok',
                    'detail' => $res
                ];

                return $this->output->set_output(json_encode($result));
            } else {
                $result = [
                    'code' => 400,
                    'status' => 'failed',
                    'detail' => $res
                ];

                return $this->output->set_output(json_encode($result));
            }
        }
    }

    public function send_backup_api()
    {
        $this->output->set_content_type('application/json');

        // $post = json_decode(file_get_contents('php://input'), true) != null ? json_decode(file_get_contents('php://input'), true) : $this->input->post();
        $notifInvoices = $this->db->get_where('tb_notif_invoice', ['is_sent' => 0, 'type_notif_invoice' => 'inv'])->result_array();

        foreach ($notifInvoices as $notifInvoice) {
            $id_surat_jalan = $notifInvoice['id_surat_jalan'];

            $invoice = $this->db->get_where('tb_invoice', ['id_surat_jalan' => $id_surat_jalan])->row_array();

            $id_invoice = $invoice['id_invoice'];

            $invoice = $this->MInvoice->getById($id_invoice);
            $contact = $this->MContact->getById($invoice['id_contact']);
            $data['invoice'] = $invoice;
            $data['store'] = $this->MContact->getById($invoice['id_contact']);
            $data['kendaraan'] = $this->MKendaraan->getById($invoice['id_kendaraan']);
            $data['courier'] = $this->MUser->getById($invoice['id_courier']);
            $data['produk'] = $this->MDetailSuratJalan->getAll($invoice['id_surat_jalan']);
            $data['id_distributor'] = $contact['id_distributor'];

            $proofClosing = "https://saleswa.topmortarindonesia.com/img/" . $invoice['proof_closing'];

            // Buat direktori penyimpanan sementara
            // $folderPath = FCPATH . 'assets/tmp/inv/';
            // Nama file berdasarkan invoice ID + timestamp
            // $fileName = 'inv_' . $invoice['id_surat_jalan'] . '_' . time() . '.pdf';
            // $filePath = $folderPath . $fileName;

            // $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
            // $mpdf->SetMargins(0, 0, 5);
            // $html = $this->load->view('Invoice/PrintNotif', $data, true);
            // $mpdf->AddPage('P');
            // $mpdf->WriteHTML($html);
            // $mpdf->Output($filePath, \Mpdf\Output\Destination::FILE);

            // Send Message
            $id_distributor = $contact['id_distributor'];
            $nomorhp = $contact['nomorhp'];
            $nama = $contact['nama'];
            $template_id = "bd507a74-4fdf-4692-8199-eb4ed8864bc7";
            $message = "Berikut adalah invoice pembelian anda.";
            $full_name = "-";
            $templateSj = "7bf2d2a0-bdd5-4c70-ba9f-a9665f66a841";
            $messageSj = "Berikut adalah surat jalan anda";

            $qontak = $this->db->get_where('tb_qontak', ['id_distributor' => $id_distributor])->row_array();

            $wa_token = $qontak['token'];
            $integration_id = $qontak['integration_id'];

            $fileNameSearch = 'inv_' . $invoice['id_surat_jalan'];
            $files = glob(FCPATH . "assets/tmp/inv/" . $fileNameSearch . "*");

            if ($files) {
                $replaceFilePath = str_replace("/home/admin2/web/order.topmortarindonesia.com/public_html/", "https://order.topmortarindonesia.com/", $files[0]);
                // echo json_encode($replaceFilePath);

                // Send Invoice
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://service-chat.qontak.com/api/open/v1/broadcasts/whatsapp/direct',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => '{
                                "to_number": "' . $nomorhp . '",
                                "to_name": "' . $nama . '",
                                "message_template_id": "' . $template_id . '",
                                "channel_integration_id": "' . $integration_id . '",
                                "language": {
                                    "code": "id"
                                },
                                "parameters": {
                                    "header":{
                                        "format":"DOCUMENT",
                                        "params": [
                                            {
                                                "key":"url",
                                                "value":"' . $replaceFilePath . '"
                                            },
                                            {
                                                "key":"filename",
                                                "value":"' . $fileNameSearch . '"
                                            }
                                        ]
                                    },
                                    "body": [
                                    {
                                        "key": "1",
                                        "value": "nama",
                                        "value_text": "' . $nama . '"
                                    },
                                    {
                                        "key": "2",
                                        "value": "message",
                                        "value_text": "' . trim(preg_replace('/\s+/', ' ', $message)) . '"
                                    },
                                    {
                                        "key": "3",
                                        "value": "sales",
                                        "value_text": "' . $full_name . '"
                                    }
                                    ]
                                }
                                }',
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: Bearer ' . $wa_token,
                        'Content-Type: application/json'
                    ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);

                $res = json_decode($response, true);
                $resData = $res['data'];

                if ($res['status'] == 'success') {
                    $id_msg = $resData['id'];
                    // Cek Log 5f70dd63-7959-4a1c-8e52-e65a1eb40487
                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://service-chat.qontak.com/api/open/v1/broadcasts/' . $id_msg . '/whatsapp/log',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_HTTPHEADER => array(
                            'Authorization: Bearer ' . $wa_token,
                            'Cookie: incap_ses_1756_2992082=Ox9FXS1ko3Vikf0LFJFeGKGyt2gAAAAAQXScjKXeLICe/UQF78vzGQ==; incap_ses_219_2992082=4GjPNG8+XzA1Rt4quwsKA4G1u2gAAAAAWfhLh+XsD0Bo64qAFthTLg==; nlbi_2992082=EiQRTKjoCUbRUjeX3B9AyAAAAAAMWeh7AVkdVtlwZ+4p2rGi; visid_incap_2992082=loW+JnDtRgOZqqa55tsRH55YmWgAAAAAQUIPAAAAAADOFD/DW2Yv8YwghY/luI5g'
                        ),
                    ));

                    $responseLog = curl_exec($curl);

                    curl_close($curl);

                    $resLog = json_decode($responseLog, true);
                    $logData = $resLog['data'];

                    if ($logData['status'] != 'failed') {
                        $notifInvoiceData = [
                            'id_surat_jalan' => $id_surat_jalan,
                            'id_msg' => $id_msg,
                            'is_sent' => 1,
                        ];

                        $this->db->update('tb_notif_invoice', $notifInvoiceData, ['id_surat_jalan' => $id_surat_jalan, 'type_notif_invoice' => 'inv']);
                    }

                    $result = [
                        'code' => 200,
                        'status' => 'ok',
                        'detail' => $res
                    ];

                    $this->output->set_output(json_encode($result));
                }
            }
        }
    }

    public function send_sj_backup()
    {
        $this->output->set_content_type('application/json');

        // $post = json_decode(file_get_contents('php://input'), true) != null ? json_decode(file_get_contents('php://input'), true) : $this->input->post();
        $notifInvoices = $this->db->get_where('tb_notif_invoice', ['is_sent' => 0, 'type_notif_invoice' => 'sj'])->result_array();

        foreach ($notifInvoices as $notifInvoice) {
            $id_surat_jalan = $notifInvoice['id_surat_jalan'];

            $invoice = $this->db->get_where('tb_invoice', ['id_surat_jalan' => $id_surat_jalan])->row_array();

            $id_invoice = $invoice['id_invoice'];

            $invoice = $this->MInvoice->getById($id_invoice);
            $contact = $this->MContact->getById($invoice['id_contact']);
            $data['invoice'] = $invoice;
            $data['store'] = $this->MContact->getById($invoice['id_contact']);
            $data['kendaraan'] = $this->MKendaraan->getById($invoice['id_kendaraan']);
            $data['courier'] = $this->MUser->getById($invoice['id_courier']);
            $data['produk'] = $this->MDetailSuratJalan->getAll($invoice['id_surat_jalan']);
            $data['id_distributor'] = $contact['id_distributor'];

            $proofClosing = "https://saleswa.topmortarindonesia.com/img/" . $invoice['proof_closing'];

            // Send Message
            $id_distributor = $contact['id_distributor'];
            $nomorhp = $contact['nomorhp'];
            $nama = $contact['nama'];
            $templateSj = "7bf2d2a0-bdd5-4c70-ba9f-a9665f66a841";
            $messageSj = "Berikut adalah surat jalan anda";

            $qontak = $this->db->get_where('tb_qontak', ['id_distributor' => $id_distributor])->row_array();

            $wa_token = $qontak['token'];
            $integration_id = $qontak['integration_id'];

            // if ($files) {

            // Send SJ
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://service-chat.qontak.com/api/open/v1/broadcasts/whatsapp/direct',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
                        "to_number": "' . $nomorhp . '",
                        "to_name": "' . $nama . '",
                        "message_template_id": "' . $templateSj . '",
                        "channel_integration_id": "' . $integration_id . '",
                        "language": {
                            "code": "id"
                        },
                        "parameters": {
                            "header":{
                                "format":"IMAGE",
                                "params": [
                                    {
                                        "key":"url",
                                        "value":"' . $proofClosing . '"
                                    },
                                    {
                                        "key":"filename",
                                        "value":"' . $invoice['proof_closing'] . '"
                                    }
                                ]
                            },
                            "body": [
                            {
                                "key": "1",
                                "value": "message",
                                "value_text": "' . trim(preg_replace('/\s+/', ' ', $messageSj)) . '"
                            }
                            ]
                        }
                        }',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $wa_token,
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            $res = json_decode($response, true);
            $resData = $res['data'];

            if ($res['status'] == 'success') {
                $id_msg = $resData['id'];
                // Cek Log 5f70dd63-7959-4a1c-8e52-e65a1eb40487
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://service-chat.qontak.com/api/open/v1/broadcasts/' . $id_msg . '/whatsapp/log',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: Bearer ' . $wa_token,
                        'Cookie: incap_ses_1756_2992082=Ox9FXS1ko3Vikf0LFJFeGKGyt2gAAAAAQXScjKXeLICe/UQF78vzGQ==; incap_ses_219_2992082=4GjPNG8+XzA1Rt4quwsKA4G1u2gAAAAAWfhLh+XsD0Bo64qAFthTLg==; nlbi_2992082=EiQRTKjoCUbRUjeX3B9AyAAAAAAMWeh7AVkdVtlwZ+4p2rGi; visid_incap_2992082=loW+JnDtRgOZqqa55tsRH55YmWgAAAAAQUIPAAAAAADOFD/DW2Yv8YwghY/luI5g'
                    ),
                ));

                $responseLog = curl_exec($curl);

                curl_close($curl);

                $resLog = json_decode($responseLog, true);
                $logData = $resLog['data'];

                if ($logData['status'] != 'failed') {
                    $notifInvoiceData = [
                        'id_surat_jalan' => $id_surat_jalan,
                        'id_msg' => $id_msg,
                        'is_sent' => 1,
                    ];

                    $this->db->update('tb_notif_invoice', $notifInvoiceData, ['id_surat_jalan' => $id_surat_jalan, 'type_notif_invoice' => 'sj']);
                }

                $result = [
                    'code' => 200,
                    'status' => 'ok',
                    'detail' => $res
                ];

                $this->output->set_output(json_encode($result));
            }
            // }
        }
    }

    public function cekNotifLog()
    {
        $notifInvoices = $this->db->get_where('tb_notif_invoice', ['is_sent' => 1])->result_array();

        foreach ($notifInvoices as $notifInvoice) {
            $id_surat_jalan = $notifInvoice['id_surat_jalan'];

            $invoice = $this->db->get_where('tb_invoice', ['id_surat_jalan' => $id_surat_jalan])->row_array();

            $id_invoice = $invoice['id_invoice'];

            $invoice = $this->MInvoice->getById($id_invoice);
            $contact = $this->MContact->getById($invoice['id_contact']);

            // Send Message
            $id_distributor = $contact['id_distributor'];

            $qontak = $this->db->get_where('tb_qontak', ['id_distributor' => $id_distributor])->row_array();

            $wa_token = $qontak['token'];
            $integration_id = $qontak['integration_id'];

            // Cek Log 5f70dd63-7959-4a1c-8e52-e65a1eb40487
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://service-chat.qontak.com/api/open/v1/broadcasts/' . $notifInvoice['id_msg'] . '/whatsapp/log',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $wa_token,
                    'Cookie: incap_ses_1756_2992082=Ox9FXS1ko3Vikf0LFJFeGKGyt2gAAAAAQXScjKXeLICe/UQF78vzGQ==; incap_ses_219_2992082=4GjPNG8+XzA1Rt4quwsKA4G1u2gAAAAAWfhLh+XsD0Bo64qAFthTLg==; nlbi_2992082=EiQRTKjoCUbRUjeX3B9AyAAAAAAMWeh7AVkdVtlwZ+4p2rGi; visid_incap_2992082=loW+JnDtRgOZqqa55tsRH55YmWgAAAAAQUIPAAAAAADOFD/DW2Yv8YwghY/luI5g'
                ),
            ));

            $responseLog = curl_exec($curl);

            curl_close($curl);

            $resLog = json_decode($responseLog, true);
            $logData = $resLog['data'][0];

            if ($logData['status'] == 'failed') {
                $notifInvoiceData = [
                    'id_surat_jalan' => $id_surat_jalan,
                    'id_msg' => $notifInvoice['id_msg'],
                    'is_sent' => 0,
                ];

                $this->db->update('tb_notif_invoice', $notifInvoiceData, ['id_surat_jalan' => $id_surat_jalan, 'type_notif_invoice' => $notifInvoice['type_notif_invoice']]);
            }
        }
    }
}
