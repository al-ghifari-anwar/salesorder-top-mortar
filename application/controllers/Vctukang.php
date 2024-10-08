<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Vctukang extends CI_Controller
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

    public function index()
    {
        $data['title'] = 'Voucher';
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Vctukang/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function self()
    {
        $data['title'] = 'Voucher Tukang';
        // $data['contact'] = $this->MContact->getById($id_contact);
        $this->db->where('is_display', '1');
        $data['cities'] = $this->db->get('tb_city')->result_array();
        $data['catcus'] = $this->db->get('tb_catcus')->result_array();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Vctukang/Self');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function verify()
    {
        $no_seri = $this->input->post("no_seri");
        $no_seri = "62" . substr($no_seri, 1);

        if ($no_seri == null) {
            $this->session->set_flashdata('failed', "Nomor seri tidak boleh kosong!");
            redirect('vctukang');
        } else {
            $this->db->join('tb_city', 'tb_city.id_city = tb_tukang.id_city');
            $getTukang = $this->db->get_where('tb_tukang', ['nomorhp' => $no_seri])->row_array();

            if (!$getTukang) {
                $this->session->set_flashdata('failed', "Nomor seri tidak terdaftar!");
                redirect('vctukang');
            } else {
                $id_tukang = $getTukang['id_tukang'];

                $getVoucherTukang = $this->db->get_where('tb_voucher_tukang', ['no_seri' => $no_seri])->row_array();

                if ($getTukang['is_demo'] == 0) {

                    if ($getVoucherTukang) {
                        if ($getVoucherTukang['is_claimed'] == 1) {
                            $this->session->set_flashdata('failed', "Nomor seri sudah pernah di claim!");
                            redirect('vctukang');
                        } else {
                            $this->session->set_flashdata('failed', "Nomor seri sudah terverifikasi, silahkan kunjungi toko untuk claim");
                            redirect('vctukang');
                        }
                    } else {
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

                        $image_name = $no_seri . '.png'; //buat name dari qr code sesuai dengan nim

                        $voucherCode = md5("Top" . md5($id_tukang));

                        $params['data'] = $voucherCode; //data yang akan di jadikan QR CODE
                        $params['level'] = 'H'; //H=High
                        $params['size'] = 10;
                        $params['savename'] = FCPATH . $config['imagedir'] . $image_name; //simpan image QR CODE ke folder assets/images/
                        $this->ciqrcode->generate($params); // fungsi untuk generate QR CODE

                        $id_distributor = $getTukang['id_distributor'];
                        $wa_token = '_GEJodr1x8u7-nSn4tZK2hNq0M5CARkRp_plNdL2tFw';
                        $template_id = '781b4601-fba6-4c69-81ad-164a680ecce7';
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
                                                    "value":"https://order.topmortarindonesia.com/assets/img/qr/' . $no_seri . '.png"
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

                            $this->session->set_flashdata('success', "Berhasil verifikasi, silahkan cek QR yang telah kami kirim melalui WhatsApp!");
                            redirect('vctukang');
                        } else {
                            $this->session->set_flashdata('failed', "Gagal memverifikasi nomor seri, silahkan coba lagi!");
                            redirect('vctukang');
                        }
                    }
                } else {
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

                    $image_name = $no_seri . '.png'; //buat name dari qr code sesuai dengan nim

                    $voucherCode = md5("Top" . md5($id_tukang));

                    $params['data'] = $voucherCode; //data yang akan di jadikan QR CODE
                    $params['level'] = 'H'; //H=High
                    $params['size'] = 10;
                    $params['savename'] = FCPATH . $config['imagedir'] . $image_name; //simpan image QR CODE ke folder assets/images/
                    $this->ciqrcode->generate($params); // fungsi untuk generate QR CODE

                    $id_distributor = $getTukang['id_distributor'];
                    $wa_token = '_GEJodr1x8u7-nSn4tZK2hNq0M5CARkRp_plNdL2tFw';
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
                                                    "value":"https://order.topmortarindonesia.com/assets/img/qr/' . $no_seri . '.png"
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

                        $this->session->set_flashdata('success', "Berhasil verifikasi, silahkan cek QR yang telah kami kirim melalui WhatsApp!");
                        redirect('vctukang');
                    } else {
                        $this->session->set_flashdata('failed', "Gagal memverifikasi nomor seri, silahkan coba lagi!");
                        redirect('vctukang');
                    }
                }
            }
        }
    }

    public function verify_self()
    {
        $post = $this->input->post();
        $nomorhp = $post['nomorhp'];
        $nomorhp = "62" . substr($nomorhp, 1);
        $nama = $post['nama'];
        $tgl_lahir = $post['tgl_lahir'];
        $address = $post['address'];
        $id_catcus = $post['id_catcus'];
        $id_city = $post['id_city'];
        $nominal = "0";
        $nota = "error.png";

        if ($nomorhp == null) {
            $this->session->set_flashdata('failed', "Nomor hp tidak boleh kosong!");
            redirect('vctukang/self');
        } else {
            $this->db->join('tb_city', 'tb_city.id_city = tb_tukang.id_city');
            $getTukang = $this->db->get_where('tb_tukang', ['nomorhp' => $nomorhp])->row_array();

            if ($getTukang) {
                $this->session->set_flashdata('failed', "Nomor anda sudah terdaftar dan tidak dapat mendapat potongan lagi");
                redirect('vctukang/self');
            } else {
                $data = [
                    'nama' => $nama,
                    'nomorhp' => $nomorhp,
                    'tgl_lahir' => $tgl_lahir != "" ? date("Y-m-d", strtotime($tgl_lahir)) : "0000-00-00",
                    'id_city' => $id_city,
                    'maps_url' => 0,
                    'address' => $address,
                    'tukang_status' => 'data',
                    'ktp_tukang' => '',
                    'id_skill' => 1,
                    'id_catcus' => $id_catcus,
                    'is_self' => 1
                ];

                $insert = $this->db->insert('tb_tukang', $data);

                $id_tukang = $this->db->insert_id();

                if ($insert) {
                    $this->db->join('tb_city', 'tb_city.id_city = tb_tukang.id_city');
                    $getTukang = $this->db->get_where('tb_tukang', ['nomorhp' => $nomorhp])->row_array();
                    $getVoucherTukang = $this->db->get_where('tb_voucher_tukang', ['no_seri' => $nomorhp])->row_array();

                    if ($getVoucherTukang) {
                        if ($getVoucherTukang['is_claimed'] == 1) {
                            $this->session->set_flashdata('failed', "Nomor sudah pernah di claim!");
                            redirect('vctukang/self');
                        } else {
                            $this->session->set_flashdata('failed', "Nomor sudah terverifikasi, silahkan kunjungi toko untuk claim");
                            redirect('vctukang/self');
                        }
                    } else {
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

                        $image_name = $nomorhp . '.png'; //buat name dari qr code sesuai dengan nim

                        $voucherCode = md5("Top" . md5($id_tukang));

                        $params['data'] = $voucherCode; //data yang akan di jadikan QR CODE
                        $params['level'] = 'H'; //H=High
                        $params['size'] = 10;
                        $params['savename'] = FCPATH . $config['imagedir'] . $image_name; //simpan image QR CODE ke folder assets/images/
                        $this->ciqrcode->generate($params); // fungsi untuk generate QR CODE

                        $id_distributor = $getTukang['id_distributor'];
                        // $wa_token = '_GEJodr1x8u7-nSn4tZK2hNq0M5CARkRp_plNdL2tFw';
                        $template_id = '7bf2d2a0-bdd5-4c70-ba9f-a9665f66a841';
                        $qontak = $this->db->get_where('tb_qontak', ['id_distributor' => $id_distributor])->row_array();
                        $integration_id = $qontak['integration_id'];
                        $wa_token = $qontak['token'];
                        // Data
                        $nomor_hp = $getTukang['nomorhp'];
                        $nama = $getTukang['nama'];


                        $message = $nama . " tukarkan voucher diskon Rp. 10.000 dengan cara tunjukkan qr ini pada toko. ";
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
                                                        "value":"https://order.topmortarindonesia.com/assets/img/qr/' . $nomorhp . '.png"
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
                            $this->MVoucherTukang->create($id_tukang, $nomorhp);

                            $this->session->set_flashdata('success', "Berhasil verifikasi, silahkan cek QR yang telah kami kirim melalui WhatsApp!");
                            redirect('vctukang/self');
                        } else {
                            $this->session->set_flashdata('failed', "Gagal memverifikasi nomor seri, silahkan coba lagi!");
                            redirect('vctukang/self');
                        }
                    }
                }
            }
        }
    }

    public function toko($id_tukang)
    {
        $data['title'] = 'Voucher';
        $data['tukang'] = $this->db->get_where('tb_tukang', ['id_tukang' => $id_tukang])->row_array();
        $data['banks'] = $this->db->get('tb_bank')->result_array();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Vctukang/Toko');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function claim()
    {
        $post = $this->input->post();
        $id_tukang = $post['id_tukang'];
        $to_name = $post['to_name'];
        $id_bank = $post['id_bank'];
        $to_account = $post['to_account'];
        $no_seri = $post['no_seri'];

        $getClaimed = $this->db->get_where('tb_voucher_tukang', ['no_seri' => $no_seri])->row_array();

        if ($getClaimed == null) {
            $getBank = $this->db->get_where('tb_bank', ['id_bank' => $id_bank])->row_array();

            if ($getBank['is_bca'] == 1) {
                // TF intrabank
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://apibca.topmortarindonesia.com/snapIntrabankVctukang.php?to=' . $to_account,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                ));

                $response = curl_exec($curl);

                curl_close($curl);

                $res = json_decode($response, true);

                if ($res['status'] == 'ok') {
                    $data = [
                        'id_tukang' => $id_tukang,
                        'id_contact' => 0,
                        'claim_date' => date("Y-m-d H:i:s"),
                        'no_seri' => $no_seri
                    ];
                    $this->db->insert('tb_voucher_tukang', $data);

                    $this->session->set_flashdata('success', "Berhasil claim voucher");
                    redirect('vctukang/toko/' . $id_tukang);
                } else {
                    $this->session->set_flashdata('failed', "Gagal claim voucher, silahkan coba lagi!");
                    redirect('vctukang/toko/' . $id_tukang);
                }
            } else {
                // TF interbank
                $bank_code = $getBank['swift_bank'];
                $to_name = str_replace(" ", "%20", $to_name);
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://apibca.topmortarindonesia.com/snapInterbankVctukang.php?to=$to_account&to_name=$to_name&bank_code=$bank_code",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                ));

                $response = curl_exec($curl);

                curl_close($curl);

                $res = json_decode($response, true);

                if ($res['status'] == 'ok') {
                    $data = [
                        'id_tukang' => $id_tukang,
                        'id_contact' => 0,
                        'claim_date' => date("Y-m-d H:i:s"),
                        'no_seri' => $no_seri
                    ];
                    $this->db->insert('tb_voucher_tukang', $data);

                    $this->session->set_flashdata('success', "Berhasil claim voucher");
                    redirect('vctukang/toko/' . $id_tukang);
                } else {
                    $this->session->set_flashdata('failed', "Gagal claim voucher, silahkan coba lagi!");
                    redirect('vctukang/toko/' . $id_tukang);
                }
            }
        } else {
            $this->session->set_flashdata('failed', "Nomor seri sudah terpakai!");
            redirect('vctukang/toko/' . $id_tukang);
        }
    }
}
