<?php

use Endroid\QrCode\QrCode;

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
        $this->load->library('form_validation');
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
    }

    public function index()
    {
        $data['title'] = 'Data Tukang Top Mortar';
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['tukangs'] = $this->db->get('tb_tukang', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $data['tukangs'] = $this->db->get('tb_tukang')->result_array();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Tukang/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function sebar_vc_city()
    {
        $data['title'] = 'Sebar Voucher Tukang';
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
            $image_name = $id_tukang . date("Y-m-d") . '.png'; //buat name dari qr code sesuai dengan nim

            $voucherCode = $id_md5;
            // Generate QR
            $qrCode = new QrCode();
            $qrCode->setText($voucherCode)
                ->setSize(300)
                ->setPadding(10)
                ->setErrorCorrection('high')
                ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
                ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
                // Path to your logo with transparency
                ->setLogo(FCPATH . "./assets/img/logo_retina.png")
                // Set the size of your logo, default is 48
                ->setLogoSize(98)
                ->setImageType(QrCode::IMAGE_TYPE_PNG);

            $image_name = $id_tukang . date("Y-m-d") . '.png';
            $qrCode->save(FCPATH . "./assets/img/qr/" . $image_name);

            $id_distributor = $getTukang['id_distributor'];
            // $wa_token = '_GEJodr1x8u7-nSn4tZK2hNq0M5CARkRp_plNdL2tFw';
            // $template_id = '781b4601-fba6-4c69-81ad-164a680ecce7';
            $template_id = '7bf2d2a0-bdd5-4c70-ba9f-a9665f66a841';
            $qontak = $this->db->get_where('tb_qontak', ['id_distributor' => $id_distributor])->row_array();
            $integration_id = $qontak['integration_id'];
            $wa_token = $qontak['token'];
            // Data
            $nomor_hp = $getTukang['nomorhp'];
            $nama = $getTukang['nama'];


            $message = "Halo " . $nama . " tukarkan voucher diskon Rp. 10.000 dengan cara tunjukkan qr ini pada toko. ";
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
                                                    "value":"https://order.topmortarindonesia.com/assets/img/qr/' . $id_tukang . date("Y-m-d") . '.png"
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

            if ($status == 'success') {
                $this->session->set_flashdata('success', "Berhasil kirim voucher!");
                redirect('sebarvctukang/' . $id_city);
            } else {
                $this->session->set_flashdata('failed', "Gagal kirim notif voucher! " . $res['error']['messages'][0] . $id_distributor);
                redirect('sebarvctukang/' . $id_city);
            }
        } else {
            $this->session->set_flashdata('failed', "Gagal kirim voucher!");
            redirect('sebarvctukang/' . $id_city);
        }
    }
}
