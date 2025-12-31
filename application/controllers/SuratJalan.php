<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SuratJalan extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $this->load->model('MSuratJalan');
        $this->load->model('MContact');
        $this->load->model('MProduk');
        $this->load->model('MDetailSuratJalan');
        $this->load->model('MUser');
        $this->load->model('MCity');
        $this->load->model('MKendaraan');
        $this->load->model('MVoucher');
        $this->load->model('Maxchathelper');
        $this->load->model('HTelegram');
        $this->load->library('form_validation');
    }

    public function city_list()
    {
        $data['title'] = 'Surat Jalan';
        $data['menuGroup'] = 'SJ';
        $data['menu'] = 'SJ';
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('SuratJalan/CityList');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function index($id_city)
    {
        $data['title'] = 'Surat Jalan';
        $data['menuGroup'] = 'SJ';
        $data['menu'] = 'SJ';
        $data['suratjalan'] = $this->MSuratJalan->getByCity($id_city);
        $data['toko'] = $this->MContact->getAll($id_city);
        $data['kurir'] = $this->MUser->getAllDefault();
        $data['kendaraan'] = $this->MKendaraan->getAll();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('SuratJalan/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function detail($id)
    {
        $data['title'] = 'Surat Jalan';
        $data['menuGroup'] = 'SJ';
        $data['menu'] = 'SJ';
        $data['suratjalan'] = $this->MSuratJalan->getById($id);
        $suratjalan = $this->MSuratJalan->getById($id);
        $data['toko'] = $this->MContact->getById($suratjalan['id_contact']);
        $toko = $this->MContact->getById($suratjalan['id_contact']);
        $data['produk'] = $this->MProduk->getByCity($toko['id_city']);
        $data['detail'] = $this->MDetailSuratJalan->getAll($suratjalan['id_surat_jalan']);
        $data['vouchers'] = $this->MVoucher->getByIdContact($suratjalan['id_contact']);
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('SuratJalan/Detail');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function bypassClosing()
    {
        $post = $this->input->post();

        $id_surat_jalan = $post['id_surat_jalan'];

        $suratJalan = $this->MSuratJalan->getById($id_surat_jalan);

        if (!empty($_FILES['pic']['name'])) {

            // Tentukan path penyimpanan sementara
            $upload_path = FCPATH . 'assets/img/closing_img/';

            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            // Konfigurasi upload
            $config['upload_path']   = $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size']      = 10000; // dalam KB
            $config['file_name']     = 'pic_' . time();

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('pic')) {
                $upload_data = $this->upload->data();
                $file_path = $upload_data['full_path']; // path lengkap ke file

                // Kirim ke endpoint eksternal via CURL
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://saleswa.topmortarindonesia.com/suratjalan.php',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => array(
                        'command' => 'closing',
                        'id_surat_jalan' => $id_surat_jalan,
                        'distance' => '0',
                        'pic' => new CURLFILE($file_path),
                    ),
                ));

                $response = curl_exec($curl);
                curl_close($curl);

                // Hapus file sementara
                unlink($file_path);

                // Decode response
                $resClosing = json_decode($response, true);

                // Debug atau kirim ke view
                if ($resClosing['status'] == 'success') {
                    // Set invoice
                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://saleswa.topmortarindonesia.com/invoice.php',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => array('id_surat_jalan' => $id_surat_jalan),
                    ));

                    $response = curl_exec($curl);

                    curl_close($curl);

                    $res = json_decode($response, true);

                    if ($res['response'] == 200) {
                        // Save delivery
                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                            CURLOPT_URL => 'https://saleswa.topmortarindonesia.com/delivery.php',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => array('endDateTime' => date('Y-m-d H:i:s'), 'endLat' => ' ', 'endLng' => ' ', 'lat' => ' ', 'lng' => ' ', 'id_courier' => $suratJalan['id_user'], 'id_contact' => $suratJalan['id_contact'], 'startDateTime' => date('Y-m-d ') . '08:00:00', 'startLat' => ' ', 'startLng' => ' ', 'id_surat_jalan' => $id_surat_jalan),
                        ));

                        $response = curl_exec($curl);

                        curl_close($curl);

                        $this->session->set_flashdata('success', "Berhasil closing");
                        redirect('suratjalan/' . $suratJalan['id_city']);
                    } else {
                        $this->session->set_flashdata('failed', "Closing berhasil, gagal membuat invoice");
                        redirect('suratjalan/' . $suratJalan['id_city']);
                    }
                } else {
                    $this->session->set_flashdata('failed', "Gagal, Koneksi putus: " . json_encode($response));
                    redirect('suratjalan/' . $suratJalan['id_city']);
                }
            } else {
                // echo json_encode(['error' => $this->upload->display_errors()]);
                $this->session->set_flashdata('failed', "Gagal, " . json_encode($this->upload->display_errors()));
                redirect('suratjalan/' . $suratJalan['id_city']);
            }
        } else {
            // echo json_encode(['error' => 'Tidak ada file yang diupload']);
            $this->session->set_flashdata('failed', "Gagal, Tidak ada file yang diupload");
            redirect('suratjalan/' . $suratJalan['id_city']);
        }
    }

    public function closing($id_surat_jalan)
    {
        $suratJalan = $this->MSuratJalan->getById($id_surat_jalan);

        $suratJalanData = [
            'is_closing' => 1,
            'date_closing' => date('Y-m-d H:i:s'),
            'proof_closing' => '-',
            'distance' => 0,
        ];

        $save = $this->db->update('tb_surat_jalan', $suratJalanData, ['id_surat_jalan' => $id_surat_jalan]);

        if ($save) {
            // Set invoice
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://devsaleswa.topmortarindonesia.com//invoiceTopSeller.php',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('id_surat_jalan' => $id_surat_jalan),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            $res = json_decode($response, true);

            if ($res['response'] == 200) {
                $this->session->set_flashdata('success', "Berhasil closing");
                redirect('suratjalan/' . $suratJalan['id_city']);
            } else {
                $this->session->set_flashdata('failed', "Gagal.");
                redirect('suratjalan/' . $suratJalan['id_city']);
            }
        } else {
            $this->session->set_flashdata('failed', "Gagal");
            redirect('suratjalan/' . $suratJalan['id_city']);
        }
    }

    public function not_closing()
    {
        $data['title'] = 'Surat Jalan Belum Colsing';
        $data['menuGroup'] = 'SJ';
        $data['menu'] = 'NotClosing';
        $data['suratjalan'] = $this->MSuratJalan->getNotClosing();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('SuratJalan/NotClosing');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function insert()
    {
        $post = $this->input->post();

        $id_contact = $post['id_contact'];

        $contact = $this->db->join('tb_city', 'tb_city.id_city = tb_contact.id_city')->get_where('tb_contact', ['tb_contact.id_contact' => $id_contact])->row_array();

        $id_distributor = $contact['id_distributor'];

        $haloai = $this->db->get_where('tb_haloai', ['id_distributor' => $id_distributor])->row_array();
        $wa_token = $haloai['token_haloai'];
        $business_id = $haloai['business_id_haloai'];
        $channel_id = $haloai['channel_id_haloai'];

        // Check first number
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://www.haloai.co.id/api/open/room/v1/details?phoneNumber=' . $contact['nomorhp'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $wa_token,
                'X-HaloAI-Business-Id: ' . $business_id,
                'Content-Type: application/json'
            ),
        ));

        $responseFirstNumber = curl_exec($curl);

        curl_close($curl);

        $resFirstNumber = json_decode($responseFirstNumber, true);

        if (date('Y-m-d', strtotime($resFirstNumber['data']['lastMessageAt'])) >= date('Y-m-d', strtotime("-1 Days"))) {
            $this->form_validation->set_rules('order_number', 'Order Number', 'required');

            if ($this->form_validation->run() == false) {
                $this->session->set_flashdata('failed', "Harap lengkapi form!");
                redirect('surat-jalan');
            } else {
                $this->MSuratJalan->insert();
            }
        } else {
            // Check second number
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://www.haloai.co.id/api/open/room/v1/details?phoneNumber=' . $contact['nomorhp_2'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $wa_token,
                    'X-HaloAI-Business-Id: ' . $business_id,
                    'Content-Type: application/json'
                ),
            ));

            $responseSecondNumber = curl_exec($curl);

            curl_close($curl);

            $resSecondNumber = json_decode($responseSecondNumber, true);

            if (date('Y-m-d', strtotime($resSecondNumber['data']['lastMessageAt'])) >= date('Y-m-d', strtotime("-1 Days"))) {
                $this->form_validation->set_rules('order_number', 'Order Number', 'required');

                if ($this->form_validation->run() == false) {
                    $this->session->set_flashdata('failed', "Harap lengkapi form!");
                    redirect('surat-jalan');
                } else {
                    $this->MSuratJalan->insert();
                }
            } else {
                $this->session->set_flashdata('failed', "Tidak dapat membuat surat jalan, karena toko belum openchat pada hari ini! Openchat terakhir pada: " . date('Y-m-d', strtotime($resFirstNumber['data']['lastMessageAt'])) . ' | ' . date('Y-m-d', strtotime("-1 Days")));
                redirect('surat-jalan');
            }
        }
    }

    public function insertdetail()
    {
        $this->form_validation->set_rules('qty_produk', 'QTY', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap lengkapi form!");
            redirect('surat-jalan/' . $this->input->post('id_surat_jalan'));
        } else {
            $this->MDetailSuratJalan->insert();
        }
    }

    public function updatedetail($id)
    {
        $this->form_validation->set_rules('qty_produk', 'QTY', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap lengkapi form");
            redirect('surat-jalan/' . $this->input->post('id_surat_jalan'));
        } else {
            $insert = $this->MDetailSuratJalan->update($id);

            if ($insert) {
                $this->session->set_flashdata('success', "Berhasil mengubah data produk!");
                redirect('surat-jalan/' . $this->input->post('id_surat_jalan'));
            } else {
                $this->session->set_flashdata('failed', "Gagal mengubah data produk!");
                redirect('surat-jalan/' . $this->input->post('id_surat_jalan'));
            }
        }
    }

    public function finish($id)
    {
        $suratjalan = $this->MSuratJalan->getById($id);

        $this->MDetailSuratJalan->setBonusItem($id, $suratjalan['id_promo'], $suratjalan['id_city']);

        // $wa_token = 'xz5922BoBI6I9ECLKVZjPMm-7-0sqx0cjIqVVeuWURI';
        // $wa_token = '_GEJodr1x8u7-nSn4tZK2hNq0M5CARkRp_plNdL2tFw';
        $template_id = '32b18403-e0ee-4cfc-9e2e-b28b95f24e37';

        $id_distributor = $this->session->userdata('id_distributor');

        $qontak = $this->db->get_where('tb_qontak', ['id_distributor' => $id_distributor])->row_array();
        $integration_id = $qontak['integration_id'];
        $wa_token = $qontak['token'];

        if ($id_distributor != 8) {

            // $haloai = $this->db->get_where('tb_haloai', ['id_distributor' => $id_distributor])->row_array();
            // $wa_token = $haloai['token_haloai'];
            // $business_id = $haloai['business_id_haloai'];
            // $channel_id = $haloai['channel_id_haloai'];
            // $template = 'notkurir';
            $message = "Pesanan Baru Status: Perlu di kirim \nKurir: " . $suratjalan['full_name'] . ". \nNama toko/penerima: " . $suratjalan['nama'] . ". \nAlamat: " . trim(preg_replace('/\s+/', ' ', $suratjalan['address'])) . ', ' . $suratjalan['nama_city'] . ". \nNo Surat Jalan: *" . $suratjalan['no_surat_jalan'] . "*";

            // $haloaiPayload = [
            //     'activate_ai_after_send' => false,
            //     'channel_id' => $channel_id,
            //     'fallback_template_message' => $template,
            //     'fallback_template_variables' => [
            //         $suratjalan['full_name'],
            //         $suratjalan['nama'],
            //         trim(preg_replace('/\s+/', ' ', $suratjalan['address'])) . ', ' . $suratjalan['nama_city'],
            //         $suratjalan['no_surat_jalan'],
            //     ],
            //     'phone_number' => $suratjalan['phone_user'],
            //     'text' => trim(preg_replace('/\s+/', ' ', $message)),
            // ];

            // $curl = curl_init();

            // curl_setopt_array($curl, array(
            //     CURLOPT_URL => 'https://www.haloai.co.id/api/open/channel/whatsapp/v1/sendMessageByPhoneSync',
            //     CURLOPT_RETURNTRANSFER => true,
            //     CURLOPT_ENCODING => '',
            //     CURLOPT_MAXREDIRS => 10,
            //     CURLOPT_TIMEOUT => 0,
            //     CURLOPT_FOLLOWLOCATION => true,
            //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //     CURLOPT_CUSTOMREQUEST => 'POST',
            //     CURLOPT_POSTFIELDS => json_encode($haloaiPayload),
            //     CURLOPT_HTTPHEADER => array(
            //         'Authorization: Bearer ' . $wa_token,
            //         'X-HaloAI-Business-Id: ' . $business_id,
            //         'Content-Type: application/json'
            //     ),
            // ));

            // $response = curl_exec($curl);

            // curl_close($curl);

            // echo $response;
            // die;

            // $res = json_decode($response, true);

            $sendNotifTele = $this->HTelegram->sendTextPrivate($suratjalan['telegram_user'], $message);

            $status = $sendNotifTele['status'];
        } else {
            $message = 'Pesanan perlu dikirim. Nomor SJ: ' . $suratjalan['no_surat_jalan'] . '. Alamat: ' . trim(preg_replace('/\s+/', ' ', $suratjalan['address']));
            $jsonRequest = [
                'to' => $suratjalan['phone_user'],
                'msgType' => 'text',
                'templateId' => 'b75d51f9-c925-4a62-8b93-dd072600b95b',
                'values' => [
                    'body' => [
                        [
                            'index' => 1,
                            'type' => 'text',
                            'text' => $suratjalan['full_name']
                        ],
                        [
                            'index' => 2,
                            'type' => 'text',
                            'text' => trim(preg_replace('/\s+/', ' ', $message))
                        ]
                    ],
                ]
            ];

            $resArray = $this->Maxchathelper->postCurl(1, 'https://app.maxchat.id/api/messages/push', $jsonRequest);

            $status = isset($resArray['content']) ? 'success' : 'no';
        }

        if ($status == "success") {
            $this->db->update('tb_surat_jalan', ['is_finished' => 1], ['id_surat_jalan' => $suratjalan['id_surat_jalan']]);

            $this->session->set_flashdata('success', "Surat jalan berhasil dibuat!");
            redirect('surat-jalan');
        } else {
            $this->session->set_flashdata('failed', "Surat jalan terbuat, tetapi notif WA tidak terkirim ke kurir..." . " -> " . json_encode($sendNotifTele));
            redirect('surat-jalan');
        }
    }

    public function deletedetail($id)
    {
        $detailSJ = $this->db->get_where('tb_detail_surat_jalan', ['id_detail_surat_jalan' => $id])->row_array();
        $insert = $this->MDetailSuratJalan->delete($id);

        if ($insert) {
            $this->session->set_flashdata('success', "Berhasil megnhapus data produk!");
            redirect('surat-jalan/' . $detailSJ['id_surat_jalan']);
        } else {
            $this->session->set_flashdata('failed', "Gagal megnhapus data produk!");
            redirect('surat-jalan/' . $detailSJ['id_surat_jalan']);
        }
    }

    public function delete($id)
    {
        $insert = $this->MSuratJalan->delete($id);

        if ($insert) {
            $this->session->set_flashdata('success', "Berhasil megnhapus data suratjalan!");
            redirect('surat-jalan');
        } else {
            $this->session->set_flashdata('failed', "Gagal megnhapus data suratjalan!");
            redirect('surat-jalan');
        }
    }

    public function print($id)
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $suratjalan = $this->MSuratJalan->getById($id);
        $data['suratjalan'] = $suratjalan;
        $data['store'] = $this->MContact->getById($suratjalan['id_contact']);
        $data['kendaraan'] = $this->MKendaraan->getById($suratjalan['id_kendaraan']);
        $data['courier'] = $this->MUser->getById($suratjalan['id_courier']);
        $data['produk'] = $this->MDetailSuratJalan->getAll($suratjalan['id_surat_jalan']);

        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('SuratJalan/Print', $data, true);
        $mpdf->AddPage('P');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    public function print_tempinv($id)
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $suratjalan = $this->MSuratJalan->getById($id);
        $data['suratjalan'] = $suratjalan;
        $data['store'] = $this->MContact->getById($suratjalan['id_contact']);
        $data['kendaraan'] = $this->MKendaraan->getById($suratjalan['id_kendaraan']);
        $data['courier'] = $this->MUser->getById($suratjalan['id_courier']);
        $data['produk'] = $this->MDetailSuratJalan->getAll($suratjalan['id_surat_jalan']);

        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('SuratJalan/PrintInv', $data, true);
        $mpdf->AddPage('P');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }
}
