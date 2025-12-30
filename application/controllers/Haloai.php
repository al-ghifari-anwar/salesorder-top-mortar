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
        $this->load->model('MInvoice');
        $this->load->model('MPayment');
        $this->load->model('MVoucher');
        $this->load->model('HTelegram');
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

            $contact = $this->db->select('id_contact, nama, nomorhp, tgl_lahir, store_owner, address, store_status, id_city, id_promo, termin_payment, kredit_limit')->where('nomorhp', $nomorhp)->or_where('nomorhp_2', $nomorhp)->get('tb_contact')->row_array();

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
            $paymentScore = $this->paymentScoring($contact);

            $contact['payment_scoring'] = $paymentScore;

            // Piutang
            $piutang = $this->getPiutang($id_contact);

            // $contact['piutang'] = $piutang . "";

            // Voucher
            $vouchers = $this->MVoucher->getByIdContactForHaloAI($id_contact);

            $contact['jml_voucher'] = count($vouchers) . "";

            $vouchersStr = "";
            $voucherExp = "";
            foreach ($vouchers as $voucher) {
                $vouchersStr .= $voucher['no_voucher'] . ",";
                if (!empty($voucherExp)) {
                    $voucherExp = date('d F Y', strtotime($voucher['exp_date']));
                }
            }

            $contact['voucher_expired'] = $voucherExp;

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

        $id_contact = $contact['id_contact'];

        $termin_payment = $contact['termin_payment'];

        $id_distributor = $contact['id_distributor'];

        $id_city = $contact['id_city'];

        $id_promo = $contact['id_promo'];

        $vouchers = $this->MVoucher->getByIdContactForHaloAI($id_contact);

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

            $jml_voucher = count($vouchers);

            foreach ($webhookProducts as $webhookProduct) {
                $nama_produk = $webhookProduct['Nama Barang'];
                $qty = $webhookProduct['Quantity'];

                $total_qty_products += $webhookProduct['Quantity'];

                $produk = $this->db->join('tb_master_produk', 'tb_master_produk.id_master_produk = tb_produk.id_master_produk')->where('tb_produk.id_city', $id_city)->like('tb_produk.nama_produk', $nama_produk)->get('tb_produk')->row_array();

                if ($produk == null) {
                    $produk = $this->db->join('tb_master_produk', 'tb_master_produk.id_master_produk = tb_produk.id_master_produk')->where('tb_produk.id_city', $id_city)->like('tb_master_produk.slang_produk', $nama_produk)->get('tb_produk')->row_array();
                }

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

            // Add Voucher Thinbed
            if ($jml_voucher > 0) {
                $vouchersStr = "";
                foreach ($vouchers as $voucher) {
                    $vouchersStr .= $voucher['no_voucher'] . ",";
                }

                $getThinbed = $this->db->like('nama_produk', 'TOP MORTAR THINBED')->get_where('tb_produk', ['id_city' => $id_city])->row_array();

                $sjDetailData = [
                    'id_surat_jalan' => $id_surat_jalan,
                    'id_produk' => $getThinbed['id_produk'],
                    'price' => $getThinbed['harga_produk'],
                    'qty_produk' => $jml_voucher,
                    'amount' => 0,
                    'is_bonus' => 1,
                    'no_voucher' => $vouchersStr,
                    'created_at' => date('Y-m-d H:i:s'),
                ];

                $saveVoucher = $this->db->insert('tb_detail_surat_jalan', $sjDetailData);

                if ($saveVoucher) {
                    $this->db->where_in('no_voucher', explode(',', $vouchersStr));
                    $this->db->update('tb_voucher', ['is_used' => 1, 'used_date' => date('Y-m-d H:i:s')]);
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
                // $haloai = $this->db->get_where('tb_haloai', ['id_distributor' => $id_distributor])->row_array();
                // $wa_token = $haloai['token_haloai'];
                // $business_id = $haloai['business_id_haloai'];
                // $channel_id = $haloai['channel_id_haloai'];
                // $template = 'notkurir';
                $message = "Pesanan Baru Status: Perlu di kirim Kurir: " . $suratjalan['full_name'] . ". Nama toko/penerima: " . $suratjalan['nama'] . ". Alamat: " . trim(preg_replace('/\s+/', ' ', $suratjalan['address'])) . ', ' . $suratjalan['nama_city'] . ". No Surat Jalan: *" . $suratjalan['no_surat_jalan'] . "*";

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
                //     'phone_number' => $suratJalan['phone_user'],
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

                // $res = json_decode($response, true);

                $sendNotifTele = $this->HTelegram->sendTextPrivate($suratjalan['telegram_user'], $message);

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

    public function getPiutang($id_contact)
    {
        $invoices = $this->MInvoice->getByIdContactWaiting($id_contact);

        $total_invoice = 0;
        $total_paid = 0;
        $total_sisa_hutang = 0;
        foreach ($invoices as $invoice) {
            $id_invoice = $invoice['id_invoice'];

            $payment = $this->db->query("SELECT SUM(amount_payment) AS amount_payment, SUM(potongan_payment) AS potongan_payment, SUM(adjustment_payment) AS adjustment_payment FROM tb_payment WHERE id_invoice = '$id_invoice'")->row_array();

            $sisaHutang = $invoice['total_invoice'] - ($payment['amount_payment'] + $payment['potongan_payment'] + $payment['adjustment_payment']);

            $jatuhTempo = date('d M Y', strtotime("+" . $invoice['termin_payment'] . " days", strtotime($invoice['date_invoice'])));

            $total_invoice += $invoice['total_invoice'];
            $total_paid += ($payment['amount_payment'] + $payment['potongan_payment'] + $payment['adjustment_payment']);
            $total_sisa_hutang += $sisaHutang;
        }

        return $total_sisa_hutang;
    }

    public function paymentScoring($selected_contact)
    {
        // Payment Scoring
        $count_late_payment = 0;
        $invoices = $this->MInvoice->getByIdContactNoMerch($selected_contact['id_contact']);
        $payments = null;
        $array_scoring = array();
        foreach ($invoices as $invoice) {
            $id_surat_jalan = $invoice['id_surat_jalan'];
            $payments = $this->MPayment->getLastByIdInvoiceOnly($invoice['id_invoice']);

            $sj = $this->db->get_where('tb_surat_jalan', ['id_surat_jalan' => $id_surat_jalan])->row_array();

            if ($sj['is_cod'] == 0) {
                $jatuhTempo = date('Y-m-d', strtotime("+" . $selected_contact['termin_payment'] . " days", strtotime($invoice['date_invoice'])));
            } else {
                $jatuhTempo = date('Y-m-d', strtotime($invoice['date_invoice']));
            }

            if ($payments) {

                foreach ($payments as $payment) {
                    $datePayment = date("Y-m-d", strtotime($payment['date_payment']));
                    if ($datePayment > $jatuhTempo) {
                        $count_late_payment += 1;
                        $date1 = new DateTime($datePayment);
                        $date2 = new DateTime($jatuhTempo);
                        $days  = $date2->diff($date1)->format('%a');

                        $scoreData = [
                            'id_invoice' => $invoice['id_invoice'],
                            'no_invoice' => $invoice['no_invoice'],
                            'status' => 'late',
                            'days_late' => $days,
                            'date_jatem' => $jatuhTempo,
                            'date_payment' => $datePayment,
                            'percent_score' => 100 - $days,
                            'is_cod' => $sj['is_cod'],
                            'date_invoice' => $invoice['date_invoice'],
                        ];

                        array_push($array_scoring, $scoreData);
                    } else {
                        if ($invoice['status_invoice'] == 'paid') {
                            $scoreData = [
                                'id_invoice' => $invoice['id_invoice'],
                                'no_invoice' => $invoice['no_invoice'],
                                'status' => 'good',
                                'days_late' => 0,
                                'date_jatem' => $jatuhTempo,
                                'date_payment' => $datePayment,
                                'percent_score' => 100,
                                'is_cod' => $sj['is_cod'],
                                'date_invoice' => $invoice['date_invoice'],
                            ];

                            array_push($array_scoring, $scoreData);
                        } else {
                            $dateNow = date("Y-m-d");
                            $count_late_payment += 1;
                            $date1 = new DateTime($dateNow);
                            $date2 = new DateTime($jatuhTempo);
                            $days  = $date2->diff($date1)->format('%a');

                            $scoreData = [
                                'id_invoice' => $invoice['id_invoice'],
                                'no_invoice' => $invoice['no_invoice'],
                                'status' => 'late',
                                'days_late' => $days,
                                'date_jatem' => $jatuhTempo,
                                'date_payment' => $datePayment,
                                'percent_score' => 100 - $days,
                                'is_cod' => $sj['is_cod'],
                                'date_invoice' => $invoice['date_invoice'],
                            ];

                            array_push($array_scoring, $scoreData);
                        }
                    }
                }
            } else {
                $dateNow = date("Y-m-d");
                if ($dateNow > $jatuhTempo) {
                    $count_late_payment += 1;
                    $date1 = new DateTime($dateNow);
                    $date2 = new DateTime($jatuhTempo);
                    $days  = $date2->diff($date1)->format('%a');

                    $scoreData = [
                        'id_invoice' => $invoice['id_invoice'],
                        'no_invoice' => $invoice['no_invoice'],
                        'status' => 'late',
                        'days_late' => $days,
                        'date_jatem' => $jatuhTempo,
                        'date_payment' => $dateNow,
                        'percent_score' => 100 - $days,
                        'is_cod' => $sj['is_cod'],
                        'date_invoice' => $invoice['date_invoice'],
                    ];

                    array_push($array_scoring, $scoreData);
                } else {
                    $scoreData = [
                        'id_invoice' => $invoice['id_invoice'],
                        'no_invoice' => $invoice['no_invoice'],
                        'status' => 'good',
                        'days_late' => 0,
                        'date_jatem' => $jatuhTempo,
                        'date_payment' => $dateNow,
                        'percent_score' => 100,
                        'is_cod' => $sj['is_cod'],
                        'date_invoice' => $invoice['date_invoice'],
                    ];

                    array_push($array_scoring, $scoreData);
                }
            }
        }

        $count_invoice = count($array_scoring);
        if ($count_invoice == 0) {
            $count_invoice = 1;
        }
        $total_score = 0;
        foreach ($array_scoring as $scoring) {
            $total_score += $scoring['percent_score'];
        }

        $val_scoring = number_format($total_score / $count_invoice, 2, '.', '.');

        if ($val_scoring > 100) {
            $val_scoring = 100;
        } else if ($val_scoring <= 100 && $val_scoring > 0) {
            $val_scoring = $val_scoring;
        } else if ($val_scoring < 0) {
            $val_scoring = 0;
        }

        return number_format($val_scoring, 2, '.', ',');
    }
}
