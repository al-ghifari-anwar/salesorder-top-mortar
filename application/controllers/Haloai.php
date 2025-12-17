<?php

class Haloai extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->output->set_content_type('application/json');
        $this->load->model('MContact');
        $this->load->model('MUser');
        $this->load->model('MSuratJalan');
        $this->load->model('MDetailSuratJalan');
    }

    public function getStore()
    {
        if (!isset($_GET['nomorhp'])) {
            $result = [
                'code' => 404,
                'status' => 'failed',
                'msg' => 'Not Found',
            ];

            return $this->output->set_output(json_encode($result));
        } else {
            $nomorhp = $_GET['nomorhp'];

            $contact = $this->db->select('id_contact, nama, nomorhp, tgl_lahir, store_owner, address, store_status, id_city, id_promo, termin_payment')->where('nomorhp', $nomorhp)->or_where('nomorhp_2', $nomorhp)->get('tb_contact')->row_array();

            if (!$contact) {
                $result = [
                    'code' => 400,
                    'status' => 'failed',
                    'msg' => 'Nomor tidak terdaftar di sistem',
                ];

                return $this->output->set_output(json_encode($result));
            }

            $contact['termin_payment'] = $contact['termin_payment'] > 0 ? "Tempo " . $contact['termin_payment'] . " hari" : "COD";

            $id_contact = $contact['id_contact'];

            $id_promo = $contact['id_promo'];

            $suratJalan = $this->db->get_where('tb_surat_jalan', ['id_contact' => $id_contact, 'is_closing' => 0])->result_array();

            $promo = $this->db->get_where('tb_promo', ['id_promo' => $id_promo])->row_array();

            // Score toko
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://order.topmortarindonesia.com/scoring/combine/' . $contact['id_contact'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_HTTPHEADER => array(
                    'Cookie: ci_session=vk5hbn4tegimdup7bt4fqqj8i7nolfba'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            $scoring = json_decode($response, true);

            $contact['payment_scoring'] = $scoring['payment'];

            $city = $this->db->select('nama_city')->where('id_city', $contact['id_city'])->get('tb_city')->row_array();

            $contact['kota'] = $city['nama_city'];

            $contact['promo_global'] = $promo ? $promo['nama_promo'] : null;

            $produks = $this->db->select('nama_produk, name_satuan, harga_produk, slang_produk, is_default_promo, kelipatan_promo, bonus_promo, img_master_produk')->join('tb_satuan', 'tb_satuan.id_satuan = tb_produk.id_satuan')->join('tb_master_produk', 'tb_master_produk.id_master_produk = tb_produk.id_master_produk')->where('tb_produk.id_city', $contact['id_city'])->get('tb_produk')->result_array();

            $arrayProduks = array();

            foreach ($produks as $produk) {
                $promoProduk = null;

                if ($produk['is_default_promo'] == 0) {
                    $promoProduk = $produk['kelipatan_promo'] . " + " . $produk['bonus_promo'];
                }

                $produkData = [
                    'nama_produk' => $produk['nama_produk'],
                    'name_satuan' => $produk['name_satuan'],
                    'harga_produk' => $produk['harga_produk'],
                    'slang_produk' => $produk['slang_produk'],
                    'promo_spesifik' => $promoProduk,
                    'image_produk' => $produk['img_master_produk'],
                ];

                array_push($arrayProduks, $produkData);
            }

            $contact['catalog_produks'] = $arrayProduks;

            $contact['ongoing_order'] = $suratJalan;

            $result = [
                'code' => 200,
                'status' => 'ok',
                'msg' => 'Success',
                'data' => $contact,
            ];

            return $this->output->set_output(json_encode($result));
        }
    }

    public function createOrder()
    {
        $haloAiToken = 'pat_7911607b2bfbf47d7dfd029c6895cac59e157b0530c651d623c99bc91667d205';
        $haloAiBusinessId = '019a7616-cbb2-7089-ae65-d629c2d82c01';
        $haloAiChannelId = '019a7616-cf5b-7769-91ab-c1dacb9e9cf9';
        $haloAiTemplate = 'order_confirmation';

        $post = json_decode(file_get_contents('php://input'), true) != null ? json_decode(file_get_contents('php://input'), true) : $this->input->post();

        $webhookProducts = json_decode($post['ticket']['data']['daftar_pemesanan'], true);

        if (empty($webhookProducts)) {
            return $this->output->set_status_header(500);
        }

        $this->db->trans_begin();

        $webhookOrderData = [
            'json_webhook_order' => json_encode($post),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->insert('webhook_order', $webhookOrderData);

        $nomorhp = $post['ticket']['customer']['phone'];

        $this->db->join('tb_city', 'tb_city.id_city = tb_contact.id_city');
        $contact = $this->db->where('nomorhp', $nomorhp)->or_where('nomorhp_2', $nomorhp)->get('tb_contact')->row_array();

        $termin_payment = $contact['termin_payment'];

        $id_distributor = $contact['id_distributor'];

        $id_city = $contact['id_city'];

        $id_promo = $contact['id_promo'];

        $courier = $this->MUser->getCourierByIdCity($id_city);

        $id_courier = 0;

        if ($courier) {
            $id_courier = $courier['id_user'];
        } else {
            $courier = $this->MUser->getCourierByCityGroup(trim(preg_replace("/\\d+/", "", $contact['nama_city'])));
            $id_courier = $courier['id_user'];
            // 
        }

        $sjData = [
            'no_surat_jalan' => 'DO-41' . rand(10000, 99999),
            'id_contact' => $contact['id_contact'],
            'dalivery_date' => date('Y-m-d H:i:s'),
            'order_number' => 0,
            'ship_to_name' => $contact['nama'],
            'ship_to_address' => $contact['address'],
            'ship_to_phone' => $contact['nomorhp'],
            'id_courier' => $id_courier,
            'id_kendaraan' => 2,
            'is_finished' => 1,
            'is_cod' => ($termin_payment >= 0 && $termin_payment < 3) ? 1 : 0,
        ];

        $save = $this->db->insert('tb_surat_jalan', $sjData);

        if (!$save) {
            echo 'Gagal';
        } else {
            $id_surat_jalan = $this->db->insert_id();

            $suratJalan = $this->MSuratJalan->getById($id_surat_jalan);

            $webhookProducts = json_decode($post['ticket']['data']['daftar_pemesanan'], true);

            $total_qty_products = 0;
            $total_qty_bonus = 0;

            foreach ($webhookProducts as $webhookProduct) {
                $nama_produk = $webhookProduct['Nama Barang'];
                $qty = $webhookProduct['Quantity'];

                $total_qty_products += $webhookProduct['Quantity'];

                $produk = $this->db->join('tb_master_produk', 'tb_master_produk.id_master_produk = tb_produk.id_master_produk')->where('tb_produk.id_city', $id_city)->like('tb_produk.nama_produk', $nama_produk)->or_like('tb_master_produk.slang_produk', $nama_produk)->get('tb_produk')->row_array();

                $sjDetailData = [
                    'id_surat_jalan' => $id_surat_jalan,
                    'id_produk' => $produk['id_produk'],
                    'price' => $produk['harga_produk'],
                    'qty_produk' => $qty,
                    'amount' => $produk['harga_produk'] * $qty,
                    'is_bonus' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                ];

                $this->db->insert('tb_detail_surat_jalan', $sjDetailData);

                if ($id_promo != 0) {
                    // Calculate bonus
                    if ($produk['is_default_promo'] == 1) {
                        $promo = $this->db->get_where('tb_promo', ['id_promo' => $id_promo])->row_array();

                        $multiplier = $qty / $promo['kelipatan_promo'];

                        if (floor($multiplier) > 0) {
                            $total_qty_bonus += floor($multiplier) * $promo['bonus_promo'];

                            $sjDetailData = [
                                'id_surat_jalan' => $id_surat_jalan,
                                'id_produk' => $produk['id_produk'],
                                'price' => $produk['harga_produk'],
                                'qty_produk' => floor($multiplier) * $promo['bonus_promo'],
                                'amount' => 0,
                                'is_bonus' => 1,
                                'created_at' => date('Y-m-d H:i:s'),
                            ];

                            $this->db->insert('tb_detail_surat_jalan', $sjDetailData);
                        }
                    } else {
                        $multiplier = $qty / $produk['kelipatan_promo'];

                        if (floor($multiplier) > 0) {
                            $sjDetailData = [
                                'id_surat_jalan' => $id_surat_jalan,
                                'id_produk' => $produk['id_produk'],
                                'price' => $produk['harga_produk'],
                                'qty_produk' => floor($multiplier) * $produk['bonus_promo'],
                                'amount' => 0,
                                'is_bonus' => 1,
                                'created_at' => date('Y-m-d H:i:s'),
                            ];

                            $this->db->insert('tb_detail_surat_jalan', $sjDetailData);
                        }
                    }
                }
            }

            // Set Bonus If Per Produk Not Match Minimum, Use qty Whole Order
            if ($total_qty_bonus == 0) {
                if ($contact['id_promo'] != 0) {
                    $promo = $this->db->get_where('tb_promo', ['id_promo' => $id_promo])->row_array();

                    $kelipatan_promo = $promo['kelipatan_promo'];

                    $multiplier = $total_qty_products / $kelipatan_promo;

                    if (floor($multiplier) > 0) {
                        $cheapestProduct = $this->MDetailSuratJalan->getCheapestProduct($id_surat_jalan);

                        $sjDetailData = [
                            'id_surat_jalan' => $id_surat_jalan,
                            'id_produk' => $cheapestProduct['id_produk'],
                            'price' => $cheapestProduct['harga_produk'],
                            'qty_produk' => floor($multiplier) * $promo['bonus_promo'],
                            'amount' => 0,
                            'is_bonus' => 1,
                            'created_at' => date('Y-m-d H:i:s'),
                        ];

                        $this->db->insert('tb_detail_surat_jalan', $sjDetailData);
                    }
                }
            }

            if ($this->db->trans_status() === FALSE) {
                $haloai = $this->db->get_where('tb_haloai', ['id_distributor' => $id_distributor])->row_array();
                $wa_token = $haloai['token_haloai'];
                $business_id = $haloai['business_id_haloai'];
                $channel_id = $haloai['channel_id_haloai'];
                $template = 'info_meeting_baru';
                $message = "Surat Jalan Tidak Terbuat, Toko: " . $suratJalan['nama'];

                $haloaiPayload = [
                    'activate_ai_after_send' => false,
                    'channel_id' => $channel_id,
                    'fallback_template_message' => $template,
                    'fallback_template_variables' => [
                        "Bella",
                        trim(preg_replace('/\s+/', ' ', $message)),
                        "Automated Message",
                    ],
                    'phone_number' => "6282131426363",
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

                $this->db->trans_rollback();
                return $this->output->set_status_header(500);
            } else {
                $this->db->trans_commit();
                return $this->output->set_status_header(200);
            }

            $detailSuratJalan = $this->db->select('COUNT(*) as jml_detail')->get_where('tb_detail_surat_jalan', ['id_surat_jalan' => $id_surat_jalan])->row_array();


            if ($detailSuratJalan == 0 || $detailSuratJalan == null) {
                $this->db->delete('tb_surat_jalan', ['id_surat_jalan' => $id_surat_jalan]);
            } else {
                // Send notif kurir
                $haloai = $this->db->get_where('tb_haloai', ['id_distributor' => $id_distributor])->row_array();
                $wa_token = $haloai['token_haloai'];
                $business_id = $haloai['business_id_haloai'];
                $channel_id = $haloai['channel_id_haloai'];
                $template = 'notkurir';
                $message = "Pesanan Baru Status: Perlu di kirim Kurir: " . $suratjalan['full_name'] . ". Nama toko/penerima: " . $suratjalan['nama'] . ". Alamat: " . trim(preg_replace('/\s+/', ' ', $suratjalan['address'])) . ', ' . $suratjalan['nama_city'] . ". No Surat Jalan: *" . $suratjalan['no_surat_jalan'] . "*";

                $haloaiPayload = [
                    'activate_ai_after_send' => false,
                    'channel_id' => $channel_id,
                    'fallback_template_message' => $template,
                    'fallback_template_variables' => [
                        $suratjalan['full_name'],
                        $suratjalan['nama'],
                        trim(preg_replace('/\s+/', ' ', $suratjalan['address'])) . ', ' . $suratjalan['nama_city'],
                        $suratjalan['no_surat_jalan'],
                    ],
                    'phone_number' => $suratJalan['phone_user'],
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

                $resultData = [
                    'no_surat_jalan' => $suratJalan['no_surat_jalan'],
                ];

                $result = [
                    'code' => 200,
                    'status' => 'ok',
                    'msg' => 'Success',
                    'data' => $resultData,
                ];

                return $this->output->set_output(json_encode($result));
            }
        }
    }
}
