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

            $contact = $this->db->select('id_contact, nama, nomorhp, tgl_lahir, store_owner, address, store_status, id_city')->where('nomorhp', $nomorhp)->get('tb_contact')->row_array();

            if (!$contact) {
                $result = [
                    'code' => 400,
                    'status' => 'failed',
                    'msg' => 'Nomor tidak terdaftar di sistem',
                ];

                return $this->output->set_output(json_encode($result));
            }

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

            $contact['kota'] = $city;

            $contact['promo'] = $promo['nama_promo'];

            $produks = $this->db->select('nama_produk, name_satuan, harga_produk')->join('tb_satuan', 'tb_satuan.id_satuan = tb_produk.id_satuan')->where('id_city', $contact['id_city'])->get('tb_produk')->result_array();

            $contact['catalog_produks'] = $produks;

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

        $webhookOrderData = [
            'json_webhook_order' => json_encode($post),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->insert('webhook_order', $webhookOrderData);

        $nomorhp = $post['ticket']['customer']['phone'];

        $contact = $this->MContact->getByNomorhp($nomorhp);

        $id_distributor = $contact['id_distributor'];

        $id_city = $contact['id_city'];

        $courier = $this->MUser->getCourierByIdCity($id_city);

        $id_courier = 0;

        if ($courier) {
            $id_courier = $courier['id_user'];
        } else {
            $courier = $this->MUser->getCourierByCityGroup($contact['nama_city']);
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
        ];

        $save = $this->db->insert('tb_surat_jalan', $sjData);

        if (!$save) {
            echo 'Gagal';
        } else {
            $id_surat_jalan = $this->db->insert_id();

            $suratJalan = $this->MSuratJalan->getById($id_surat_jalan);

            $webhookProducts = json_decode($post['ticket']['data']['daftar_pemesanan'], true);

            foreach ($webhookProducts as $webhookProduct) {
                $nama_produk = $webhookProduct['Nama Barang'];
                $qty = $webhookProduct['Quantity'];

                $produk = $this->db->get_where('tb_produk', ['id_city' => $id_city, 'nama_produk' => $nama_produk])->row_array();

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
            }

            $detailSuratJalan = $this->db->select('COUNT(*) as jml_detail')->get_where('tb_detail_surat_jalan', ['id_surat_jalan' => $id_surat_jalan])->row_array();

            if ($detailSuratJalan == 0 || $detailSuratJalan == null) {
                $this->db->delete('tb_surat_jalan', ['id_surat_jalan' => $id_surat_jalan]);
            } else {
                // Send notif kurir
                $qontak = $this->db->get_where('tb_qontak', ['id_distributor' => $id_distributor])->row_array();
                $integration_id = $qontak['integration_id'];
                $wa_token = $qontak['token'];
                $template_id = '32b18403-e0ee-4cfc-9e2e-b28b95f24e37';

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
                    "to_number": "' . $suratJalan['phone_user'] . '",
                    "to_name": "' . $suratJalan['full_name'] . '",
                    "message_template_id": "' . $template_id . '",
                    "channel_integration_id": "' . $integration_id . '",
                    "language": {
                        "code": "id"
                    },
                    "parameters": {
                        "body": [
                        {
                            "key": "1",
                            "value": "nama",
                            "value_text": "' . $suratJalan['full_name'] . '"
                        },
                        {
                            "key": "2",
                            "value": "store",
                            "value_text": "' . $suratJalan['nama'] . '"
                        },
                        {
                            "key": "3",
                            "value": "address",
                            "value_text": "' . trim(preg_replace('/\s+/', ' ', $suratJalan['address'])) . ', ' . $suratJalan['nama_city'] . '"
                        },
                        {
                            "key": "4",
                            "value": "no_surat",
                            "value_text": "' . $suratJalan['no_surat_jalan'] . '"
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
