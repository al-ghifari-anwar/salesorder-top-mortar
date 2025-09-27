<?php

class Checklistrenvi extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MRenvi');

        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }

        if ($this->session->userdata('level_user') == 'salesleader' || $this->session->userdata('level_user') == 'sales' || $this->session->userdata('level_user') == 'penagihan') {
            // redirect('');
        } else {
            redirect('');
        }
    }

    public function index()
    {
        $data['title'] = 'Checlist Renvi';
        $data['menuGroup'] = 'ChecklistRenvi';
        $data['menu'] = 'ChecklistRenvi';
        $id_city = $this->session->userdata('id_city');

        $jatem1s = $this->MRenvi->getJatem1($id_city);
        $jatem2s = $this->MRenvi->getJatem2($id_city);
        $jatem3s = $this->MRenvi->getJatem3($id_city);
        $mingguans = $this->MRenvi->getMingguan($id_city);

        $renvis = array();

        foreach ($jatem1s as $jatem1) {
            $id_inv = $jatem1['id_invoice'];
            $count = $this->db->query("SELECT COUNT(*) AS jmlRenvis FROM tb_renvis_jatem WHERE id_invoice = '$id_inv' AND type_renvis = 'jatem1'")->row_array();
            $jatuhTempo = date('d M Y', strtotime("+" . $jatem1['termin_payment'] . " days", strtotime($jatem1['date_invoice'])));
            $date1 = new DateTime(date("Y-m-d"));
            $date2 = new DateTime($jatuhTempo);
            $days  = $date2->diff($date1)->format('%a');
            $operan = "";
            if ($date1 < $date2) {
                $operan = "-";
            }
            $days = $operan . $days;
            $jatem1['days'] = $days;
            $jatem1['jatuh_tempo'] = $jatuhTempo;
            $jatem1['is_new'] = $count['jmlRenvis'] == 1 ? "1" : "0";
            $id_con = $jatem1['id_contact'];
            $dateJatem = date('Y-m-d', strtotime("+" . $jatem1['termin_payment'] . " days", strtotime($jatem1['date_invoice'])));
            $lastVisit = $this->db->query("SELECT * FROM tb_visit WHERE id_contact = '$id_con' AND DATE(date_visit) >= '$dateJatem' AND source_visit IN ('jatem1','jatem2','jatem3','weekly') ORDER BY date_visit DESC LIMIT 1")->row_array();
            $created_at = $jatem1['created_at'];
            $jatem1['created_at'] = $lastVisit == null ? $created_at : $lastVisit['date_visit'];
            $renvis[] = $jatem1;
        }

        foreach ($jatem2s as $jatem2) {
            $id_inv = $jatem2['id_invoice'];
            $count = $this->db->query("SELECT COUNT(*) AS jmlRenvis FROM tb_renvis_jatem WHERE id_invoice = '$id_inv' AND type_renvis = 'jatem2'")->row_array();
            $jatuhTempo = date('d M Y', strtotime("+" . $jatem2['termin_payment'] . " days", strtotime($jatem2['date_invoice'])));
            $date1 = new DateTime(date("Y-m-d"));
            $date2 = new DateTime($jatuhTempo);
            $days  = $date2->diff($date1)->format('%a');
            $operan = "";
            if ($date1 < $date2) {
                $operan = "-";
            }
            $days = $operan . $days;
            $jatem2['days'] = $days;
            $jatem2['jatuh_tempo'] = $jatuhTempo;
            $jatem2['is_new'] = $count['jmlRenvis'] == 1 ? "1" : "0";
            $id_con = $jatem2['id_contact'];
            $dateJatem = date('Y-m-d', strtotime("+" . $jatem2['termin_payment'] . " days", strtotime($jatem2['date_invoice'])));
            $lastVisit = $this->db->query("SELECT * FROM tb_visit WHERE id_contact = '$id_con' AND DATE(date_visit) >= '$dateJatem' AND source_visit IN ('jatem1','jatem2','jatem3','weekly') ORDER BY date_visit DESC LIMIT 1")->row_array();
            $created_at = $jatem2['created_at'];
            $jatem2['created_at'] = $lastVisit == null ? $created_at : $lastVisit['date_visit'];
            $renvis[] = $jatem2;
        }

        foreach ($jatem3s as $jatem3) {
            $id_inv = $jatem3['id_invoice'];
            $count = $this->db->query("SELECT COUNT(*) AS jmlRenvis FROM tb_renvis_jatem WHERE id_invoice = '$id_inv' AND type_renvis = 'jatem3'")->row_array();
            $jatuhTempo = date('d M Y', strtotime("+" . $jatem3['termin_payment'] . " days", strtotime($jatem3['date_invoice'])));
            $date1 = new DateTime(date("Y-m-d"));
            $date2 = new DateTime($jatuhTempo);
            $days  = $date2->diff($date1)->format('%a');
            $operan = "";
            if ($date1 < $date2) {
                $operan = "-";
            }
            $days = $operan . $days;
            $jatem3['days'] = $days;
            $jatem3['jatuh_tempo'] = $jatuhTempo;
            $jatem3['is_new'] = $count['jmlRenvis'] == 1 ? "1" : "0";
            $id_con = $jatem3['id_contact'];
            $dateJatem = date('Y-m-d', strtotime("+" . $jatem3['termin_payment'] . " days", strtotime($jatem3['date_invoice'])));
            $lastVisit = $this->db->query("SELECT * FROM tb_visit WHERE id_contact = '$id_con' AND DATE(date_visit) >= '$dateJatem' AND source_visit IN ('jatem1','jatem2','jatem3','weekly') ORDER BY date_visit DESC LIMIT 1")->row_array();
            $created_at = $jatem3['created_at'];
            $jatem3['created_at'] = $lastVisit == null ? $created_at : $lastVisit['date_visit'];
            $renvis[] = $jatem3;
        }

        foreach ($mingguans as $mingguan) {
            $id_inv = $mingguan['id_invoice'];
            $mingguan['id_renvis_jatem'] = $mingguan['id_rencana_visit'];
            $count = $this->db->query("SELECT COUNT(*) AS jmlRenvis FROM tb_renvis_jatem WHERE id_invoice = '$id_inv' AND type_renvis = 'jatem3'")->row_array();
            $jatuhTempo = date('d M Y', strtotime("+" . $mingguan['termin_payment'] . " days", strtotime($mingguan['date_invoice'])));
            $date1 = new DateTime(date("Y-m-d"));
            $date2 = new DateTime($jatuhTempo);
            $days  = $date2->diff($date1)->format('%a');
            $operan = "";
            if ($date1 < $date2) {
                $operan = "-";
            }
            $days = $operan . $days;
            $mingguan['days'] = $days;
            $mingguan['jatuh_tempo'] = $jatuhTempo;
            $mingguan['is_new'] = $count['jmlRenvis'] == 1 ? "1" : "0";
            $id_con = $mingguan['id_contact'];
            $dateJatem = date('Y-m-d', strtotime("+" . $mingguan['termin_payment'] . " days", strtotime($mingguan['date_invoice'])));
            $lastVisit = $this->db->query("SELECT * FROM tb_visit WHERE id_contact = '$id_con' AND DATE(date_visit) >= '$dateJatem' AND source_visit IN ('jatem1','jatem2','jatem3','weekly') ORDER BY date_visit DESC LIMIT 1")->row_array();
            $created_at = $mingguan['created_at'];
            $mingguan['created_at'] = $lastVisit == null ? $created_at : $lastVisit['date_visit'];
            $mingguan['type_renvis'] = $mingguan['type_rencana'];
            $renvis[] = $mingguan;
        }

        // echo json_encode($renvi);
        // die;

        $data['renvis'] = $renvis;

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('ChecklistRenvi/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function proses()
    {
        $selectedIds = $this->input->post('ids');
        if (empty($selectedIds)) {
            echo "no_data";
            return;
        }

        $id_city = $this->session->userdata('id_city');

        $jatem1s = $this->MRenvi->getJatem1($id_city);
        $jatem2s = $this->MRenvi->getJatem2($id_city);
        $jatem3s = $this->MRenvi->getJatem3($id_city);
        $mingguans = $this->MRenvi->getMingguan($id_city);

        $renvis = array();

        foreach ($jatem1s as $jatem1) {
            $jatem1['selected'] = in_array($jatem1['id_renvis_jatem'], $selectedIds) ? 'yes' : 'no';
            $id_inv = $jatem1['id_invoice'];
            $count = $this->db->query("SELECT COUNT(*) AS jmlRenvis FROM tb_renvis_jatem WHERE id_invoice = '$id_inv' AND type_renvis = 'jatem1'")->row_array();
            $jatuhTempo = date('d M Y', strtotime("+" . $jatem1['termin_payment'] . " days", strtotime($jatem1['date_invoice'])));
            $date1 = new DateTime(date("Y-m-d"));
            $date2 = new DateTime($jatuhTempo);
            $days  = $date2->diff($date1)->format('%a');
            $operan = "";
            if ($date1 < $date2) {
                $operan = "-";
            }
            $days = $operan . $days;
            $jatem1['days'] = $days;
            $jatem1['jatuh_tempo'] = $jatuhTempo;
            $jatem1['is_new'] = $count['jmlRenvis'] == 1 ? "1" : "0";
            $id_con = $jatem1['id_contact'];
            $dateJatem = date('Y-m-d', strtotime("+" . $jatem1['termin_payment'] . " days", strtotime($jatem1['date_invoice'])));
            $lastVisit = $this->db->query("SELECT * FROM tb_visit WHERE id_contact = '$id_con' AND DATE(date_visit) >= '$dateJatem' AND source_visit IN ('jatem1','jatem2','jatem3','weekly') ORDER BY date_visit DESC LIMIT 1")->row_array();
            $created_at = $jatem1['created_at'];
            $jatem1['created_at'] = $lastVisit == null ? $created_at : $lastVisit['date_visit'];
            $renvis[] = $jatem1;
        }

        foreach ($jatem2s as $jatem2) {
            $jatem2['selected'] = in_array($jatem2['id_renvis_jatem'], $selectedIds) ? 'yes' : 'no';
            $id_inv = $jatem2['id_invoice'];
            $count = $this->db->query("SELECT COUNT(*) AS jmlRenvis FROM tb_renvis_jatem WHERE id_invoice = '$id_inv' AND type_renvis = 'jatem2'")->row_array();
            $jatuhTempo = date('d M Y', strtotime("+" . $jatem2['termin_payment'] . " days", strtotime($jatem2['date_invoice'])));
            $date1 = new DateTime(date("Y-m-d"));
            $date2 = new DateTime($jatuhTempo);
            $days  = $date2->diff($date1)->format('%a');
            $operan = "";
            if ($date1 < $date2) {
                $operan = "-";
            }
            $days = $operan . $days;
            $jatem2['days'] = $days;
            $jatem2['jatuh_tempo'] = $jatuhTempo;
            $jatem2['is_new'] = $count['jmlRenvis'] == 1 ? "1" : "0";
            $id_con = $jatem2['id_contact'];
            $dateJatem = date('Y-m-d', strtotime("+" . $jatem2['termin_payment'] . " days", strtotime($jatem2['date_invoice'])));
            $lastVisit = $this->db->query("SELECT * FROM tb_visit WHERE id_contact = '$id_con' AND DATE(date_visit) >= '$dateJatem' AND source_visit IN ('jatem1','jatem2','jatem3','weekly') ORDER BY date_visit DESC LIMIT 1")->row_array();
            $created_at = $jatem2['created_at'];
            $jatem2['created_at'] = $lastVisit == null ? $created_at : $lastVisit['date_visit'];
            $renvis[] = $jatem2;
        }

        foreach ($jatem3s as $jatem3) {
            $jatem3['selected'] = in_array($jatem3['id_renvis_jatem'], $selectedIds) ? 'yes' : 'no';
            $id_inv = $jatem3['id_invoice'];
            $count = $this->db->query("SELECT COUNT(*) AS jmlRenvis FROM tb_renvis_jatem WHERE id_invoice = '$id_inv' AND type_renvis = 'jatem3'")->row_array();
            $jatuhTempo = date('d M Y', strtotime("+" . $jatem3['termin_payment'] . " days", strtotime($jatem3['date_invoice'])));
            $date1 = new DateTime(date("Y-m-d"));
            $date2 = new DateTime($jatuhTempo);
            $days  = $date2->diff($date1)->format('%a');
            $operan = "";
            if ($date1 < $date2) {
                $operan = "-";
            }
            $days = $operan . $days;
            $jatem3['days'] = $days;
            $jatem3['jatuh_tempo'] = $jatuhTempo;
            $jatem3['is_new'] = $count['jmlRenvis'] == 1 ? "1" : "0";
            $id_con = $jatem3['id_contact'];
            $dateJatem = date('Y-m-d', strtotime("+" . $jatem3['termin_payment'] . " days", strtotime($jatem3['date_invoice'])));
            $lastVisit = $this->db->query("SELECT * FROM tb_visit WHERE id_contact = '$id_con' AND DATE(date_visit) >= '$dateJatem' AND source_visit IN ('jatem1','jatem2','jatem3','weekly') ORDER BY date_visit DESC LIMIT 1")->row_array();
            $created_at = $jatem3['created_at'];
            $jatem3['created_at'] = $lastVisit == null ? $created_at : $lastVisit['date_visit'];
            $renvis[] = $jatem3;
        }

        foreach ($mingguans as $mingguan) {
            $id_inv = $mingguan['id_invoice'];
            $mingguan['id_renvis_jatem'] = $mingguan['id_rencana_visit'];
            $mingguan['selected'] = in_array($mingguan['id_renvis_jatem'], $selectedIds) ? 'yes' : 'no';
            $count = $this->db->query("SELECT COUNT(*) AS jmlRenvis FROM tb_renvis_jatem WHERE id_invoice = '$id_inv' AND type_renvis = 'jatem3'")->row_array();
            $jatuhTempo = date('d M Y', strtotime("+" . $mingguan['termin_payment'] . " days", strtotime($mingguan['date_invoice'])));
            $date1 = new DateTime(date("Y-m-d"));
            $date2 = new DateTime($jatuhTempo);
            $days  = $date2->diff($date1)->format('%a');
            $operan = "";
            if ($date1 < $date2) {
                $operan = "-";
            }
            $days = $operan . $days;
            $mingguan['days'] = $days;
            $mingguan['jatuh_tempo'] = $jatuhTempo;
            $mingguan['is_new'] = $count['jmlRenvis'] == 1 ? "1" : "0";
            $id_con = $mingguan['id_contact'];
            $dateJatem = date('Y-m-d', strtotime("+" . $mingguan['termin_payment'] . " days", strtotime($mingguan['date_invoice'])));
            $lastVisit = $this->db->query("SELECT * FROM tb_visit WHERE id_contact = '$id_con' AND DATE(date_visit) >= '$dateJatem' AND source_visit IN ('jatem1','jatem2','jatem3','weekly') ORDER BY date_visit DESC LIMIT 1")->row_array();
            $created_at = $mingguan['created_at'];
            $mingguan['created_at'] = $lastVisit == null ? $created_at : $lastVisit['date_visit'];
            $mingguan['type_renvis'] = $mingguan['type_rencana'];
            $renvis[] = $mingguan;
        }

        // echo json_encode($renvis);
        // die;

        $data['renvis'] = $renvis;

        // Buat direktori penyimpanan sementara
        $folderPath = FCPATH . 'assets/tmp/renvis/';
        // Nama file berdasarkan invoice ID + timestamp
        $fileName = 'renvi_' . $this->session->userdata('id_user') . '_' . time() . '.pdf';
        $filePath = $folderPath . $fileName;

        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('ChecklistRenvi/Print', $data, true);
        $mpdf->AddPage('P');
        $mpdf->WriteHTML($html);
        $mpdf->Output($filePath, \Mpdf\Output\Destination::FILE);
        // $mpdf->Output();

        $user = $this->db->get_where('tb_user', ['id_user' => $this->session->userdata('id_user')])->row_array();

        // Send Message
        $id_distributor = $user['id_distributor'];
        $nomorhp = $user['phone_user'];
        $nama = $user['full_name'];
        $template_id = "bd507a74-4fdf-4692-8199-eb4ed8864bc7";
        $message = "Checklist visit anda";
        $full_name = "-";

        $qontak = $this->db->get_where('tb_qontak', ['id_distributor' => $id_distributor])->row_array();

        $wa_token = $qontak['token'];
        $integration_id = $qontak['integration_id'];

        // Send Invoice
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
                        "to_number": "' . $nomorhp . '",
                        "to_name": "' . $nama . '",
                        "message_template_id": "' . $template_id . '",
                        "channel_integration_id": "' . $integration_id . '",
                        "language": {
                            "code": "id"
                        },
                        "parameters": {
                            "header":{
                                "format":"DOCUMENT",
                                "params": [
                                    {
                                        "key":"url",
                                        "value":"https://order.topmortarindonesia.com/assets/tmp/renvis/' . $fileName . '"
                                    },
                                    {
                                        "key":"filename",
                                        "value":"' . $fileName . '"
                                    }
                                ]
                            },
                            "body": [
                            {
                                "key": "1",
                                "value": "nama",
                                "value_text": "' . $nama . '"
                            },
                            {
                                "key": "2",
                                "value": "message",
                                "value_text": "' . trim(preg_replace('/\s+/', ' ', $message)) . '"
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

        if ($res['status'] == 'success') {
            $this->session->set_flashdata('success', "Berhasil mengirim data!");
            redirect('checklistrenvi');
        } else {
            $this->session->set_flashdata('failed', "Gagal mengirim data!");
            redirect('checklistrenvi');
        }
    }
}
