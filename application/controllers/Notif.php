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
        $files = glob(FCPATH . "assets/tmp/inv/*" . $fileNameSearch . "*");

        if ($files) {
            $replaceFilePath = str_replace("/home/admin/web/", "https://", $files[0]);
            echo json_encode($replaceFilePath);
        }

        // Send Invoice
        // $curl = curl_init();

        // curl_setopt_array($curl, array(
        //     CURLOPT_URL => 'https://service-chat.qontak.com/api/open/v1/broadcasts/whatsapp/direct',
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => '',
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 0,
        //     CURLOPT_FOLLOWLOCATION => true,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => 'POST',
        //     CURLOPT_POSTFIELDS => '{
        //                 "to_number": "' . $nomorhp . '",
        //                 "to_name": "' . $nama . '",
        //                 "message_template_id": "' . $template_id . '",
        //                 "channel_integration_id": "' . $integration_id . '",
        //                 "language": {
        //                     "code": "id"
        //                 },
        //                 "parameters": {
        //                     "header":{
        //                         "format":"DOCUMENT",
        //                         "params": [
        //                             {
        //                                 "key":"url",
        //                                 "value":"https://order.topmortarindonesia.com/assets/tmp/inv/' . $fileName . '"
        //                             },
        //                             {
        //                                 "key":"filename",
        //                                 "value":"' . $fileName . '"
        //                             }
        //                         ]
        //                     },
        //                     "body": [
        //                     {
        //                         "key": "1",
        //                         "value": "nama",
        //                         "value_text": "' . $nama . '"
        //                     },
        //                     {
        //                         "key": "2",
        //                         "value": "message",
        //                         "value_text": "' . trim(preg_replace('/\s+/', ' ', $message)) . '"
        //                     },
        //                     {
        //                         "key": "3",
        //                         "value": "sales",
        //                         "value_text": "' . $full_name . '"
        //                     }
        //                     ]
        //                 }
        //                 }',
        //     CURLOPT_HTTPHEADER => array(
        //         'Authorization: Bearer ' . $wa_token,
        //         'Content-Type: application/json'
        //     ),
        // ));

        // $response = curl_exec($curl);

        // curl_close($curl);

        // $res = json_decode($response, true);

        // if ($res['status'] == 'success') {
        //     $result = [
        //         'code' => 200,
        //         'status' => 'ok',
        //         'detail' => $res,
        //         'detailSj' => $resSj
        //     ];

        //     return $this->output->set_output(json_encode($result));
        // } else {
        //     $result = [
        //         'code' => 400,
        //         'status' => 'failed',
        //         'detail' => $res,
        //         'detailSj' => $resSj
        //     ];

        //     return $this->output->set_output(json_encode($result));
        // }
    }
}
