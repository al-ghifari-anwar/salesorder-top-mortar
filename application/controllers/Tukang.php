<?php

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Writer\PngWriter;
use claviska\SimpleImage;

defined('BASEPATH') or exit('No direct script access allowed');

class Tukang extends CI_Controller
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
        $this->load->model('MVoucherTukang');
        $this->load->model('Maxchathelper');
        $this->load->library('form_validation');
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
    }

    public function index()
    {
        $id_distributor = $this->session->userdata('id_distributor');
        $data['title'] = 'Data Tukang Top Mortar';
        $data['menuGroup'] = 'Data';
        $data['menu'] = 'Tukang';
        if ($this->session->userdata('level_user') == 'admin_c') {
            $this->db->join('tb_city', 'tb_city.id_city = tb_tukang.id_city');
            $this->db->join('tb_skill', 'tb_skill.id_skill = tb_tukang.id_skill');
            $data['tukangs'] = $this->db->get('tb_tukang', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $this->db->join('tb_city', 'tb_city.id_city = tb_tukang.id_city');
            $this->db->join('tb_skill', 'tb_skill.id_skill = tb_tukang.id_skill');
            $this->db->where('id_distributor', $id_distributor);
            $data['tukangs'] = $this->db->get('tb_tukang')->result_array();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Tukang/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function demo($id_tukang)
    {
        $is_demo = $this->input->post('is_demo');

        $query = $this->db->update('tb_tukang', ['is_demo' => $is_demo], ['id_tukang' => $id_tukang]);

        if ($query) {
            $this->session->set_flashdata('success', "Sukses ubah status demo!");
            redirect('tukang');
        } else {
            $this->session->set_flashdata('failed', "Gagal ubah status demo!");
            redirect('tukang');
        }
    }

    public function sebar_vc_city()
    {
        $data['title'] = 'Sebar Voucher Tukang';
        $data['menuGroup'] = 'TopSeller';
        $data['menu'] = 'Sebarvctukang';
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Tukang/City');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function sebar_vc($id_city)
    {
        $data['title'] = 'Data Tukang Top Mortar';
        $data['menuGroup'] = 'TopSeller';
        $data['menu'] = 'Sebarvctukang';
        $data['city'] = $this->MCity->getById($id_city);
        $data['tukangs'] = $this->db->get_where('tb_tukang', ['id_city' => $id_city])->result_array();
        $data['vctukangs'] = $this->MVoucherTukang->getVoucherDigital($id_city);
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Tukang/VoucherList');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function create_vc($id_city)
    {
        $post = $this->input->post();
        $id_tukang = $post['id_tukang'];
        // $rand = rand(1000000000, 9999999999);
        $id_md5 = md5("Top" . md5($id_tukang . date("Y-m-d-H-i-s")));

        $this->db->join('tb_city', 'tb_city.id_city = tb_tukang.id_city');
        $getTukang = $this->db->get_where('tb_tukang', ['id_tukang' => $id_tukang])->row_array();

        $query = $this->MVoucherTukang->createVoucherDigital($id_tukang, 0, 0, 0, $id_md5);

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

            $id_distributor = $getTukang['id_distributor'];

            $haloai = $this->db->get_where('tb_haloai', ['id_distributor' => $id_distributor])->row_array();
            $wa_token = $haloai['token_haloai'];
            $business_id = $haloai['business_id_haloai'];
            $channel_id = $haloai['channel_id_haloai'];
            $template = 'notif_materi_img';

            // Data
            $nomor_hp = $getTukang['nomorhp'];
            $nama = $getTukang['nama'];

            $message = "Halo " . $nama . " *Beli Top Mortar, Kembaliannya bisa buat beli Kopi!*  Dapatkan *Potongan Langsung Rp.10,000* setiap pembelian produk Top Mortar di toko bangunan terdekat. Tunjukan QR ini pada toko saat berbelanja  SK:  QR hanya berlaku 1x Potongan hanya berlaku per nota belanja Berlaku untuk semua produk top mortar ";

            // $image = "https://order.topmortarindonesia.com/assets/img/qr/framed_" . $image_name;

            $haloaiPayload = [
                'activate_ai_after_send' => false,
                'channel_id' => $channel_id,
                "fallback_template_header" => [
                    'filename' => $image_name,
                    'type' => 'image',
                    'url' => "https://order.topmortarindonesia.com/assets/img/qr/framed_" . $image_name,
                ],
                'fallback_template_message' => $template,
                'fallback_template_variables' => [
                    trim(preg_replace('/\s+/', ' ', $message)),
                ],
                "media" => [
                    'filename' => $image_name,
                    'type' => 'image',
                    'url' => "https://order.topmortarindonesia.com/assets/img/qr/framed_" . $image_name,
                ],
                'phone_number' => $nomor_hp,
                'text' => trim(preg_replace('/\s+/', ' ', $message)),
            ];

            // Send message
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
            // echo $response;

            $status = $res['status'];

            if ($status == 'success') {
                $this->session->set_flashdata('success', "Berhasil kirim voucher!");
                redirect('sebarvctukang/' . $id_city);
            } else {
                $this->session->set_flashdata('failed', "Gagal kirim notif voucher! " . json_encode($res));
                redirect('sebarvctukang/' . $id_city);
            }
        } else {
            $this->session->set_flashdata('failed', "Gagal kirim voucher!");
            redirect('sebarvctukang/' . $id_city);
        }
    }
}
