<?php

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Writer\PngWriter;
use claviska\SimpleImage;

class Maxchat extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Maxchathelper');
        $this->load->model('MVoucherTukang');
    }

    public function inbound()
    {
        $json = file_get_contents("php://input");

        $vars = json_decode($json, true);

        $target_number = $vars['from'];

        $dataInbound = [
            'id_maxchat' => $vars['id'],
            'from_inbound_msg' => $vars['from'],
            'type_inbound_msg' => $vars['msgType'],
            'serviceid_inbound_msg' => $vars['serviceId'],
            'timestamp_inbound_msg' => date("Y-m-d H:i:s", $vars['timestamp']),
            'text_inbound_msg' => $vars['text'],
            'username_inbound_msg' => $vars['username'],
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ];

        $this->db->insert('tb_inbound_msg', $dataInbound);

        // Voucher System
        $this->db->order_by('created_at', 'DESC');
        $getOutbound = $this->db->get_where('tb_outbound_msg', ['to_outbound_msg' => $target_number])->row_array();

        if (!$getOutbound) {
            // Nothing  to do
            $logData = [
                'to_number' => $target_number,
                'status' => 'No Outbound'
            ];

            $this->db->insert('tb_log_vc_maxchat', $logData);
        } else {
            $getTukang = $this->db->get_where('tb_tukang', ['nomorhp' => $target_number])->row_array();

            if (!$getTukang) {
                // Nothing to do
                $logData = [
                    'to_number' => $target_number,
                    'status' => 'No Tukang with number ' . $target_number
                ];

                $this->db->insert('tb_log_vc_maxchat', $logData);
            } else {
                $id_tukang = $getTukang['id_tukang'];

                $this->db->order_by('created_at', 'DESC');
                $checkVoucher = $this->db->get_where('tb_voucher_tukang', ['id_tukang' => $id_tukang, 'is_claimed' => 0])->row_array();

                if ($checkVoucher) {
                    $exp_at = date("Y-m-d", strtotime($checkVoucher['exp_at']));

                    if ($exp_at < date("Y-m-d")) {
                        $logData = [
                            'to_number' => $target_number,
                            'status' => 'OK Continue to send voucher'
                        ];

                        $this->db->insert('tb_log_vc_maxchat', $logData);

                        $this->sendVoucher($id_tukang);
                    } else {
                        $logData = [
                            'to_number' => $target_number,
                            'status' => 'Voucher still active'
                        ];

                        $this->db->insert('tb_log_vc_maxchat', $logData);
                    }
                } else {
                    $this->db->order_by('created_at', 'DESC');
                    $checkVoucherClaim = $this->db->get_where('tb_voucher_tukang', ['id_tukang' => $id_tukang, 'is_claimed' => 1])->row_array();

                    if ($checkVoucherClaim) {
                        $claim_date = date("Y-m-d", strtotime($checkVoucherClaim['claim_date']));
                        $batas_date = date("Y-m-d", strtotime('-7 days'));

                        if ($claim_date < $batas_date) {
                            $logData = [
                                'to_number' => $target_number,
                                'status' => 'OK Continue to send voucher'
                            ];

                            $this->db->insert('tb_log_vc_maxchat', $logData);

                            $this->sendVoucher($id_tukang);
                        } else {
                            $logData = [
                                'to_number' => $target_number,
                                'status' => 'Voucher needs cooldown'
                            ];

                            $this->db->insert('tb_log_vc_maxchat', $logData);
                        }
                    } else {
                        $logData = [
                            'to_number' => $target_number,
                            'status' => 'OK Continue to send voucher'
                        ];

                        $this->db->insert('tb_log_vc_maxchat', $logData);
                        $this->sendVoucher($id_tukang);
                    }
                }
            }
        }
    }

    public function outbound()
    {
        $json = file_get_contents("php://input");

        $vars = json_decode($json, true);

        $dataOutbound = [
            'id_maxchat' => $vars['id'],
            'to_outbound_msg' => $vars['to'],
            'status_outbound_msg' => $vars['status'],
            'timestamp_outbound_msg' => date("Y-m-d H:i:s", $vars['timestamp']),
            'serviceid_outbound_msg' => $vars['serviceId'],
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ];

        $this->db->insert('tb_outbound_msg', $dataOutbound);
    }

    public function sendVoucher($id_tukang)
    {
        $id_md5 = md5("Top" . md5($id_tukang . date("Y-m-d-H-i-s")));

        $this->db->join('tb_city', 'tb_city.id_city = tb_tukang.id_city');
        $getTukang = $this->db->get_where('tb_tukang', ['id_tukang' => $id_tukang])->row_array();

        $query = $this->MVoucherTukang->createVoucherDigital($id_tukang, 0, 0, 0, $id_md5);

        $nomor_hp = $getTukang['nomorhp'];
        $nama = $getTukang['nama'];

        if ($query) {
            // Read Logo File
            $logoPath = FCPATH . "./assets/img/logo_retina.png";
            $logoImageReader = new SimpleImage();
            $logoImageReader->fromFile($logoPath)->bestFit(100, 100);
            // Next, create a slightly larger image,
            // fill it with a rounded white square,
            // and overlay the resized logo
            $logoImageBuilder = new SimpleImage();
            $logoImageBuilder->fromNew(110, 110)->roundedRectangle(0, 0, 110, 110, 10, 'white', 'filled')->overlay($logoImageReader);

            $logoData = $logoImageBuilder->toDataUri('image/png', 100);

            $image_name = $id_tukang . $id_md5 . date("Y-m-d") . '.png'; //buat name dari qr code sesuai dengan nim

            $voucherCode = $id_md5;
            // Generate QR
            $qrCode = Builder::create()
                ->writer(new PngWriter())
                ->writerOptions([])
                ->data($voucherCode)
                ->size(500)
                ->logoPath($logoData)
                ->logoResizeToWidth(100)
                ->encoding(new Encoding('ISO-8859-1'))
                ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
                ->build()
                ->saveToFile(FCPATH . "./assets/img/qr/" . $image_name);

            $qrPath = FCPATH . "./assets/img/qr/" . $image_name;
            $qrImageLoader = new SimpleImage();
            $qrImageLoader->fromFile($qrPath)->resize(420, 420);

            $frameBuilder = new SimpleImage();
            $frameBuilder->fromFile(FCPATH . "./assets/img/frame_qr_2.jpg")
                ->autoOrient()
                ->overlay($qrImageLoader, 'center', 1, 20, -220)
                ->toFile(FCPATH . "./assets/img/qr/framed_" . $image_name, 'image/png');

            // $wa_token = '_GEJodr1x8u7-nSn4tZK2hNq0M5CARkRp_plNdL2tFw';
            // $template_id = '781b4601-fba6-4c69-81ad-164a680ecce7';
            // Data



            // $message = "Halo " . $nama . " tukarkan voucher diskon Rp. 10.000 dengan cara tunjukkan qr ini pada toko. ";
            $message = "Halo " . $nama . " *Beli Top Mortar, Kembaliannya bisa buat beli Kopi!*  Dapatkan *Potongan Langsung Rp.10,000* setiap pembelian produk Top Mortar di toko bangunan terdekat. Tunjukan QR ini pada toko saat berbelanja  SK:  QR hanya berlaku 1x Potongan hanya berlaku per nota belanja Berlaku untuk semua produk top mortar Lihat Lokasi Toko:  https://order.topmortarindonesia.com/penukaranstore . Kirim voucher ke teman via link: https://order.topmortarindonesia.com/referal/" . $voucherCode;

            $image = "https://order.topmortarindonesia.com/assets/img/qr/framed_" . $image_name;

            // Send message
            $jsonRequest = [
                'to' => $nomor_hp,
                'msgType' => 'image',
                'templateId' => 'ad6c74a0-5e00-4380-92f9-f9c467c4f399',
                'values' => [
                    'body' => [
                        [
                            'index' => 1,
                            'type' => 'text',
                            'text' => $nama
                        ],
                        [
                            'index' => 2,
                            'type' => 'text',
                            'text' => $message
                        ]
                    ],
                    'header' => [
                        'type' => 'image',
                        'attachmentUrl' => $image
                    ]
                ]
            ];

            $resArray = $this->Maxchathelper->postCurl(1, 'https://app.maxchat.id/api/messages/push', $jsonRequest);

            // $res = json_decode($response, true);
            // echo $response;
            // die;

            // $status = $res['status'];

            if (isset($resArray['content'])) {
                $logData = [
                    'to_number' => $nomor_hp,
                    'status' => 'Succes send voucher',
                ];

                $this->db->insert('tb_log_vc_maxchat', $logData);

                $this->session->set_flashdata('success', "Berhasil kirim voucher!");
            } else {
                $logData = [
                    'to_number' => $nomor_hp,
                    'status' => 'Fail send voucher',
                ];

                $this->db->insert('tb_log_vc_maxchat', $logData);

                $this->session->set_flashdata('failed', "Gagal kirim notif voucher! ");
            }
        } else {
            $logData = [
                'to_number' => $nomor_hp,
                'status' => 'Fail cerating voucher',
                'detail' => $this->db->error(),
            ];

            $this->db->insert('tb_log_vc_maxchat', $logData);

            $this->session->set_flashdata('failed', "Gagal kirim voucher!");
        }
    }
}
