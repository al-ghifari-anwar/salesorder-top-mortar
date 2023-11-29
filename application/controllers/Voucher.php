<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Voucher extends CI_Controller
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
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['title'] = 'Voucher';
        $data['city'] = $this->MCity->getAll();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Voucher/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function list_voucher($id_city)
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Voucher List';
        $data['city'] = $this->MCity->getAll();
        $data['voucher'] = $this->MVoucher->getByCity($id_city);
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Voucher/List');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function regist_voucher($id_city)
    {
        $this->form_validation->set_rules('no_voucher', 'Nomor Voucher', 'required|is_unique[tb_voucher.no_voucher]');

        $data['title'] = 'Register Voucher';
        $data['id_city'] = $id_city;

        if ($this->form_validation->run() == false) {
            $data['store'] = $this->MContact->getAll($id_city);
            $this->load->view('Theme/Header', $data);
            $this->load->view('Theme/Menu');
            $this->load->view('Voucher/Register');
            $this->load->view('Theme/Footer');
            $this->load->view('Theme/Scripts');
        } else {
            $insert = $this->MVoucher->insert();

            if ($insert) {
                $this->session->set_flashdata('success', "Berhasil menyimpan voucher!");
                redirect('reg-voucher/' . $id_city);
            } else {
                $this->session->set_flashdata('failed', "Gagal menyimpan voucher!");
                redirect('reg-voucher/' . $id_city);
            }
        }
    }

    public function claim()
    {
        $this->form_validation->set_rules('no_voucher', 'Nomor Voucher', 'required');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'Claim Voucher';
            $this->load->view('Theme/Header', $data);
            $this->load->view('Theme/Menu');
            $this->load->view('Voucher/ClaimForm');
            $this->load->view('Theme/Footer');
            $this->load->view('Theme/Scripts');
        } else {
            $data['title'] = 'Claim Voucher';
            $claimed = $this->MVoucher->getByNomor();
            $data['claimed'] = $claimed;
            $data['toko'] = $this->MContact->getById($claimed['id_contact']);

            $this->load->view('Theme/Header', $data);
            $this->load->view('Theme/Menu');
            $this->load->view('Voucher/Claimed');
            $this->load->view('Theme/Footer');
            $this->load->view('Theme/Scripts');
        }
    }

    public function claimed()
    {
        $wa_token = 'xz5922BoBI6I9ECLKVZjPMm-7-0sqx0cjIqVVeuWURI';
        $template_id = '85f17083-255d-4340-af32-5dd22f483960';
        $integration_id = '31c076d5-ac80-4204-adc9-964c9b0c590b';

        $post = $this->input->post();

        $store = $this->MContact->getById($post['id_contact']);
        $vouchers = $post['vouchers_ori'];

        $nomor_hp = "6282131426363";
        $nama = "Bella";
        $message = "Claim voucher dari toko " . $store['nama'] . " sebanyak " . $post['actual_vouchers'] . " point. Kode voucher: " . $vouchers;
        $full_name = "Automated Message";

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
                    "body": [
                    {
                        "key": "1",
                        "value": "nama",
                        "value_text": "' . $nama . '"
                    },
                    {
                        "key": "2",
                        "value": "message",
                        "value_text": "' . $message . '"
                    },
                    {
                        "key": "3",
                        "value": "sales",
                        "value_text": "' . $full_name . '"
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

        $status = $res['status'];

        if ($status == 'success') {
            $nomor_hp = $store['nomorhp'];
            $nama = $store['nama'];
            $message = "Anda telah claim voucher sebanyak " . $post['actual_vouchers'] . " point. Kode voucher: " . $vouchers;
            $full_name = "PT Top Mortar Indonesia";

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
                        "body": [
                        {
                            "key": "1",
                            "value": "nama",
                            "value_text": "' . $nama . '"
                        },
                        {
                            "key": "2",
                            "value": "message",
                            "value_text": "' . $message . '"
                        },
                        {
                            "key": "3",
                            "value": "sales",
                            "value_text": "' . $full_name . '"
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

            $status = $res['status'];

            if ($status == 'success') {

                $this->session->set_flashdata('success', "Berhasil claim voucher!");
                redirect('voucher');
            }
        } else {
            $this->session->set_flashdata('failed', "Gagal claim voucher!");
            redirect('voucher');
        }
    }
}
