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
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
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
        $data['contact'] = $this->MContact->getAllForVouchers($id_city);
        $data['id_city'] = $id_city;
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Voucher/List');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function laporan_penerima($id_city)
    {
        date_default_timezone_set('Asia/Jakarta');
        $data['city'] = $this->MCity->getById($id_city);
        // $data['dates'] = explode("-", $dateRange);
        // $this->load->view('Stok/Print', $data);
        $data['dateNow'] = date("Y-m-d");
        // $this->db->join('tb_voucher', 'tb_contact.id_contact = tb_voucher.id_contact', 'LEFT');
        // $data['contacts'] = $this->db->get_where('tb_contact', ['is_claimed' => 0, 'tb_contact.id_city' => $id_city])->result_array();
        $data['contacts'] = $this->db->query("SELECT tc.*, MAX(date_voucher) as date_voucher, MAX(exp_date) as exp_date, MAX(id_voucher) as id_voucher, MAX(is_claimed) as is_claimed FROM tb_contact tc LEFT JOIN tb_voucher tv ON tv.id_contact = tc.id_contact WHERE tc.id_city = $id_city GROUP BY tc.id_contact")->result_array();
        // $this->load->view('Voucher/PrintPenerima', $data);
        // PDF
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Voucher/PrintPenerima', $data, true);
        $mpdf->AddPage('P');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    public function laporan_voucher($id_city)
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }

        $post = $this->input->post();
        $berdasarkan = $post["berdasarkan"];

        $data['city'] = $this->MCity->getById($id_city);
        // $data['dates'] = explode("-", $dateRange);
        // $this->load->view('Stok/Print', $data);
        // PDF
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        if ($berdasarkan == 'belum-terima') {
            $data['contact'] = $this->db->query("SELECT * FROM tb_contact LEFT JOIN tb_voucher ON tb_voucher.id_contact = tb_contact.id_contact WHERE tb_voucher.id_voucher IS NULL AND tb_contact.id_city = '$id_city' AND store_status IN ('data','passive','active')")->result_array();
            $html = $this->load->view('Voucher/PrintNotRechieved', $data, true);
        } else if ($berdasarkan == 'expired') {
            $dateNow = date("Y-m-d 23:59:59");
            $data['contact'] = $this->db->query("SELECT * FROM tb_contact LEFT JOIN tb_voucher ON tb_voucher.id_contact = tb_contact.id_contact WHERE tb_voucher.id_voucher IS NOT NULL AND tb_contact.id_city = '$id_city' AND tb_voucher.exp_date < '$dateNow' GROUP BY tb_contact.id_contact")->result_array();
            $html = $this->load->view('Voucher/PrintExpired', $data, true);
        } else if ($berdasarkan == 'claimed') {
            $dateNow = date("Y-m-d 23:59:59");
            $data['voucher'] = $this->db->query("SELECT * FROM tb_voucher JOIN tb_contact ON tb_contact.id_contact = tb_voucher.id_contact WHERE tb_voucher.is_claimed = 1 AND tb_contact.id_city = '$id_city'")->result_array();
            $html = $this->load->view('Voucher/PrintClaimed', $data, true);
        } else if ($berdasarkan == 'not-claimed') {
            $dateNow = date("Y-m-d 00:00:00");
            $data['voucher'] = $this->db->query("SELECT * FROM tb_voucher JOIN tb_contact ON tb_contact.id_contact = tb_voucher.id_contact WHERE tb_voucher.is_claimed = 0 AND tb_contact.id_city = '$id_city' AND tb_voucher.exp_date >= '$dateNow'")->result_array();
            $html = $this->load->view('Voucher/PrintNotClaimed', $data, true);
        }
        $mpdf->AddPage('L');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    public function regist_voucher($id_city)
    {
        date_default_timezone_set('Asia/Jakarta');


        $curl = curl_init();
        $id_contact = $_POST['id_contact'];
        $jml_voucher = $_POST['jml_voucher'];

        $getVoucher = $this->db->get_where('tb_voucher', ['id_contact' => $id_contact, 'is_claimed' => 0])->row_array();

        if ($getVoucher) {
            $this->session->set_flashdata('warning', "Maaf, toko masih memiliki voucher aktif!");
            redirect('voucher-list/' . $id_city);
        }

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://saleswa.topmortarindonesia.com/insertVoucher.php?j=' . $jml_voucher . '&s=' . $id_contact . '&t=m',
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

        $status = $res['status'];

        if ($status == 'ok') {
            // Get Qontak
            $id_distributor = $this->session->userdata('id_distributor');
            $qontak = $this->db->query("SELECT * FROM tb_qontak WHERE id_distributor = '$id_distributor'")->row_array();
            $template_id = "77b9cbfa-4ea7-48d6-a081-da07e7901802";
            $integration_id = $qontak['integration_id'];
            $wa_token = $qontak['token'];
            // $wa_token = 'xz5922BoBI6I9ECLKVZjPMm-7-0sqx0cjIqVVeuWURI';
            // $wa_token = '_GEJodr1x8u7-nSn4tZK2hNq0M5CARkRp_plNdL2tFw';

            // Get Contacts
            $contact = $this->MContact->getById($id_contact);

            // Get Vuchers
            $dateNow = date("m-d");
            $getVoucher = $this->db->query("SELECT * FROM tb_voucher WHERE id_contact = '$id_contact' AND is_claimed = 0 AND date_voucher LIKE '%$dateNow%' ")->result_array();
            $vouchers = "";
            foreach ($getVoucher as $voucherArr) {
                $vouchers .= $voucherArr['no_voucher'] . ",";
            }

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
                                        "to_number": "' . $contact['nomorhp'] . '",
                                        "to_name": "' . $contact['nama'] . '",
                                        "message_template_id": "' . $template_id . '",
                                        "channel_integration_id": "' . $integration_id . '",
                                        "language": {
                                            "code": "id"
                                        },
                                        "parameters": {
                                            "header":{
                                                "format":"VIDEO",
                                                "params": [
                                                    {
                                                        "key":"url",
                                                        "value":"https://saleswa.topmortarindonesia.com/vids/send_voucher.mp4"
                                                    },
                                                    {
                                                        "key":"filename",
                                                        "value":"bday.jpg"
                                                    }
                                                ]
                                            },
                                            "body": [
                                                {
                                                    "key": "1",
                                                    "value": "nama",
                                                    "value_text": "' . $contact['nama'] . '"
                                                },
                                                {
                                                    "key": "2",
                                                    "value": "jml_voucher",
                                                    "value_text": "' . $jml_voucher . '"
                                                },
                                                {
                                                    "key": "3",
                                                    "value": "no_voucher",
                                                    "value_text": "' . $vouchers . '"
                                                },
                                                {
                                                    "key": "4",
                                                    "value": "date_voucher",
                                                    "value_text": "' . date("d M, Y", strtotime("+30 days")) . '"
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
                $this->session->set_flashdata('success', "Berhasil kirim voucher!");
                redirect('voucher-list/' . $id_city);
            } else {
                $this->session->set_flashdata('warning', "Berhasil kirim voucher, tapi notif tidak terkirim!");
                redirect('voucher-list/' . $id_city);
            }
        } else {
            $this->session->set_flashdata('failed', "Gagal kirim voucher!");
            redirect('voucher-list/' . $id_city);
        }
    }

    public function claim()
    {
        $this->form_validation->set_rules('no_voucher1', 'Nomor Voucher', 'required');

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
        // $wa_token = 'xz5922BoBI6I9ECLKVZjPMm-7-0sqx0cjIqVVeuWURI';
        $wa_token = '_GEJodr1x8u7-nSn4tZK2hNq0M5CARkRp_plNdL2tFw';
        $template_id = '85f17083-255d-4340-af32-5dd22f483960';
        // $integration_id = '31c076d5-ac80-4204-adc9-964c9b0c590b';

        $post = $this->input->post();

        $store = $this->MContact->getById($post['id_contact']);
        $id_distributor = $store['id_distributor'];
        $vouchers = $post['vouchers_ori'];

        $qontak = $this->db->get_where('tb_qontak', ['id_distributor' => $id_distributor])->row_array();
        $integration_id = $qontak['integration_id'];
        $wa_token = $qontak['token'];

        // if ($id_distributor == 1) {
        $nomor_hp = '6282131426363';
        // } else {
        //     $nomor_hp = '6281128500888';
        // }

        $nama = "Admin";
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
                $id_contact = $store['id_contact'];
                $this->MVoucher->update_claim($vouchers);
                $this->db->update('tb_rencana_visit', ['is_visited' => 1], ['id_contact' => $id_contact, 'type_rencana' => 'voucher']);
                $this->session->set_flashdata('success', "Berhasil claim voucher!");
                redirect('voucher');
            }
        } else {
            $this->session->set_flashdata('failed', "Gagal claim voucher!");
            redirect('voucher');
        }
    }

    public function regist_manual($id_city)
    {
        date_default_timezone_set('Asia/Jakarta');

        $curl = curl_init();
        $id_contact = $_POST['id_contact'];
        $jml_voucher = $_POST['jml_voucher'];

        $status = 'ok';

        if ($status == 'ok') {
            // Get Qontak
            // $id_distributor = $this->session->userdata('id_distributor');
            $id_distributor = 1;
            $qontak = $this->db->query("SELECT * FROM tb_qontak WHERE id_distributor = '$id_distributor'")->row_array();
            $template_id = "c4504076-8fc7-44a0-9534-9f6ebc3e56e5";
            $integration_id = $qontak['integration_id'];
            $wa_token = 'xz5922BoBI6I9ECLKVZjPMm-7-0sqx0cjIqVVeuWURI';

            // Get Contacts
            $contact = $this->MContact->getById($id_contact);

            // Get Vuchers
            // $dateNow = date("m-d");
            $dateNow = "03-04";
            $getVoucher = $this->db->query("SELECT * FROM tb_voucher WHERE id_contact = '$id_contact' AND is_claimed = 0 AND date_voucher LIKE '%$dateNow%' ")->result_array();
            $vouchers = "";
            foreach ($getVoucher as $voucherArr) {
                $vouchers .= $voucherArr['no_voucher'] . ",";
            }

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
                                        "to_number": "' . $contact['nomorhp'] . '",
                                        "to_name": "' . $contact['nama'] . '",
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
                                                        "value":"https://saleswa.topmortarindonesia.com/img/vc_passive.jpg"
                                                    },
                                                    {
                                                        "key":"filename",
                                                        "value":"bday.jpg"
                                                    }
                                                ]
                                            },
                                            "body": [
                                                {
                                                    "key": "1",
                                                    "value": "nama",
                                                    "value_text": "' . $contact['nama'] . '"
                                                },
                                                {
                                                    "key": "2",
                                                    "value": "jml_voucher",
                                                    "value_text": "' . $jml_voucher . '"
                                                },
                                                {
                                                    "key": "3",
                                                    "value": "no_voucher",
                                                    "value_text": "' . $vouchers . '"
                                                },
                                                {
                                                    "key": "4",
                                                    "value": "date_voucher",
                                                    "value_text": "' . date("d M, Y", strtotime("+30 days")) . '"
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
                // $this->session->set_flashdata('success', "Berhasil kirim voucher!");
                // redirect('voucher-list/' . $id_city);
                echo "Success notif";
                echo $response;
            } else {
                // $this->session->set_flashdata('warning', "Berhasil kirim voucher, tapi notif tidak terkirim!");
                // redirect('voucher-list/' . $id_city);
                echo "Failed notif";
                echo $response;
            }
        } else {
            // $this->session->set_flashdata('failed', "Gagal kirim voucher!");
            // redirect('voucher-list/' . $id_city);
        }
    }
}
