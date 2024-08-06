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
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
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
        $data['contacts'] = $this->MContact->getAll($id_city);
        $data['users'] = $this->MUser->getAllForManualRenvi($id_city);
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

        $getContact = $this->MContact->getById($id_contact);

        $id_distributor = $getContact['id_distributor'];

        $getQontak = $this->db->get_where('tb_qontak', ['id_distributor' => $id_distributor])->row_array();

        $data = [
            'id_contact' => $id_contact,
            'distance_visit' => 0.001,
            'laporan_visit' => $laporan_visit,
            'source_visit' => $source_visit,
            'id_user' => $id_user,
            'date_visit' => date("Y-m-d H:i:s"),
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
            $wa_token = '_GEJodr1x8u7-nSn4tZK2hNq0M5CARkRp_plNdL2tFw';
            $template_id = '85f17083-255d-4340-af32-5dd22f483960';
            $message = "Terimakasih telah melakukan pembayaran sebesar Rp. " . number_format($pay_value, 0, ',', '.') . ". ";
            $nomor_hp = $getContact['nomorhp'];
            $nama = $getContact['nama'];
            $integration_id = $getQontak['integration_id'];
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

            // echo $response;
            // die;

            $this->session->set_flashdata('success', "Berhasil menambah visit!");
            redirect('manualvisit');
        } else {
            $this->session->set_flashdata('failed', "Gagal menambah visit!");
            redirect('manualvisit');
        }
    }
}
