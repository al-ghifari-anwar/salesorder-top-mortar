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
        $data['menuGroup'] = 'Analisa';
        $data['menu'] = 'Notif';
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
        $data['contact_active'] = $this->db->get_where('tb_contact', ['id_city' => $id_city, 'store_status' => 'active'])->result_array();
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
        $folderPathInv = FCPATH . 'assets/tmp/inv/';
        // Nama file berdasarkan invoice ID + timestamp
        $fileNameInv = 'inv_' . $invoice['id_surat_jalan'] . '_' . time() . '.pdf';
        $filePathInv = $folderPathInv . $fileNameInv;
        $fileUrlInv = "https://order.topmortarindonesia.com/assets/tmp/inv/" . $fileNameInv;

        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Invoice/PrintNotif', $data, true);
        $mpdf->AddPage('P');
        $mpdf->WriteHTML($html);
        $mpdf->Output($filePathInv, \Mpdf\Output\Destination::FILE);

        // Send Message
        $id_distributor = $contact['id_distributor'];
        $nomorhp = $contact['nomorhp'];
        $nama = $contact['nama'];
        $full_name = "PT Top Mortar Indonesia";


        $haloai = $this->db->get_where('tb_haloai', ['id_distributor' => $id_distributor])->row_array();
        $wa_token = $haloai['token_haloai'];
        $business_id = $haloai['business_id_haloai'];
        $channel_id = $haloai['channel_id_haloai'];
        $templateSj = 'notif_materi_img';
        $templateInv = 'notif_materi_pdf';

        $messageSj = "Berikut adalah surat jalan anda";
        $messageInv = "Berikut adalah invoice pembelian anda.";

        // Send SJ
        $haloaiPayload = [
            'activate_ai_after_send' => false,
            'channel_id' => $channel_id,
            "fallback_template_header" => [
                'filename' => $invoice['proof_closing'],
                'type' => 'image',
                'url' => $proofClosing,
            ],
            'fallback_template_message' => $templateSj,
            'fallback_template_variables' => [
                trim(preg_replace('/\s+/', ' ', $messageSj)),
            ],
            "media" => [
                'filename' => $invoice['proof_closing'],
                'type' => 'image',
                'url' => $proofClosing,
            ],
            'phone_number' => $nomorhp,
            'text' => trim(preg_replace('/\s+/', ' ', $messageSj)),
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

        $responseSj = curl_exec($curl);

        curl_close($curl);

        $resSj = json_decode($responseSj, true);

        if ($resSj['status'] == 'error') {
            $dataNotif = [
                'id_surat_jalan' => $invoice['id_surat_jalan'],
                'type_notif_invoice' => 'sj',
                'file_notif_invoice' => $proofClosing,
                'id_msg' => '-',
                'is_sent' => 0
            ];

            $this->db->insert('tb_notif_invoice', $dataNotif);
        } else {
            if ($resSj['delivery_status'] != 'failed') {
                $dataNotif = [
                    'id_surat_jalan' => $invoice['id_surat_jalan'],
                    'type_notif_invoice' => 'sj',
                    'file_notif_invoice' => $proofClosing,
                    'id_msg' => '-',
                    'is_sent' => 1
                ];

                $this->db->insert('tb_notif_invoice', $dataNotif);
            } else {
                $dataNotif = [
                    'id_surat_jalan' => $invoice['id_surat_jalan'],
                    'type_notif_invoice' => 'sj',
                    'file_notif_invoice' => $proofClosing,
                    'id_msg' => '-',
                    'is_sent' => 0
                ];

                $this->db->insert('tb_notif_invoice', $dataNotif);
            }
        }


        // Send Invoice
        $haloaiPayload = [
            'activate_ai_after_send' => false,
            'channel_id' => $channel_id,
            "fallback_template_header" => [
                'filename' => $fileUrlInv,
                'type' => 'document',
                'url' => $filePathInv,
            ],
            'fallback_template_message' => $templateInv,
            'fallback_template_variables' => [
                trim(preg_replace('/\s+/', ' ', $messageInv)),
            ],
            "media" => [
                'filename' => $fileUrlInv,
                'type' => 'document',
                'url' => $filePathInv,
            ],
            'phone_number' => $nomorhp,
            'text' => trim(preg_replace('/\s+/', ' ', $messageInv)),
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

        $responseInv = curl_exec($curl);

        curl_close($curl);

        $resInv = json_decode($responseInv, true);

        if ($resInv['status'] == 'error') {
            $dataNotif = [
                'id_surat_jalan' => $invoice['id_surat_jalan'],
                'type_notif_invoice' => 'inv',
                'file_notif_invoice' => $fileUrlInv,
                'id_msg' => '-',
                'is_sent' => 0
            ];

            $this->db->insert('tb_notif_invoice', $dataNotif);
        } else {
            if ($resInv['delivery_status'] != 'failed') {
                $dataNotif = [
                    'id_surat_jalan' => $invoice['id_surat_jalan'],
                    'type_notif_invoice' => 'inv',
                    'file_notif_invoice' => $fileUrlInv,
                    'id_msg' => '-',
                    'is_sent' => 1
                ];

                $this->db->insert('tb_notif_invoice', $dataNotif);
            } else {
                $dataNotif = [
                    'id_surat_jalan' => $invoice['id_surat_jalan'],
                    'type_notif_invoice' => 'inv',
                    'file_notif_invoice' => $fileUrlInv,
                    'id_msg' => '-',
                    'is_sent' => 0
                ];

                $this->db->insert('tb_notif_invoice', $dataNotif);
            }
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

        // Send Message
        $id_distributor = $contact['id_distributor'];
        $nomorhp = $contact['nomorhp'];
        $nama = $contact['nama'];
        $full_name = "PT Top Mortar Indonesia";

        $haloai = $this->db->get_where('tb_haloai', ['id_distributor' => $id_distributor])->row_array();
        $wa_token = $haloai['token_haloai'];
        $business_id = $haloai['business_id_haloai'];
        $channel_id = $haloai['channel_id_haloai'];
        $templateInv = 'notif_materi_pdf';

        $messageInv = "Berikut adalah invoice pembelian anda.";

        $fileNameSearch = 'inv_' . $invoice['id_surat_jalan'];
        $files = glob(FCPATH . "assets/tmp/inv/" . $fileNameSearch . "*");

        if ($files) {
            $replaceFilePath = str_replace("/home/admin2/web/order.topmortarindonesia.com/public_html/", "https://order.topmortarindonesia.com/", $files[0]);
            // echo json_encode($replaceFilePath);

            // Send Invoice
            $haloaiPayload = [
                'activate_ai_after_send' => false,
                'channel_id' => $channel_id,
                "fallback_template_header" => [
                    'filename' => $fileNameSearch,
                    'type' => 'document',
                    'url' => $replaceFilePath,
                ],
                'fallback_template_message' => $templateInv,
                'fallback_template_variables' => [
                    trim(preg_replace('/\s+/', ' ', $messageInv)),
                ],
                "media" => [
                    'filename' => $fileNameSearch,
                    'type' => 'document',
                    'url' => $replaceFilePath,
                ],
                'phone_number' => $nomorhp,
                'text' => trim(preg_replace('/\s+/', ' ', $messageInv)),
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

            $responseInv = curl_exec($curl);

            curl_close($curl);

            $resInv = json_decode($responseInv, true);

            if ($resInv['status'] == 'success') {
                if ($resInv['delivery_status'] != 'failed') {
                    $result = [
                        'code' => 200,
                        'status' => 'ok',
                        'detail' => $resInv
                    ];

                    return $this->output->set_output(json_encode($result));
                } else {
                    $result = [
                        'code' => 400,
                        'status' => 'failed',
                        'detail' => $resInv
                    ];

                    return $this->output->set_output(json_encode($result));
                }
            } else {
                $result = [
                    'code' => 400,
                    'status' => 'failed',
                    'detail' => $resInv
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

            // Send Message
            $id_distributor = $contact['id_distributor'];
            $nomorhp = $contact['nomorhp'];
            $nama = $contact['nama'];
            $full_name = "PT Top Mortar Indonesia";

            $haloai = $this->db->get_where('tb_haloai', ['id_distributor' => $id_distributor])->row_array();
            $wa_token = $haloai['token_haloai'];
            $business_id = $haloai['business_id_haloai'];
            $channel_id = $haloai['channel_id_haloai'];
            $templateInv = 'notif_materi_pdf';

            $messageInv = "Berikut adalah invoice pembelian anda.";

            $fileNameSearch = 'inv_' . $invoice['id_surat_jalan'];
            $files = glob(FCPATH . "assets/tmp/inv/" . $fileNameSearch . "*");

            if ($files) {
                $replaceFilePath = str_replace("/home/admin2/web/order.topmortarindonesia.com/public_html/", "https://order.topmortarindonesia.com/", $files[0]);
                // echo json_encode($replaceFilePath);

                // Send Invoice
                $haloaiPayload = [
                    'activate_ai_after_send' => false,
                    'channel_id' => $channel_id,
                    "fallback_template_header" => [
                        'filename' => $fileNameSearch,
                        'type' => 'document',
                        'url' => $replaceFilePath,
                    ],
                    'fallback_template_message' => $templateInv,
                    'fallback_template_variables' => [
                        trim(preg_replace('/\s+/', ' ', $messageInv)),
                    ],
                    "media" => [
                        'filename' => $fileNameSearch,
                        'type' => 'document',
                        'url' => $replaceFilePath,
                    ],
                    'phone_number' => $nomorhp,
                    'text' => trim(preg_replace('/\s+/', ' ', $messageInv)),
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

                $responseInv = curl_exec($curl);

                curl_close($curl);

                $resInv = json_decode($responseInv, true);

                if ($resInv['status'] == 'error') {
                    $dataNotif = [
                        'is_sent' => 0
                    ];

                    $this->db->update('tb_notif_invoice', $dataNotif, ['id_surat_jalan' => $id_surat_jalan, 'type_notif_invoice' => 'inv']);
                } else {
                    if ($resInv['delivery_status'] != 'failed') {
                        $dataNotif = [
                            'is_sent' => 1
                        ];

                        $this->db->update('tb_notif_invoice', $dataNotif, ['id_surat_jalan' => $id_surat_jalan, 'type_notif_invoice' => 'inv']);
                    } else {
                        $dataNotif = [
                            'is_sent' => 0
                        ];

                        $this->db->update('tb_notif_invoice', $dataNotif, ['id_surat_jalan' => $id_surat_jalan, 'type_notif_invoice' => 'inv']);
                    }
                }
            }
        }
    }

    public function send_sj_backup()
    {
        $this->output->set_content_type('application/json');

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

            $haloai = $this->db->get_where('tb_haloai', ['id_distributor' => $id_distributor])->row_array();
            $wa_token = $haloai['token_haloai'];
            $business_id = $haloai['business_id_haloai'];
            $channel_id = $haloai['channel_id_haloai'];
            $templateSj = 'notif_materi_img';

            $messageSj = "Berikut adalah surat jalan anda";

            // Send SJ
            $haloaiPayload = [
                'activate_ai_after_send' => false,
                'channel_id' => $channel_id,
                "fallback_template_header" => [
                    'filename' => $invoice['proof_closing'],
                    'type' => 'image',
                    'url' => $proofClosing,
                ],
                'fallback_template_message' => $templateSj,
                'fallback_template_variables' => [
                    trim(preg_replace('/\s+/', ' ', $messageSj)),
                ],
                "media" => [
                    'filename' => $invoice['proof_closing'],
                    'type' => 'image',
                    'url' => $proofClosing,
                ],
                'phone_number' => $nomorhp,
                'text' => trim(preg_replace('/\s+/', ' ', $messageSj)),
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

            $responseSj = curl_exec($curl);

            curl_close($curl);

            $resSj = json_decode($responseSj, true);

            if ($resSj['status'] == 'error') {
                $dataNotif = [
                    'is_sent' => 0
                ];

                $this->db->update('tb_notif_invoice', $dataNotif, ['id_surat_jalan' => $id_surat_jalan, 'type_notif_invoice' => 'sj']);
            } else {
                if ($resSj['delivery_status'] != 'failed') {
                    $dataNotif = [
                        'is_sent' => 1
                    ];

                    $this->db->update('tb_notif_invoice', $dataNotif, ['id_surat_jalan' => $id_surat_jalan, 'type_notif_invoice' => 'sj']);
                } else {
                    $dataNotif = [
                        'is_sent' => 0
                    ];

                    $this->db->update('tb_notif_invoice', $dataNotif, ['id_surat_jalan' => $id_surat_jalan, 'type_notif_invoice' => 'sj']);
                }
            }
        }
    }

    public function cekNotifLog()
    {
        $notifInvoices = $this->db->get_where('tb_notif_invoice', ['is_sent' => 1, 'DATE(created_at)' => date('Y-m-d')])->result_array();

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

            // $logData = ['status' => 'failed'];
            // $logData = null;

            if (isset($resLog['data'])) {
                $logData = $resLog['data'][0];
                if ($logData['status'] == 'failed') {
                    $notifInvoiceData = [
                        'id_surat_jalan' => $id_surat_jalan,
                        'id_msg' => $notifInvoice['id_msg'],
                        'is_sent' => 0,
                    ];

                    $this->db->update('tb_notif_invoice', $notifInvoiceData, ['id_surat_jalan' => $id_surat_jalan, 'type_notif_invoice' => $notifInvoice['type_notif_invoice']]);
                } //else {
                //     $result = [
                //         'code' => 200,
                //         'status' => 'ok',
                //         'msg' => 'No data updated',
                //         'detail' => $resLog,
                //     ];

                //     $this->output->set_output(json_encode($result));
                // }
            } //else {
            //     $result = [
            //         'code' => 400,
            //         'status' => 'ok',
            //         'msg' => 'Error',
            //         'detail' => $resLog,
            //     ];

            //     $this->output->set_output(json_encode($result));
            // }
        }
    }
}
