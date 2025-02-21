<?php


use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Writer\PngWriter;
use claviska\SimpleImage;

class Referal extends CI_Controller
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
        $this->load->model('MVoucher');
        $this->load->model('MVoucherTukang');
        $this->load->library('form_validation');
    }

    public function index($id_md5)
    {
        $data['title'] = 'Referal Voucher';
        $voucher = $this->MVoucherTukang->getByIdMd5($id_md5);
        $id_tukang = $voucher['id_tukang'];
        $data['voucher'] = $voucher;
        $data['tukang'] = $this->db->get_where('tb_tukang', ['id_tukang' => $id_tukang])->row_array();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Referal/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function verify($id_voucher_tukang)
    {
        $no_seri = $this->input->post("nomorhp");
        $nama = $this->input->post('nama');
        $no_seri = "62" . substr($no_seri, 1);

        $voucherOld = $this->db->get_where('tb_voucher_tukang', ['id_voucher_tukang' => $id_voucher_tukang])->row_array();

        $id_tukang_old = $voucherOld['id_tukang'];
        $tukangOld = $this->db->get_where('tb_tukang', ['id_tukang' => $id_tukang_old])->row_array();

        if ($no_seri == null) {
            $this->session->set_flashdata('failed', "Nomor HP tidak boleh kosong!");
            redirect('referal/' . $voucherOld['id_md5']);
        } else {
            $this->db->join('tb_city', 'tb_city.id_city = tb_tukang.id_city');
            $getTukang = $this->db->get_where('tb_tukang', ['nomorhp' => $no_seri])->row_array();

            if ($getTukang) {
                if ($getTukang['is_demo'] == 1) {
                    $getVoucherTukang = $this->db->get_where('tb_voucher_tukang', ['no_seri' => $no_seri])->row_array();

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

                    // Generate QR
                    $image_name = $no_seri  . date("YmdHis") . '.png';

                    $voucherCode = md5("Top" . md5($getTukang['id_tukang']));

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

                    if ($voucherOld['type_voucher'] == 'tokopromo') {
                        $imgPath = './assets/img/frame_qr_tokopromo.png';
                    } else {
                        $imgPath = './assets/img/frame_qr_2.png';
                    }

                    $frameBuilder = new SimpleImage();
                    $frameBuilder->fromFile(FCPATH . $imgPath)
                        ->autoOrient()
                        ->overlay($qrImageLoader, 'center', 1, 20, -220)
                        ->toFile(FCPATH . "./assets/img/qr/framed_" . $image_name, 'image/png');

                    $id_distributor = $getTukang['id_distributor'];
                    $wa_token = '_GEJodr1x8u7-nSn4tZK2hNq0M5CARkRp_plNdL2tFw';
                    $template_id = '781b4601-fba6-4c69-81ad-164a680ecce7';
                    $qontak = $this->db->get_where('tb_qontak', ['id_distributor' => $id_distributor])->row_array();
                    $integration_id = $qontak['integration_id'];
                    $wa_token = $qontak['token'];
                    // Data
                    $nomor_hp = $getTukang['nomorhp'];
                    $nama = $getTukang['nama'];


                    // $message = "Halo " . $nama . " tukarkan voucher diskon Rp. 10.000 dengan cara tunjukkan qr ini pada toko. ";
                    $message = "Halo " . $nama . " *Beli Top Mortar, Kembaliannya bisa buat beli Kopi!*  Dapatkan *Potongan Langsung Rp.10,000* setiap pembelian produk Top Mortar di toko bangunan terdekat. Tunjukan QR ini pada toko saat berbelanja SK: QR hanya berlaku 1x Potongan hanya berlaku per nota belanja Berlaku untuk semua produk top morta Lihat Lokasi Toko: https://order.topmortarindonesia.com/penukaranstore . Kirim voucher ke teman via link: https://order.topmortarindonesia.com/referal/" . $voucherCode;
                    // Send message
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
                                    "to_number": "' . $nomor_hp . '",
                                    "to_name": "' . $nama . '",
                                    "message_template_id": "' . $template_id . '",
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
                                                    "value":"https://order.topmortarindonesia.com/assets/img/qr/framed_' . $image_name . '"
                                                },
                                                {
                                                    "key":"filename",
                                                    "value":"qrtukang.png"
                                                }
                                            ]
                                        },
                                        "body": [
                                            {
                                                "key": "1",
                                                "value": "nama",
                                                "value_text": "' . $message . '"
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
                    // echo $response;

                    $status = $res['status'];

                    if ($status == "success") {
                        $this->MVoucherTukang->create($getTukang['id_tukang'], $no_seri);

                        $id_voucher_old = $voucherOld['id_voucher_tukang'];
                        $this->db->update('tb_voucher_tukang', ['is_claimed' => 1], ['id_voucher_tukang' => $id_voucher_old]);

                        $this->session->set_flashdata('success', "Berhasil mengirim voucher, silahkan cek QR yang telah kami kirim melalui WhatsApp!");
                        redirect('referal/complete/verify');
                    } else {
                        $this->session->set_flashdata('failed', "Gagal mengirim voucher, silahkan coba lagi!");
                        redirect('referal/complete/verify');
                    }
                } else {
                    $this->session->set_flashdata('failed', "Nomor HP sudah terdaftar!");
                    redirect('referal/' . $voucherOld['id_md5']);
                }
            } else {
                $dataTukang = [
                    'nama' => $nama,
                    'nomorhp' => $no_seri,
                    'tgl_lahir' => "0000-00-00",
                    'id_city' => $tukangOld['id_city'],
                    'maps_url' => 0,
                    'address' => "",
                    'tukang_status' => 'data',
                    'ktp_tukang' => '',
                    'id_skill' => 1,
                    'id_catcus' => 1,
                    'is_self' => 1
                ];

                $insert = $this->db->insert('tb_tukang', $dataTukang);

                if ($insert) {
                    $id_tukang = $this->db->insert_id();

                    $tukangNew = $this->db->get_where('tb_tukang', ['id_tukang' => $id_tukang])->row_array();
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
                    // Generate QR

                    $image_name = $no_seri . date("YmdHis") . '.png'; //buat name dari qr code sesuai dengan nim

                    $voucherCode = md5("Top" . md5($id_tukang));

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

                    if ($voucherOld['type_voucher'] == 'tokopromo') {
                        $imgPath = './assets/img/frame_qr_tokopromo.png';
                    } else {
                        $imgPath = './assets/img/frame_qr_2.png';
                    }

                    $frameBuilder = new SimpleImage();
                    $frameBuilder->fromFile(FCPATH . $imgPath)
                        ->autoOrient()
                        ->overlay($qrImageLoader, 'center', 1, 20, -220)
                        ->toFile(FCPATH . "./assets/img/qr/framed_" . $image_name, 'image/png');

                    $id_distributor = $getTukang['id_distributor'];
                    $wa_token = '_GEJodr1x8u7-nSn4tZK2hNq0M5CARkRp_plNdL2tFw';
                    $template_id = '7bf2d2a0-bdd5-4c70-ba9f-a9665f66a841';
                    $qontak = $this->db->get_where('tb_qontak', ['id_distributor' => $id_distributor])->row_array();
                    $integration_id = $qontak['integration_id'];
                    $wa_token = $qontak['token'];
                    // Data
                    $nomor_hp = $getTukang['nomorhp'];
                    $nama = $getTukang['nama'];


                    // $message = "Halo " . $nama . " tukarkan voucher diskon Rp. 10.000 dengan cara tunjukkan qr ini pada toko. ";
                    if ($voucherOld['type_voucher'] == 'tokopromo') {
                        $message = "Halo " . $nama . " *Beli Top Mortar, Kembaliannya bisa buat beli Kopi!*  Dapatkan *Potongan Langsung Rp.5,000* setiap pembelian produk Top Mortar di toko bangunan terdekat. Tunjukan QR ini pada toko saat berbelanja  SK:  QR hanya berlaku 1x Potongan hanya berlaku per nota belanja Berlaku untuk semua produk top mortar Lihat Lokasi Toko:  https://order.topmortarindonesia.com/penukaranstore . Kirim voucher ke teman via link: https://order.topmortarindonesia.com/referal/" . $voucherCode;
                    } else {
                        $message = "Halo " . $nama . " *Beli Top Mortar, Kembaliannya bisa buat beli Kopi!*  Dapatkan *Potongan Langsung Rp.10,000* setiap pembelian produk Top Mortar di toko bangunan terdekat. Tunjukan QR ini pada toko saat berbelanja  SK:  QR hanya berlaku 1x Potongan hanya berlaku per nota belanja Berlaku untuk semua produk top mortar Lihat Lokasi Toko:  https://order.topmortarindonesia.com/penukaranstore . Kirim voucher ke teman via link: https://order.topmortarindonesia.com/referal/" . $voucherCode;
                    }

                    // Send message
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
                                    "to_number": "' . $nomor_hp . '",
                                    "to_name": "' . $nama . '",
                                    "message_template_id": "' . $template_id . '",
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
                                                    "value":"https://order.topmortarindonesia.com/assets/img/qr/framed_' . $image_name . '"
                                                },
                                                {
                                                    "key":"filename",
                                                    "value":"qrtukang.png"
                                                }
                                            ]
                                        },
                                        "body": [
                                            {
                                                "key": "1",
                                                "value": "nama",
                                                "value_text": "' . $message . '"
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
                    // echo $response;

                    $status = $res['status'];

                    if ($status == "success") {
                        $this->MVoucherTukang->create($id_tukang, $no_seri);

                        $id_voucher_old = $voucherOld['id_voucher_tukang'];
                        $this->db->update('tb_voucher_tukang', ['is_claimed' => 1], ['id_voucher_tukang' => $id_voucher_old]);

                        $this->session->set_flashdata('success', "Berhasil mengirim voucher, silahkan cek QR yang telah kami kirim melalui WhatsApp!");
                        redirect('referal/complete/verify');
                    } else {
                        $this->session->set_flashdata('failed', "Gagal mengirim voucher, silahkan coba lagi!");
                        redirect('referal/complete/verify');
                    }
                }
            }
        }
    }

    public function complete_verify()
    {
        $data['title'] = 'Referal Voucher';
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Referal/Complete');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }
}
