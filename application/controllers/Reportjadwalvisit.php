<?php

class Reportjadwalvisit extends CI_Controller
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
        $this->load->model('MVisit');
        $this->load->model('MRenvi');
        $this->load->model('MInvoice');
        $this->load->model('HTelegram');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['title'] = 'Report Jadwal Visit';
        $data['menuGroup'] = 'Visit';
        $data['menu'] = 'ReportJadwalVisit';
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['city'] = $this->MCity->getById($this->session->userdata('id_city'));
        } else if ($this->session->userdata('level_user') == 'salesspv') {
            $userCity = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->row_array();
            $nama_city = trim(preg_replace("/\\d+/", "", $userCity['nama_city']));
            $data['city'] = $this->db->like('nama_city', $nama_city)->get_where('tb_city', ['id_distributor' => $this->session->userdata('id_distributor')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Reportjadwalvisit/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function print()
    {
        $date = $_GET['date'];
        $id_city = $_GET['ct'];

        $data['city'] = $this->MCity->getById($id_city);

        $cluster = 0;
        if (date('D', strtotime($date)) == 'Mon' || date('D', strtotime($date)) == 'Thu') {
            $cluster = 1;
        } else if (date('D', strtotime($date)) == 'Tue' || date('D', strtotime($date)) == 'Fri') {
            $cluster = 2;
        } else if (date('D', strtotime($date)) == 'Wed' || date('D', strtotime($date)) == 'Sat') {
            $cluster = 3;
        }

        if (date('D', strtotime($date)) == 'Mon') {
            $dayName = 'senin';
        } else if (date('D', strtotime($date)) == 'Tue') {
            $dayName = 'selasa';
        } else if (date('D', strtotime($date)) == 'Wed') {
            $dayName = 'rabu';
        } else if (date('D', strtotime($date)) == 'Thu') {
            $dayName = 'kamis';
        } else if (date('D', strtotime($date)) == 'Fri') {
            $dayName = 'jumat';
        } else if (date('D', strtotime($date)) == 'Sat') {
            $dayName = 'sabtu';
        }

        $data['cluster'] = $cluster;
        $data['dayName'] = $dayName;
        $data['date'] = $date;

        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_jadwal_visit.id_contact');
        $data['jadwalVisits'] = $this->db->get_where('tb_jadwal_visit', ['DATE(date_jadwal_visit)' => $date, 'tb_jadwal_visit.id_city' => $id_city, 'tb_jadwal_visit.cluster_jadwal_visit' => $cluster])->result_array();

        // $tambahanVisit = $this->db->join('tb_contact', 'tb_contact.id_contact = tb_visit.id_contact')->where('tb_contact.id_city', $id_city)->where("tb_visit.id_contact NOT IN (SELECT id_contact FROM tb_jadwal_visit WHERE date_jadwal_visit = '$date')", null, true)->where_not_in('source_visit', ['mg', 'absen_in', 'absen_in_store', 'absen_in_bc'])->where('DATE(tb_visit.date_visit)', $date)->get('tb_visit')->result_array();

        // echo $this->db->last_query();
        // echo "<br>";
        // echo json_encode($tambahanVisit);
        // die;

        $data['tambahanVisits'] = $this->db->join('tb_contact', 'tb_contact.id_contact = tb_visit.id_contact')->where('tb_contact.id_city', $id_city)->where("tb_visit.id_contact NOT IN (SELECT id_contact FROM tb_jadwal_visit WHERE date_jadwal_visit = '$date')", null, true)->where_not_in('source_visit', ['mg', 'absen_in', 'absen_in_store', 'absen_in_bc'])->where('DATE(tb_visit.date_visit)', $date)->get('tb_visit')->result_array();

        // $this->load->view('Reportjadwalvisit/Print', $data);

        // Buat direktori penyimpanan sementara
        // $folderPath = FCPATH . 'assets/tmp/renvis/';
        // Nama file berdasarkan invoice ID + timestamp
        // $fileName = 'renvi_' . $this->session->userdata('id_user') . '_' . time() . '.pdf';
        // $filePath = $folderPath . $fileName;

        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Reportjadwalvisit/Print', $data, true);
        $mpdf->AddPage('P');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
        // $mpdf->Output($filePath, \Mpdf\Output\Destination::FILE);

        // $user = $this->db->get_where('tb_user', ['id_user' => $this->session->userdata('id_user')])->row_array();
    }

    public function sendNotif()
    {
        $this->output->set_content_type('application/json');

        // $chatId = "-5015093066";
        $chatId = "-1003589286815";

        $date = date('Y-m-d');

        $citys = $this->db->where('is_notif_jadwal', 1)->get('tb_city')->result_array();

        $resposneTele = array();

        foreach ($citys as $city) {
            $id_city = $city['id_city'];

            $data['city'] = $this->MCity->getById($id_city);

            $cluster = 0;
            if (date('D', strtotime($date)) == 'Mon' || date('D', strtotime($date)) == 'Thu') {
                $cluster = 1;
            } else if (date('D', strtotime($date)) == 'Tue' || date('D', strtotime($date)) == 'Fri') {
                $cluster = 2;
            } else if (date('D', strtotime($date)) == 'Wed' || date('D', strtotime($date)) == 'Sat') {
                $cluster = 3;
            }

            if (date('D', strtotime($date)) == 'Mon') {
                $dayName = 'senin';
            } else if (date('D', strtotime($date)) == 'Tue') {
                $dayName = 'selasa';
            } else if (date('D', strtotime($date)) == 'Wed') {
                $dayName = 'rabu';
            } else if (date('D', strtotime($date)) == 'Thu') {
                $dayName = 'kamis';
            } else if (date('D', strtotime($date)) == 'Fri') {
                $dayName = 'jumat';
            } else if (date('D', strtotime($date)) == 'Sat') {
                $dayName = 'sabtu';
            }

            $data['cluster'] = $cluster;
            $data['dayName'] = $dayName;
            $data['date'] = $date;

            $this->db->join('tb_contact', 'tb_contact.id_contact = tb_jadwal_visit.id_contact');
            $data['jadwalVisits'] = $this->db->get_where('tb_jadwal_visit', ['DATE(date_jadwal_visit)' => $date, 'tb_jadwal_visit.id_city' => $id_city, 'tb_jadwal_visit.cluster_jadwal_visit' => $cluster])->result_array();

            $data['tambahanVisits'] = $this->db->join('tb_contact', 'tb_contact.id_contact = tb_visit.id_contact')->where('tb_contact.id_city', $id_city)->where("tb_visit.id_contact NOT IN (SELECT id_contact FROM tb_jadwal_visit WHERE date_jadwal_visit = '$date')", null, true)->where_not_in('source_visit', ['mg', 'absen_in', 'absen_in_store', 'absen_in_bc'])->where('DATE(tb_visit.date_visit)', $date)->get('tb_visit')->result_array();

            // $this->load->view('Jadwalvisit/Print', $data);

            // Buat direktori penyimpanan sementara
            $folderPath = FCPATH . 'assets/tmp/report_visit/';
            // Nama file berdasarkan invoice ID + timestamp
            $fileName = "report_visit_" . $city['nama_city'] . '_' . date('d F Y') . '_' . time() . '.pdf';
            $filePath = $folderPath . $fileName;
            $fileUrl = "https://order.topmortarindonesia.com/assets/tmp/report_visit/" . $fileName;

            $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
            $mpdf->SetMargins(0, 0, 5);
            $html = $this->load->view('Reportjadwalvisit/Print', $data, true);
            $mpdf->AddPage('P');
            $mpdf->WriteHTML($html);
            // $mpdf->Output();
            $mpdf->Output($filePath, \Mpdf\Output\Destination::FILE);

            $message = "*Report Jadwal Visit*\nKota: *" . $city['nama_city'] . "*\nTanggal: *" . date('d F Y') . "*";

            $send = $this->HTelegram->sendDocumentGroup($chatId, $message, $fileUrl);

            array_push($resposneTele, $send);
        }

        $result = [
            'code' => 200,
            'status' => 'ok',
            'msg' => 'Success',
            'data' => $resposneTele,
        ];

        return $this->output->set_output(json_encode($result));
    }

    public function confirmVisit()
    {
        $date = date('Y-m-d');

        $cluster = 0;
        if (date('D', strtotime($date)) == 'Mon' || date('D', strtotime($date)) == 'Thu') {
            $cluster = 1;
        } else if (date('D', strtotime($date)) == 'Tue' || date('D', strtotime($date)) == 'Fri') {
            $cluster = 2;
        } else if (date('D', strtotime($date)) == 'Wed' || date('D', strtotime($date)) == 'Sat') {
            $cluster = 3;
        }

        if (date('D', strtotime($date)) == 'Mon') {
            $dayName = 'senin';
        } else if (date('D', strtotime($date)) == 'Tue') {
            $dayName = 'selasa';
        } else if (date('D', strtotime($date)) == 'Wed') {
            $dayName = 'rabu';
        } else if (date('D', strtotime($date)) == 'Thu') {
            $dayName = 'kamis';
        } else if (date('D', strtotime($date)) == 'Fri') {
            $dayName = 'jumat';
        } else if (date('D', strtotime($date)) == 'Sat') {
            $dayName = 'sabtu';
        }

        $data['cluster'] = $cluster;
        $data['dayName'] = $dayName;
        $data['date'] = $date;

        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_jadwal_visit.id_contact');
        $jadwalVisits = $this->db->get_where('tb_jadwal_visit', ['DATE(date_jadwal_visit)' => $date, 'tb_jadwal_visit.cluster_jadwal_visit' => $cluster])->result_array();

        foreach ($jadwalVisits as $jadwalVisit) {
            $id_jadwal_visit = $jadwalVisit['id_jadwal_visit'];

            $id_contact = $jadwalVisit['id_contact'];
            $contact = $this->db->get_where('tb_contact', ['id_contact' => $id_contact])->row_array();

            $id_city = $contact['id_city'];

            $city = $this->db->get_where('tb_city', ['id_city' => $id_city])->row_array();

            $visit = $this->db->get_where('tb_visit', ['id_contact' => $id_contact, 'DATE(date_visit)' => date('Y-m-d', strtotime($date))])->row_array();

            $is_visited = 0;
            $id_distributor = $city['id_distributor'];

            $has_session = "";

            if ($jadwalVisit['kategori_jadwal_visit'] == 'Toko Baru' || $jadwalVisit['kategori_jadwal_visit'] == 'passive' || $jadwalVisit['kategori_jadwal_visit'] == 'Akan passive') {
                if ($visit) {
                    $haloai = $this->db->get_where('tb_haloai', ['id_distributor' => $id_distributor])->row_array();
                    $wa_token = $haloai['token_haloai'];
                    $business_id = $haloai['business_id_haloai'];

                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://www.haloai.co.id/api/open/room/v1/details?phoneNumber=' . $contact['nomorhp'],
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_HTTPHEADER => array(
                            'Authorization: Bearer ' . $wa_token,
                            'X-HaloAI-Business-Id: ' . $business_id,
                            'Content-Type: application/json'
                        ),
                    ));

                    $responseLastMsg = curl_exec($curl);

                    curl_close($curl);

                    $resLastMsg = json_decode($responseLastMsg, true);

                    if (isset($resLastMsg['data'])) {
                        if (isset($resLastMsg['data']['sessionStatus'])) {
                            if ($resLastMsg['data']['sessionStatus'] != 'expired') {
                                $is_visited = 1;
                                $status_color = 'text-green';
                                $has_session = "yes" . $contact['nomorhp'];
                            }
                        } else {
                            $has_session = "no" . $contact['nomorhp'];
                        }
                    }

                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://www.haloai.co.id/api/open/room/v1/details?phoneNumber=' . $contact['nomorhp_2'],
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_HTTPHEADER => array(
                            'Authorization: Bearer ' . $wa_token,
                            'X-HaloAI-Business-Id: ' . $business_id,
                            'Content-Type: application/json'
                        ),
                    ));

                    $responseLastMsg = curl_exec($curl);

                    curl_close($curl);

                    $resLastMsg = json_decode($responseLastMsg, true);

                    if (isset($resLastMsg['data'])) {
                        if (isset($resLastMsg['data']['sessionStatus'])) {
                            if ($resLastMsg['data']['sessionStatus'] != 'expired') {
                                $is_visited = 1;
                                $status_color = 'text-green';
                                $has_session = "yes" . $contact['nomorhp'];
                            }
                        } else {
                            $has_session = "no" . $contact['nomorhp'];
                        }
                    }
                }
            } else {
                if ($visit) {
                    $status_color = 'text-green';
                    $is_visited = 1;
                }
            }

            $updateJadwal = $this->db->update('tb_jadwal_visit', ['is_yes' => $is_visited], ['id_jadwal_visit' => $id_jadwal_visit]);

            if ($updateJadwal) {
                $result = [
                    'code' => 200,
                    'status' => 'ok',
                    'msg' => 'Success',
                ];

                return $this->output->set_output(json_encode($result));
            } else {
                $result = [
                    'code' => 400,
                    'status' => 'failed',
                    'msg' => 'Failed',
                ];

                return $this->output->set_output(json_encode($result));
            }
        }
    }
}
