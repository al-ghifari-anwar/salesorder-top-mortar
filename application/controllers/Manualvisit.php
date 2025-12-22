<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Manualvisit extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $this->load->model('MCity');
        $this->load->model('MContact');
        $this->load->model('MUser');
        $this->load->library('form_validation');
        date_default_timezone_set('Asia/Jakarta');
    }

    public function index()
    {
        $data['title'] = 'Manual Visit';
        $data['menuGroup'] = 'Visit';
        $data['menu'] = 'ManualVisit';
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else if ($this->session->userdata('level_user') == 'salesspv') {
            $userCity = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->row_array();
            $nama_city = trim(preg_replace("/\\d+/", "", $userCity['nama_city']));
            $data['city'] = $this->db->like('nama_city', $nama_city)->get_where('tb_city', ['id_distributor' => $this->session->userdata('id_distributor')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Manualvisit/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function city($id_city)
    {
        $data['title'] = 'Manual Visit';
        $data['menuGroup'] = 'Visit';
        $data['menu'] = 'ManualVisit';
        $data['contacts'] = $this->MContact->getAll($id_city);
        $data['users'] = $this->MUser->getAllForManualRenvi($id_city);
        $data['id_city'] = $id_city;
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Manualvisit/Detail');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function insert()
    {
        $post = $this->input->post();

        $id_contact = $post['id_contact'];
        $id_user = $post['id_user'];
        $source_visit = $post['source_visit'];
        $laporan_visit = $post['laporan_visit'];
        $manual_user = $post['manual_user'];
        $is_pay = $post['is_pay'];
        $pay_value = $post['pay_value'];
        $id_city = $post['id_city'];
        $date_visit = $post['date_visit'] . ' ' . date("H:i:s");

        $getContact = $this->MContact->getById($id_contact);

        $id_distributor = $getContact['id_distributor'];

        $getQontak = $this->db->get_where('tb_qontak', ['id_distributor' => $id_distributor])->row_array();

        $data = [
            'id_contact' => $id_contact,
            'distance_visit' => 0.001,
            'laporan_visit' => $laporan_visit,
            'source_visit' => $source_visit,
            'id_user' => $id_user,
            'date_visit' => date("Y-m-d H:i:s", strtotime($date_visit)),
            'is_manual' => 1,
            'manual_user' => $manual_user,
            'is_pay' => $is_pay,
            'pay_value' => $pay_value
        ];

        $insert = $this->db->insert('tb_visit', $data);

        if ($insert) {
            $visitDate = date("Y-m-d");
            if ($source_visit == 'jatem3') {
                $this->db->query("UPDATE tb_rencana_visit SET is_visited = 1, visit_date = '$visitDate' WHERE id_contact = '$id_contact' AND type_rencana = 'jatem'");

                $this->db->query("UPDATE tb_renvis_jatem SET is_visited = 1, visit_date = '$visitDate' WHERE id_contact = '$id_contact' AND type_renvis = 'jatem3'");
            } else {
                if ($source_visit == 'jatem2') {
                    $this->db->query("UPDATE tb_renvis_jatem SET is_visited = 1, visit_date = '$visitDate' WHERE id_contact = '$id_contact' AND type_renvis = 'jatem2'");
                } else if ($source_visit == 'jatem1') {
                    $this->db->query("UPDATE tb_renvis_jatem SET is_visited = 1, visit_date = '$visitDate' WHERE id_contact = '$id_contact' AND type_renvis = 'jatem1'");
                } else if ($source_visit == 'weekly') {
                    $this->db->query("UPDATE tb_rencana_visit SET is_visited = 1, visit_date = '$visitDate' WHERE id_contact = '$id_contact' AND type_rencana = 'tagih_mingguan'");
                }
            }

            // Notif to customer
            // $wa_token = $getQontak['token'];
            // $template_id = '9241bf86-ae94-4aa8-8975-551409af90b9';
            $message = "Terimakasih telah melakukan pembayaran sebesar Rp. " . number_format($pay_value, 0, ',', '.') . ". ";
            if ($pay_value <= 0) {
                $message = "Hari ini kami belum menerima pembayaran mohon dibantu pembayaran nya. Terimakasih";
            }
            $nomor_hp = $getContact['nomorhp'];
            $nama = $getContact['nama'];
            // $integration_id = $getQontak['integration_id'];
            $full_name = "PT Top Mortar Indonesia";

            $haloai = $this->db->get_where('tb_haloai', ['id_distributor' => $id_distributor])->row_array();
            $wa_token = $haloai['token_haloai'];
            $business_id = $haloai['business_id_haloai'];
            $channel_id = $haloai['channel_id_haloai'];
            $template = 'notif_meeting_baru';

            $haloaiPayload = [
                'activate_ai_after_send' => false,
                'channel_id' => $channel_id,
                'fallback_template_message' => $template,
                'fallback_template_variables' => [
                    $nama,
                    trim(preg_replace('/\s+/', ' ', $message)),
                    $full_name,
                ],
                'phone_number' => $nomor_hp,
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

            // echo $response;
            // die;

            $nomor_hp_admin = "6289636224827";
            $nama_admin = "April";

            $message = "Toko " . $nama . "telah melakukan pembayaran sebesar Rp. " . number_format($pay_value, 0, ',', '.') . ". ";
            if ($pay_value <= 0) {
                $message = "Toko " . $nama . " hari ini belum melakukan pembayaran ";
            }

            $haloaiPayload = [
                'activate_ai_after_send' => false,
                'channel_id' => $channel_id,
                'fallback_template_message' => $template,
                'fallback_template_variables' => [
                    $nama_admin,
                    trim(preg_replace('/\s+/', ' ', $message)),
                    $full_name,
                ],
                'phone_number' => $nomor_hp_admin,
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

            $this->session->set_flashdata('success', "Berhasil menambah visit!");
            redirect('manualvisit/' . $id_city);
        } else {
            $this->session->set_flashdata('failed', "Gagal menambah visit!");
            redirect('manualvisit/' . $id_city);
        }
    }
}
