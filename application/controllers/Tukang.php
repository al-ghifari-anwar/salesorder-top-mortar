<?php
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
        $data['tukangs'] = $this->db->get('tb_tukang')->result_array();
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
        $id_md5 = md5("Top" . md5($id_tukang . date("Y-m-d-H-i-s")));

        $this->db->join('tb_city', 'tb_city.id_city = tb_tukang.id_city');
        $getTukang = $this->db->get_where('tb_tukang', ['id_tukang' => $id_tukang])->row_array();

        $query = $this->MVoucherTukang->createVoucherDigital($id_tukang, 0, 0, 0);

        if ($query) {
            // Generate QR
            $this->load->library('ciqrcode');
            $config['cacheable']    = true; //boolean, the default is true
            $config['cachedir']             = './assets/'; //string, the default is application/cache/
            $config['errorlog']             = './assets/'; //string, the default is application/logs/
            $config['imagedir']             = './assets/img/qr/'; //direktori penyimpanan qr code
            $config['quality']              = true; //boolean, the default is true
            $config['size']                 = '1024'; //interger, the default is 1024
            $config['black']                = array(224, 255, 255); // array, default is array(255,255,255)
            $config['white']                = array(70, 130, 180); // array, default is array(0,0,0)
            $this->ciqrcode->initialize($config);

            $image_name = $id_tukang . date("Y-m-d-H-i-s") . '.png'; //buat name dari qr code sesuai dengan nim

            $voucherCode = $id_md5;

            $params['data'] = $voucherCode; //data yang akan di jadikan QR CODE
            $params['level'] = 'H'; //H=High
            $params['size'] = 10;
            $params['savename'] = FCPATH . $config['imagedir'] . $image_name; //simpan image QR CODE ke folder assets/images/
            $this->ciqrcode->generate($params); // fungsi untuk generate QR CODE

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
                                                    "value":"https://order.topmortarindonesia.com/assets/img/qr/' . $id_tukang . date("Y-m-d-H-i-s") . '.png"
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
