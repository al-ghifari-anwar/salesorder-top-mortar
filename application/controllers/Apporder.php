<?php

class Apporder extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MApporder');
        $this->load->model('MApporderDetail');
        $this->load->model('MCity');
        $this->load->model('MContact');
        $this->load->model('MSuratJalan');
        $this->load->model('MDetailSuratJalan');
        $this->load->model('MUser');
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
    }

    public function index()
    {
        $data['title'] = 'Pesanan Top Seller';
        $data['apporders'] = $this->MApporder->get();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Apporder/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function approve($id_apporder)
    {
        $apporder = $this->MApporder->getById($id_apporder);

        $id_contact = $apporder['id_contact'];

        $contact = $this->MContact->getById($id_contact);

        $id_distributor = $contact['id_distributor'];
        $id_city = $contact['id_city'];


        $approderDetails = $this->MApporderDetail->getByIdApporder($apporder['id_apporder']);

        $courier = $this->MUser->getCourierByIdCity($id_city);
        $id_courier = $courier['id_user'];

        $suratJalanData = [
            'user_approved_apporder' => $this->session->userdata('id_user'),
            'id_apporder' => $id_apporder,
            'no_surat_jalan' => "DO-" . rand(10000000, 99999999),
            'id_contact' => $id_contact,
            'dalivery_date' => date("Y-m-d H:i:s"),
            'order_number' => 0,
            'ship_to_name' => $contact['nama'],
            'ship_to_address' => $contact['address'],
            'ship_to_phone' => $contact['nomorhp'],
            'id_courier' => $id_courier,
            'id_kendaraan' => 2,
            'is_finished' => 1,
            'is_cod' => 0,
        ];

        $saveSuratJalan = $this->MSuratJalan->create($suratJalanData);

        if ($saveSuratJalan) {
            $id_surat_jalan = $this->db->insert_id();

            $suratJalan = $this->MSuratJalan->getById($id_surat_jalan);

            foreach ($approderDetails as $approderDetail) {
                $detailSuratJalanData = [
                    'id_surat_jalan' => $id_surat_jalan,
                    'id_produk' => $approderDetail['id_produk'],
                    'price' => $approderDetail['price_produk'],
                    'qty_produk' => $approderDetail['qty_apporder_detail'],
                    'amount' => $approderDetail['total_apporder_detail'],
                    'is_bonus' => 0,
                    'no_voucher' => '',
                ];

                $saveDetailSuratJalan = $this->MDetailSuratJalan->create($detailSuratJalanData);
            }

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

            if ($res['status'] == 'success') {
                $this->session->set_flashdata('success', "Pesanan berhasil dikonfirmasi");
                redirect('apporder');
            } else {
                $this->session->set_flashdata('failed', "Pesanan dibuat, tetapi tidak mengirim notif ke kurir");
                redirect('apporder');
            }
        } else {
            $this->session->set_flashdata('failed', "Terjadi kesalahan, harap coba lagi");
            redirect('apporder');
        }
    }
}
