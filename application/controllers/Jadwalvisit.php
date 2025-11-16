<?php

class Jadwalvisit extends CI_Controller
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
        $this->load->library('form_validation');
    }

    public function index()
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Jadwal Visit Baru';
        $data['menuGroup'] = 'Visit';
        $data['menu'] = 'JadwalVisit';
        if ($this->session->userdata('level_user') == 'admin_c' || $this->session->userdata('level_user') == 'sales') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Jadwalvisit/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function print()
    {
        $id_city = $_GET['ct'];

        $data['city'] = $this->MCity->getById($id_city);

        $cluster = 0;
        if (date('D') == 'Mon' || date('D') == 'Thu') {
            $cluster = 1;
        } else if (date('D') == 'Tue' || date('D') == 'Fri') {
            $cluster = 2;
        } else if (date('D') == 'Wed' || date('D') == 'Sat') {
            $cluster = 3;
        }

        if (date('D') == 'Mon') {
            $dayName = 'senin';
        } else if (date('D') == 'Tue') {
            $dayName = 'selasa';
        } else if (date('D') == 'Wed') {
            $dayName = 'rabu';
        } else if (date('D') == 'Thu') {
            $dayName = 'kamis';
        } else if (date('D') == 'Fri') {
            $dayName = 'jumat';
        } else if (date('D') == 'Sat') {
            $dayName = 'sabtu';
        }

        $data['cluster'] = $cluster;
        $data['dayName'] = $dayName;

        $jatem1s = $this->MRenvi->getJatem1($id_city);
        $jatem2s = $this->MRenvi->getJatem2($id_city);
        $jatem3s = $this->MRenvi->getJatem3($id_city);
        $mingguans = $this->MRenvi->getMingguan($id_city);
        $passives = $this->MRenvi->getPassive($id_city);

        $renvis = array();
        $renvisPassives = array();

        foreach ($jatem1s as $jatem1) {
            $id_inv = $jatem1['id_invoice'];
            $count = $this->db->query("SELECT COUNT(*) AS jmlRenvis FROM tb_renvis_jatem WHERE id_invoice = '$id_inv' AND type_renvis = 'jatem1'")->row_array();
            $jatuhTempo = date('d M Y', strtotime("+" . $jatem1['termin_payment'] . " days", strtotime($jatem1['date_invoice'])));
            $jatem = date('Y-m-d', strtotime("+" . $jatem1['termin_payment'] . " days", strtotime($jatem1['date_invoice'])));
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
            $jatem1['jatem'] = $jatem;
            $jatem1['is_new'] = $count['jmlRenvis'] == 1 ? "1" : "0";
            $id_con = $jatem1['id_contact'];
            $dateJatem = date('Y-m-d', strtotime("+" . $jatem1['termin_payment'] . " days", strtotime($jatem1['date_invoice'])));
            //  AND DATE(date_visit) >= '$dateJatem'
            $lastVisit = $this->db->query("SELECT * FROM tb_visit WHERE id_contact = '$id_con' AND source_visit IN ('jatem1','jatem2','jatem3','weekly','passive','normal') ORDER BY date_visit DESC LIMIT 1")->row_array();
            $created_at = $jatem1['created_at'];
            $jatem1['created_at'] = $lastVisit == null ? $created_at : $lastVisit['date_visit'];
            $renvis[] = $jatem1;
        }

        foreach ($jatem2s as $jatem2) {
            $id_inv = $jatem2['id_invoice'];
            $count = $this->db->query("SELECT COUNT(*) AS jmlRenvis FROM tb_renvis_jatem WHERE id_invoice = '$id_inv' AND type_renvis = 'jatem2'")->row_array();
            $jatuhTempo = date('d M Y', strtotime("+" . $jatem2['termin_payment'] . " days", strtotime($jatem2['date_invoice'])));
            $jatem = date('Y-m-d', strtotime("+" . $jatem2['termin_payment'] . " days", strtotime($jatem2['date_invoice'])));
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
            $jatem2['jatem'] = $jatem;
            $jatem2['is_new'] = $count['jmlRenvis'] == 1 ? "1" : "0";
            $id_con = $jatem2['id_contact'];
            $dateJatem = date('Y-m-d', strtotime("+" . $jatem2['termin_payment'] . " days", strtotime($jatem2['date_invoice'])));
            //  AND DATE(date_visit) >= '$dateJatem'
            $lastVisit = $this->db->query("SELECT * FROM tb_visit WHERE id_contact = '$id_con' AND source_visit IN ('jatem1','jatem2','jatem3','weekly','passive','normal') ORDER BY date_visit DESC LIMIT 1")->row_array();
            $created_at = $jatem2['created_at'];
            $jatem2['created_at'] = $lastVisit == null ? $created_at : $lastVisit['date_visit'];
            $renvis[] = $jatem2;
        }

        foreach ($jatem3s as $jatem3) {
            $id_inv = $jatem3['id_invoice'];
            $count = $this->db->query("SELECT COUNT(*) AS jmlRenvis FROM tb_renvis_jatem WHERE id_invoice = '$id_inv' AND type_renvis = 'jatem3'")->row_array();
            $jatuhTempo = date('d M Y', strtotime("+" . $jatem3['termin_payment'] . " days", strtotime($jatem3['date_invoice'])));
            $jatem = date('Y-m-d', strtotime("+" . $jatem3['termin_payment'] . " days", strtotime($jatem3['date_invoice'])));
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
            $jatem3['jatem'] = $jatem;
            $jatem3['is_new'] = $count['jmlRenvis'] == 1 ? "1" : "0";
            $id_con = $jatem3['id_contact'];
            $dateJatem = date('Y-m-d', strtotime("+" . $jatem3['termin_payment'] . " days", strtotime($jatem3['date_invoice'])));
            //  AND DATE(date_visit) >= '$dateJatem'
            $lastVisit = $this->db->query("SELECT * FROM tb_visit WHERE id_contact = '$id_con' AND source_visit IN ('jatem1','jatem2','jatem3','weekly','passive','normal') ORDER BY date_visit DESC LIMIT 1")->row_array();
            $created_at = $jatem3['created_at'];
            $jatem3['created_at'] = $lastVisit == null ? $created_at : $lastVisit['date_visit'];
            $renvis[] = $jatem3;
        }

        foreach ($mingguans as $mingguan) {
            $id_inv = $mingguan['id_invoice'];
            $mingguan['id_renvis_jatem'] = $mingguan['id_rencana_visit'];
            $count = $this->db->query("SELECT COUNT(*) AS jmlRenvis FROM tb_renvis_jatem WHERE id_invoice = '$id_inv' AND type_renvis = 'jatem3'")->row_array();
            $jatuhTempo = date('d M Y', strtotime("+" . $mingguan['termin_payment'] . " days", strtotime($mingguan['date_invoice'])));
            $jatem = date('Y-m-d', strtotime("+" . $mingguan['termin_payment'] . " days", strtotime($mingguan['date_invoice'])));
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
            $mingguan['jatem'] = $jatem;
            $mingguan['is_new'] = $count['jmlRenvis'] == 1 ? "1" : "0";
            $id_con = $mingguan['id_contact'];
            $dateJatem = date('Y-m-d', strtotime("+" . $mingguan['termin_payment'] . " days", strtotime($mingguan['date_invoice'])));
            // $dateInvoice = date('Y-m-d', strtotime($mingguan['date_invoice']));
            //  AND DATE(date_visit) >= '$dateJatem'
            $lastVisit = $this->db->query("SELECT * FROM tb_visit WHERE id_contact = '$id_con' AND source_visit IN ('jatem1','jatem2','jatem3','weekly','passive','normal') ORDER BY date_visit DESC LIMIT 1")->row_array();
            $created_at = $mingguan['created_at'];
            $mingguan['created_at'] = $lastVisit == null ? $created_at : $lastVisit['date_visit'];
            $mingguan['type_renvis'] = $mingguan['type_rencana'];
            $renvis[] = $mingguan;
        }

        foreach ($passives as $passive) {
            $id_con = $passive['id_contact'];
            $count = $this->db->query("SELECT COUNT(*) AS jmlRenvis FROM tb_rencana_visit WHERE id_contact = '$id_con' AND type_rencana = 'passive'")->row_array();
            $date_margin = date("Y-m-d", strtotime("-1 month"));
            $lastVisit = $this->db->query("SELECT * FROM tb_visit WHERE id_contact = '$id_con' AND source_visit IN ('jatem1','jatem2','jatem3','weekly','voucher','passive','renvisales','normal') AND date_visit >= '$date_margin' ORDER BY date_visit DESC LIMIT 1")->row_array();
            $passive['last_visit'] = $lastVisit == null ? '0000-00-00' : $lastVisit['date_visit'];
            $created_at = $passive['created_at'];
            $passive['created_at'] = $lastVisit == null ? $created_at : $lastVisit['date_visit'];
            $passive['is_new'] = $count['jmlRenvis'] == 1 ? "1" : "0";
            $passive['type_renvis'] = $passive['type_rencana'];

            $getBadScore = $this->db->query("SELECT * FROM tb_bad_score WHERE id_contact = '$id_con'")->row_array();

            if ($getBadScore) {
                if ($getBadScore['is_approved'] != 1) {
                    $renvisPassives[] = $passive;
                }
            } else {
                $renvisPassives[] = $passive;
            }
        }

        $data['renvis'] = $renvis;
        $data['renvisPassives'] = $renvisPassives;

        // $this->load->view('Jadwalvisit/Print', $data);

        // Buat direktori penyimpanan sementara
        // $folderPath = FCPATH . 'assets/tmp/renvis/';
        // Nama file berdasarkan invoice ID + timestamp
        // $fileName = 'renvi_' . $this->session->userdata('id_user') . '_' . time() . '.pdf';
        // $filePath = $folderPath . $fileName;

        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Jadwalvisit/Print', $data, true);
        $mpdf->AddPage('L');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
        // $mpdf->Output($filePath, \Mpdf\Output\Destination::FILE);

        // $user = $this->db->get_where('tb_user', ['id_user' => $this->session->userdata('id_user')])->row_array();
    }

    public function save()
    {
        $this->db->where_in('id_distributor', [1, 5]);
        $citys = $this->db->get('tb_city')->result_array();

        foreach ($citys as $city) {
            $id_city = $city['id_city'];

            // $data['city'] = $this->MCity->getById($id_city);

            $cluster = 0;
            if (date('D') == 'Mon' || date('D') == 'Thu') {
                $cluster = 1;
            } else if (date('D') == 'Tue' || date('D') == 'Fri') {
                $cluster = 2;
            } else if (date('D') == 'Wed' || date('D') == 'Sat') {
                $cluster = 3;
            }

            if (date('D') == 'Mon') {
                $dayName = 'senin';
            } else if (date('D') == 'Tue') {
                $dayName = 'selasa';
            } else if (date('D') == 'Wed') {
                $dayName = 'rabu';
            } else if (date('D') == 'Thu') {
                $dayName = 'kamis';
            } else if (date('D') == 'Fri') {
                $dayName = 'jumat';
            } else if (date('D') == 'Sat') {
                $dayName = 'sabtu';
            }

            $data['cluster'] = $cluster;
            $data['dayName'] = $dayName;

            $jatem1s = $this->MRenvi->getJatem1($id_city);
            $jatem2s = $this->MRenvi->getJatem2($id_city);
            $jatem3s = $this->MRenvi->getJatem3($id_city);
            $mingguans = $this->MRenvi->getMingguan($id_city);
            $passives = $this->MRenvi->getPassive($id_city);

            $renvis = array();
            $renvisPassives = array();

            foreach ($jatem1s as $jatem1) {
                $id_inv = $jatem1['id_invoice'];
                $count = $this->db->query("SELECT COUNT(*) AS jmlRenvis FROM tb_renvis_jatem WHERE id_invoice = '$id_inv' AND type_renvis = 'jatem1'")->row_array();
                $jatuhTempo = date('d M Y', strtotime("+" . $jatem1['termin_payment'] . " days", strtotime($jatem1['date_invoice'])));
                $jatem = date('Y-m-d', strtotime("+" . $jatem1['termin_payment'] . " days", strtotime($jatem1['date_invoice'])));
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
                $jatem1['jatem'] = $jatem;
                $jatem1['is_new'] = $count['jmlRenvis'] == 1 ? "1" : "0";
                $id_con = $jatem1['id_contact'];
                $dateJatem = date('Y-m-d', strtotime("+" . $jatem1['termin_payment'] . " days", strtotime($jatem1['date_invoice'])));
                //  AND DATE(date_visit) >= '$dateJatem'
                $lastVisit = $this->db->query("SELECT * FROM tb_visit WHERE id_contact = '$id_con' AND source_visit IN ('jatem1','jatem2','jatem3','weekly','passive') ORDER BY date_visit DESC LIMIT 1")->row_array();
                $created_at = $jatem1['created_at'];
                $jatem1['created_at'] = $lastVisit == null ? $created_at : $lastVisit['date_visit'];
                $renvis[] = $jatem1;
            }

            foreach ($jatem2s as $jatem2) {
                $id_inv = $jatem2['id_invoice'];
                $count = $this->db->query("SELECT COUNT(*) AS jmlRenvis FROM tb_renvis_jatem WHERE id_invoice = '$id_inv' AND type_renvis = 'jatem2'")->row_array();
                $jatuhTempo = date('d M Y', strtotime("+" . $jatem2['termin_payment'] . " days", strtotime($jatem2['date_invoice'])));
                $jatem = date('Y-m-d', strtotime("+" . $jatem2['termin_payment'] . " days", strtotime($jatem2['date_invoice'])));
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
                $jatem2['jatem'] = $jatem;
                $jatem2['is_new'] = $count['jmlRenvis'] == 1 ? "1" : "0";
                $id_con = $jatem2['id_contact'];
                $dateJatem = date('Y-m-d', strtotime("+" . $jatem2['termin_payment'] . " days", strtotime($jatem2['date_invoice'])));
                //  AND DATE(date_visit) >= '$dateJatem'
                $lastVisit = $this->db->query("SELECT * FROM tb_visit WHERE id_contact = '$id_con' AND source_visit IN ('jatem1','jatem2','jatem3','weekly','passive') ORDER BY date_visit DESC LIMIT 1")->row_array();
                $created_at = $jatem2['created_at'];
                $jatem2['created_at'] = $lastVisit == null ? $created_at : $lastVisit['date_visit'];
                $renvis[] = $jatem2;
            }

            foreach ($jatem3s as $jatem3) {
                $id_inv = $jatem3['id_invoice'];
                $count = $this->db->query("SELECT COUNT(*) AS jmlRenvis FROM tb_renvis_jatem WHERE id_invoice = '$id_inv' AND type_renvis = 'jatem3'")->row_array();
                $jatuhTempo = date('d M Y', strtotime("+" . $jatem3['termin_payment'] . " days", strtotime($jatem3['date_invoice'])));
                $jatem = date('Y-m-d', strtotime("+" . $jatem3['termin_payment'] . " days", strtotime($jatem3['date_invoice'])));
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
                $jatem3['jatem'] = $jatem;
                $jatem3['is_new'] = $count['jmlRenvis'] == 1 ? "1" : "0";
                $id_con = $jatem3['id_contact'];
                $dateJatem = date('Y-m-d', strtotime("+" . $jatem3['termin_payment'] . " days", strtotime($jatem3['date_invoice'])));
                //  AND DATE(date_visit) >= '$dateJatem'
                $lastVisit = $this->db->query("SELECT * FROM tb_visit WHERE id_contact = '$id_con' AND source_visit IN ('jatem1','jatem2','jatem3','weekly','passive') ORDER BY date_visit DESC LIMIT 1")->row_array();
                $created_at = $jatem3['created_at'];
                $jatem3['created_at'] = $lastVisit == null ? $created_at : $lastVisit['date_visit'];
                $renvis[] = $jatem3;
            }

            foreach ($mingguans as $mingguan) {
                $id_inv = $mingguan['id_invoice'];
                $mingguan['id_renvis_jatem'] = $mingguan['id_rencana_visit'];
                $count = $this->db->query("SELECT COUNT(*) AS jmlRenvis FROM tb_renvis_jatem WHERE id_invoice = '$id_inv' AND type_renvis = 'jatem3'")->row_array();
                $jatuhTempo = date('d M Y', strtotime("+" . $mingguan['termin_payment'] . " days", strtotime($mingguan['date_invoice'])));
                $jatem = date('Y-m-d', strtotime("+" . $mingguan['termin_payment'] . " days", strtotime($mingguan['date_invoice'])));
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
                $mingguan['jatem'] = $jatem;
                $mingguan['is_new'] = $count['jmlRenvis'] == 1 ? "1" : "0";
                $id_con = $mingguan['id_contact'];
                $dateJatem = date('Y-m-d', strtotime("+" . $mingguan['termin_payment'] . " days", strtotime($mingguan['date_invoice'])));
                //  AND DATE(date_visit) >= '$dateJatem'
                $lastVisit = $this->db->query("SELECT * FROM tb_visit WHERE id_contact = '$id_con' AND source_visit IN ('jatem1','jatem2','jatem3','weekly','passive','normal') ORDER BY date_visit DESC LIMIT 1")->row_array();
                $created_at = $mingguan['created_at'];
                $mingguan['created_at'] = $lastVisit == null ? $created_at : $lastVisit['date_visit'];
                $mingguan['type_renvis'] = $mingguan['type_rencana'];
                $renvis[] = $mingguan;
            }

            foreach ($passives as $passive) {
                $id_con = $passive['id_contact'];
                $count = $this->db->query("SELECT COUNT(*) AS jmlRenvis FROM tb_rencana_visit WHERE id_contact = '$id_con' AND type_rencana = 'passive'")->row_array();
                $date_margin = date("Y-m-d", strtotime("-1 month"));
                $lastVisit = $this->db->query("SELECT * FROM tb_visit WHERE id_contact = '$id_con' AND source_visit IN ('jatem1','jatem2','jatem3','weekly','voucher','passive','renvisales','mg') AND date_visit >= '$date_margin' ORDER BY date_visit DESC LIMIT 1")->row_array();
                $passive['last_visit'] = $lastVisit == null ? '0000-00-00' : $lastVisit['date_visit'];
                $created_at = $passive['created_at'];
                $passive['created_at'] = $lastVisit == null ? $created_at : $lastVisit['date_visit'];
                $passive['is_new'] = $count['jmlRenvis'] == 1 ? "1" : "0";
                $passive['type_renvis'] = $passive['type_rencana'];

                $getBadScore = $this->db->query("SELECT * FROM tb_bad_score WHERE id_contact = '$id_con'")->row_array();

                if ($getBadScore) {
                    if ($getBadScore['is_approved'] != 1) {
                        $renvisPassives[] = $passive;
                    }
                } else {
                    $renvisPassives[] = $passive;
                }
            }

            $jadwalVisits = array();

            // Filter 1 (Janji Bayar)
            $id_city = $city['id_city'];
            $this->db->join('tb_contact', 'tb_contact.id_contact = tb_visit.id_contact');
            $janjiBayars = $this->db->get_where('tb_visit', ['pay_date' => date('Y-m-d'), 'tb_contact.id_city' => $id_city])->result_array();

            foreach ($janjiBayars as $janjiBayar) {
                if (count($jadwalVisits) <= 14) {
                    $date_visit_janji_bayar = date('Y-m-d', strtotime($janjiBayar['date_visit']));
                    $id_contact = $janjiBayar['id_contact'];

                    $rowLastVisit = $this->db->query("SELECT * FROM tb_visit WHERE id_contact = '$id_contact' AND source_visit IN ('voucher','passive','renvisales','mg','normal','jatem1','jatem2','jatem3','weekly') ORDER BY date_visit DESC LIMIT 1")->row_array();

                    $date_last_for_counter = date('Y-m-d', strtotime($rowLastVisit['date_visit']));
                    $last_visit = date('d M Y', strtotime($rowLastVisit['date_visit']));

                    $lastPayVisit = $this->db->query("SELECT * FROM tb_visit WHERE is_pay = 'pay' AND DATE(date_visit) > '$date_visit_janji_bayar'")->row_array();

                    $date1 = new DateTime(date("Y-m-d"));
                    $date2 = new DateTime($date_last_for_counter);
                    $days  = $date2->diff($date1)->format('%a');
                    $operan = "";
                    if ($date1 < $date2) {
                        $operan = "-";
                    }
                    $days = $operan . $days;

                    $id_invoice = $janjiBayar['id_invoice'];
                    $invoice = $this->MInvoice->getById($id_invoice);
                    // Jatem Days
                    $jatemInv = date('Y-m-d', strtotime("+" . $janjiBayar['termin_payment'] . " days", strtotime($invoice['date_invoice'])));
                    $dateInv1 = new DateTime(date("Y-m-d"));
                    $dateInv2 = new DateTime($jatemInv);
                    $daysInvJatem  = $dateInv2->diff($dateInv1)->format('%a');
                    $operanInvJatem = "";
                    if ($dateInv1 < $dateInv2) {
                        $operanInvJatem = "-";
                    }
                    $daysInvJatem = $operanInvJatem . $daysInvJatem;

                    $payment = $this->db->query("SELECT SUM(amount_payment) AS amount_payment FROM tb_payment WHERE id_invoice = '$id_invoice'")->row_array();
                    $amountPayment = $payment == null ? 0 : $payment['amount_payment'];
                    $sisaHutang = $invoice['total_invoice'] - $amountPayment;

                    $renvisFilter = [
                        'id_contact' => $janjiBayar['id_contact'],
                        'filter' => 'Janji Bayar',
                        'nama' => $janjiBayar['nama'],
                        'type_renvis' => 'Janji Bayar',
                        'last_visit' => $last_visit,
                        'days' => $days,
                        'daysJatem' => $daysInvJatem,
                        'total_invoice' => $sisaHutang,
                        'is_new' => 0,
                    ];

                    // if ($days > 3) {
                    if (!$lastPayVisit) {
                        if (array_search($janjiBayar['id_contact'], array_column($jadwalVisits, 'id_contact')) == "") {
                            array_push($jadwalVisits, $renvisFilter);
                        }
                    }
                    // }
                }
            }

            // Filter 2
            foreach ($renvis as $renvi) {
                $type_renvis = $renvi['type_renvis'];
                $id_contact = $renvi['id_contact'];
                $created_at = date('Y-m-d', strtotime($renvi['created_at']));

                $last_visit = '';
                $date_last_for_counter = '';
                if ($renvi['is_new'] == 1) {
                    if ($type_renvis == 'jatem1') {
                        // $date_last_for_counter = date('Y-m-d', strtotime($renvi['jatem']));
                        // $last_visit = $renvi['jatuh_tempo'];
                        $date_last_for_counter = date('Y-m-d', strtotime($renvi['created_at']));
                        $last_visit = date('d M Y', strtotime($renvi['created_at']));
                    } else if ($type_renvis == 'tagih_mingguan') {
                        $date_last_for_counter = date('Y-m-d', strtotime($renvi['created_at']));
                        $last_visit = date('d M Y', strtotime($renvi['created_at']));
                        // $date_last_for_counter = date('Y-m-d', strtotime($renvi['date_invoice']));
                        // $last_visit = date('d M Y', strtotime($renvi['date_invoice']));
                    } else {
                        $date_last_for_counter = date('Y-m-d', strtotime($renvi['created_at']));
                        $last_visit = date('d M Y', strtotime($renvi['created_at']));
                    }
                } else {
                    $date_last_for_counter = date('Y-m-d', strtotime($renvi['created_at']));
                    $last_visit = date('d M Y', strtotime($renvi['created_at']));
                }

                $date1 = new DateTime(date("Y-m-d"));
                $date2 = new DateTime($date_last_for_counter);
                $days  = $date2->diff($date1)->format('%a');
                $operan = "";
                if ($date1 < $date2) {
                    $operan = "-";
                }
                $days = $operan . $days;

                // Invoice
                $id_invoice = $renvi['id_invoice'];
                $invoices = $this->MInvoice->getByIdInvoiceWaiting($id_invoice);

                $total_invoice = 0;
                foreach ($invoices as $invoice) {
                    $id_invoice = $invoice['id_invoice'];
                    // Jatem Days
                    $jatemInv = date('Y-m-d', strtotime("+" . $invoice['termin_payment'] . " days", strtotime($invoice['date_invoice'])));
                    $dateInv1 = new DateTime(date("Y-m-d"));
                    $dateInv2 = new DateTime($jatemInv);
                    $daysInvJatem  = $dateInv2->diff($dateInv1)->format('%a');
                    $operanInvJatem = "";
                    if ($dateInv1 < $dateInv2) {
                        $operanInvJatem = "-";
                    }
                    $daysInvJatem = $operanInvJatem . $daysInvJatem;

                    $payment = $this->db->query("SELECT SUM(amount_payment) AS amount_payment FROM tb_payment WHERE id_invoice = '$id_invoice'")->row_array();
                    $amountPayment = $payment == null ? 0 : $payment['amount_payment'];
                    $sisaHutang = $invoice['total_invoice'] - $amountPayment;

                    if ($renvi['type_renvis'] != 'tagih_mingguan') {
                        if ($daysInvJatem >= 0) {
                            $total_invoice += $sisaHutang;
                        }
                    } else {
                        if ($daysInvJatem <= 0) {
                            $total_invoice += $sisaHutang;
                        } else {
                            $total_invoice += $sisaHutang;
                        }
                    }
                }

                if ($total_invoice == 0) {
                    $invoices = $this->MInvoice->getByIdContactWaiting($id_contact);
                    foreach ($invoices as $invoice) {
                        $id_invoice = $invoice['id_invoice'];
                        // Jatem Days
                        $jatemInv = date('Y-m-d', strtotime("+" . $invoice['termin_payment'] . " days", strtotime($invoice['date_invoice'])));
                        $dateInv1 = new DateTime(date("Y-m-d"));
                        $dateInv2 = new DateTime($jatemInv);
                        $daysInvJatem  = $dateInv2->diff($dateInv1)->format('%a');
                        $operanInvJatem = "";
                        if ($dateInv1 < $dateInv2) {
                            $operanInvJatem = "-";
                        }
                        $daysInvJatem = $operanInvJatem . $daysInvJatem;

                        $payment = $this->db->query("SELECT SUM(amount_payment) AS amount_payment FROM tb_payment WHERE id_invoice = '$id_invoice'")->row_array();
                        $sisaHutang = $invoice['total_invoice'] - $payment['amount_payment'];

                        if ($renvi['type_renvis'] != 'tagih_mingguan') {
                            if ($daysInvJatem >= 0) {
                                $total_invoice += $sisaHutang;
                            }
                        } else {
                            if ($daysInvJatem <= 0) {
                                $total_invoice += $sisaHutang;
                            }
                        }
                    }
                }

                $is_new = 0;
                if ($renvi['type_renvis'] == 'jatem1') {
                    if ($renvi['is_new'] == 1) {
                        $is_new = 1;
                    }
                }

                // Jatem Days
                $date1jatem = new DateTime(date("Y-m-d"));
                $date2jatem = new DateTime($renvi['jatem']);
                $daysJatem  = $date2jatem->diff($date1jatem)->format('%a');
                $operanJatem = "";
                if ($date1jatem < $date2jatem) {
                    $operanJatem = "-";
                }
                $daysJatem = $operanJatem . $daysJatem;

                $renvisFilter = [
                    'id_contact' => $renvi['id_contact'],
                    'filter' => 'Cluster ' . $cluster . ', 0 & 7 Hari',
                    'nama' => $renvi['nama'],
                    'type_renvis' => $renvi['type_renvis'],
                    'last_visit' => $last_visit,
                    'days' => $days,
                    'daysJatem' => $daysJatem,
                    'total_invoice' => $total_invoice,
                    'is_new' => $is_new,
                ];

                if ($renvi['cluster'] == $cluster) {
                    if (count($jadwalVisits) <= 14) {
                        // if ($days == 0 || $days >= 7) {
                        if ($days > 3) {
                            if (array_search($renvi['id_contact'], array_column($jadwalVisits, 'id_contact')) == "") {
                                array_push($jadwalVisits, $renvisFilter);
                            }
                        }
                        // }

                        if ($renvi['hari_bayar'] == $dayName) {
                            if (array_search($renvi['id_contact'], array_column($jadwalVisits, 'id_contact')) == "") {
                                array_push($jadwalVisits, $renvisFilter);
                            }
                        }
                    }
                }
            }

            // Filter 3
            foreach ($renvis as $renvi) {
                $type_renvis = $renvi['type_renvis'];
                $id_contact = $renvi['id_contact'];
                $created_at = date('Y-m-d', strtotime($renvi['created_at']));

                $last_visit = '';
                $date_last_for_counter = '';
                if ($renvi['is_new'] == 1) {
                    if ($type_renvis == 'jatem1') {
                        // $date_last_for_counter = date('Y-m-d', strtotime($renvi['jatem']));
                        // $last_visit = $renvi['jatuh_tempo'];
                        $date_last_for_counter = date('Y-m-d', strtotime($renvi['created_at']));
                        $last_visit = date('d M Y', strtotime($renvi['created_at']));
                    } else if ($type_renvis == 'tagih_mingguan') {
                        $date_last_for_counter = date('Y-m-d', strtotime($renvi['created_at']));
                        $last_visit = date('d M Y', strtotime($renvi['created_at']));
                        // $date_last_for_counter = date('Y-m-d', strtotime($renvi['date_invoice']));
                        // $last_visit = date('d M Y', strtotime($renvi['date_invoice']));
                    } else {
                        $date_last_for_counter = date('Y-m-d', strtotime($renvi['created_at']));
                        $last_visit = date('d M Y', strtotime($renvi['created_at']));
                    }
                } else {
                    $date_last_for_counter = date('Y-m-d', strtotime($renvi['created_at']));
                    $last_visit = date('d M Y', strtotime($renvi['created_at']));
                }

                $date1 = new DateTime(date("Y-m-d"));
                $date2 = new DateTime($date_last_for_counter);
                $days  = $date2->diff($date1)->format('%a');
                $operan = "";
                if ($date1 < $date2) {
                    $operan = "-";
                }
                $days = $operan . $days;

                // Invoice
                $id_invoice = $renvi['id_invoice'];
                $invoices = $this->MInvoice->getByIdInvoiceWaiting($id_invoice);

                $total_invoice = 0;
                foreach ($invoices as $invoice) {
                    $id_invoice = $invoice['id_invoice'];
                    // Jatem Days
                    $jatemInv = date('Y-m-d', strtotime("+" . $invoice['termin_payment'] . " days", strtotime($invoice['date_invoice'])));
                    $dateInv1 = new DateTime(date("Y-m-d"));
                    $dateInv2 = new DateTime($jatemInv);
                    $daysInvJatem  = $dateInv2->diff($dateInv1)->format('%a');
                    $operanInvJatem = "";
                    if ($dateInv1 < $dateInv2) {
                        $operanInvJatem = "-";
                    }
                    $daysInvJatem = $operanInvJatem . $daysInvJatem;

                    $payment = $this->db->query("SELECT SUM(amount_payment) AS amount_payment FROM tb_payment WHERE id_invoice = '$id_invoice'")->row_array();
                    $amountPayment = $payment == null ? 0 : $payment['amount_payment'];
                    $sisaHutang = $invoice['total_invoice'] - $amountPayment;

                    if ($renvi['type_renvis'] != 'tagih_mingguan') {
                        if ($daysInvJatem >= 0) {
                            $total_invoice += $sisaHutang;
                        }
                    } else {
                        if ($daysInvJatem <= 0) {
                            $total_invoice += $sisaHutang;
                        } else {
                            $total_invoice += $sisaHutang;
                        }
                    }
                }

                if ($total_invoice == 0) {
                    $invoices = $this->MInvoice->getByIdContactWaiting($id_contact);
                    foreach ($invoices as $invoice) {
                        $id_invoice = $invoice['id_invoice'];
                        // Jatem Days
                        $jatemInv = date('Y-m-d', strtotime("+" . $invoice['termin_payment'] . " days", strtotime($invoice['date_invoice'])));
                        $dateInv1 = new DateTime(date("Y-m-d"));
                        $dateInv2 = new DateTime($jatemInv);
                        $daysInvJatem  = $dateInv2->diff($dateInv1)->format('%a');
                        $operanInvJatem = "";
                        if ($dateInv1 < $dateInv2) {
                            $operanInvJatem = "-";
                        }
                        $daysInvJatem = $operanInvJatem . $daysInvJatem;

                        $payment = $this->db->query("SELECT SUM(amount_payment) AS amount_payment FROM tb_payment WHERE id_invoice = '$id_invoice'")->row_array();
                        $sisaHutang = $invoice['total_invoice'] - $payment['amount_payment'];

                        if ($renvi['type_renvis'] != 'tagih_mingguan') {
                            if ($daysInvJatem >= 0) {
                                $total_invoice += $sisaHutang;
                            }
                        } else {
                            if ($daysInvJatem <= 0) {
                                $total_invoice += $sisaHutang;
                            }
                        }
                    }
                }

                $is_new = 0;
                if ($renvi['type_renvis'] == 'jatem1') {
                    if ($renvi['is_new'] == 1) {
                        $is_new = 1;
                    }
                }

                // Jatem Days
                $date1jatem = new DateTime(date("Y-m-d"));
                $date2jatem = new DateTime($renvi['jatem']);
                $daysJatem  = $date2jatem->diff($date1jatem)->format('%a');
                $operanJatem = "";
                if ($date1jatem < $date2jatem) {
                    $operanJatem = "-";
                }
                $daysJatem = $operanJatem . $daysJatem;

                $renvisFilter = [
                    'id_contact' => $renvi['id_contact'],
                    'filter' => 'Cluster Lain di hari bayar ' . $renvi['hari_bayar'] . ',  0 & 7 Hari',
                    'nama' => $renvi['nama'],
                    'type_renvis' => $renvi['type_renvis'],
                    'last_visit' => $last_visit,
                    'days' => $days,
                    'daysJatem' => $daysJatem,
                    'total_invoice' => $total_invoice,
                    'is_new' => $is_new,
                ];

                if (count($jadwalVisits) <= 14) {
                    if ($renvi['cluster'] != 1) {
                        if ($renvi['hari_bayar'] == $dayName) {
                            // if ($days == 0 || $days >= 7) {
                            // if ($days > 3) {
                            if (array_search($renvi['id_contact'], array_column($jadwalVisits, 'id_contact')) == "") {
                                array_push($jadwalVisits, $renvisFilter);
                            }
                            // }
                            // }
                        }
                    }
                }
            }
            // Filter 4
            // Filter 4 (Toko yang akan passive)
            $id_city = $city['id_city'];
            $contactActives = $this->db->get_where('tb_contact', ['id_city' => $id_city, 'cluster' => $cluster, 'store_status' => 'active'])->result_array();

            foreach ($contactActives as $contactActive) {
                $id_contact = $contactActive['id_contact'];
                $lastOrder = $this->db->query("SELECT MAX(date_closing) as date_closing, id_contact FROM tb_surat_jalan WHERE id_contact = '$id_contact' AND is_closing = 1 GROUP BY id_contact")->row_array();

                if ($lastOrder != null) {
                    $dateMin6Week = date('Y-m-d', strtotime("-6 week"));
                    $dateMin2Month = date("Y-m-d", strtotime("-2 month"));
                    $dateLastOrder = date("Y-m-d", strtotime($lastOrder['date_closing']));

                    if ($dateLastOrder <= $dateMin6Week && $dateLastOrder >= $dateMin2Month) {
                        if (count($jadwalVisits) <= 14) {
                            $rowLastVisit = $this->db->query("SELECT * FROM tb_visit WHERE id_contact = '$id_contact' AND source_visit IN ('voucher','passive','renvisales','mg','normal','jatem1','jatem2','jatem3') ORDER BY date_visit DESC LIMIT 1")->row_array();

                            $date_last_for_counter = date('Y-m-d', strtotime($rowLastVisit['date_visit']));
                            $last_visit = date('d M Y', strtotime($rowLastVisit['date_visit']));

                            $date1 = new DateTime(date("Y-m-d"));
                            $date2 = new DateTime($date_last_for_counter);
                            $days  = $date2->diff($date1)->format('%a');
                            $operan = "";
                            if ($date1 < $date2) {
                                $operan = "-";
                            }
                            $days = $operan . $days;

                            $renvisFilter = [
                                'id_contact' => $contactActive['id_contact'],
                                'filter' => 'Toko akan pasif dalam 2 minggu',
                                'nama' => $contactActive['nama'],
                                'type_renvis' => 'Akan passive',
                                'last_visit' => $last_visit,
                                'days' => $days,
                                'daysJatem' => '-',
                                'total_invoice' => 0,
                                'is_new' => 0,
                            ];

                            // if ($days == 0 || $days >= 7) {
                            if ($days > 3) {
                                if (array_search($id_contact, array_column($jadwalVisits, 'id_contact')) == "") {
                                    array_push($jadwalVisits, $renvisFilter);
                                }
                            }
                            // }
                        }
                    }
                }
            }

            // Filter 5 (Toko data / baru)
            $id_city = $city['id_city'];
            $contactDatas = $this->db->get_where('tb_contact', ['id_city' => $id_city, 'cluster' => $cluster, 'store_status' => 'data'])->result_array();

            foreach ($contactDatas as $contactData) {
                if (count($jadwalVisits) <= 14) {
                    $id_contact = $contactData['id_contact'];

                    $rowLastVisit = $this->db->query("SELECT * FROM tb_visit WHERE id_contact = '$id_contact' AND source_visit IN ('voucher','passive','renvisales','mg','normal') ORDER BY date_visit DESC LIMIT 1")->row_array();

                    $date_last_for_counter = date('Y-m-d', strtotime($rowLastVisit['date_visit']));
                    $last_visit = date('d M Y', strtotime($rowLastVisit['date_visit']));

                    $date1 = new DateTime(date("Y-m-d"));
                    $date2 = new DateTime($date_last_for_counter);
                    $days  = $date2->diff($date1)->format('%a');
                    $operan = "";
                    if ($date1 < $date2) {
                        $operan = "-";
                    }
                    $days = $operan . $days;

                    $renvisFilter = [
                        'id_contact' => $contactData['id_contact'],
                        'filter' => 'Toko Baru',
                        'nama' => $contactData['nama'],
                        'type_renvis' => 'Toko Baru',
                        'last_visit' => $last_visit,
                        'days' => $days,
                        'daysJatem' => '-',
                        'total_invoice' => 0,
                        'is_new' => 0,
                    ];

                    // if ($days == 0 || $days >= 7) {
                    if ($days > 3) {
                        if (array_search($id_contact, array_column($jadwalVisits, 'id_contact')) == "") {
                            array_push($jadwalVisits, $renvisFilter);
                        }
                    }
                    // }
                }
            }
            // Filter 6 (Toko passive)
            foreach ($renvisPassives as $renvisPassive) {
                $date_last_for_counter = date('Y-m-d', strtotime($renvisPassive['created_at']));
                $last_visit = date('d M Y', strtotime($renvisPassive['created_at']));

                $date1 = new DateTime(date("Y-m-d"));
                $date2 = new DateTime($date_last_for_counter);
                $days  = $date2->diff($date1)->format('%a');
                $operan = "";
                if ($date1 < $date2) {
                    $operan = "-";
                }
                $days = $operan . $days;

                $renvisFilter = [
                    'id_contact' => $renvisPassive['id_contact'],
                    'filter' => 'Passive',
                    'nama' => $renvisPassive['nama'],
                    'type_renvis' => $renvisPassive['type_renvis'],
                    'last_visit' => $last_visit,
                    'days' => $days,
                    'daysJatem' => '-',
                    'total_invoice' => 0,
                    'is_new' => 0,
                ];

                if ($renvisPassive['cluster'] == $cluster) {
                    if (count($jadwalVisits) <= 14) {
                        // if ($days == 0 || $days >= 7) {
                        if ($days > 3) {
                            if (array_search($renvisPassive['id_contact'], array_column($jadwalVisits, 'id_contact')) == "") {
                                array_push($jadwalVisits, $renvisFilter);
                            }
                        }
                        // }
                    }
                }
            }


            // Filter 7 (Toko Aktif)
            $id_city = $city['id_city'];
            $contactDatas = $this->db->get_where('tb_contact', ['id_city' => $id_city, 'cluster' => $cluster, 'store_status' => 'active'])->result_array();

            foreach ($contactDatas as $contactData) {
                if (count($jadwalVisits) <= 14) {
                    $id_contact = $contactData['id_contact'];

                    $rowLastVisit = $this->db->query("SELECT * FROM tb_visit WHERE id_contact = '$id_contact' AND source_visit IN ('voucher','passive','renvisales','mg','normal','jatem1','jatem2','jatem3','weekly') ORDER BY date_visit DESC LIMIT 1")->row_array();

                    $date_last_for_counter = date('Y-m-d', strtotime($rowLastVisit['date_visit']));
                    $last_visit = date('d M Y', strtotime($rowLastVisit['date_visit']));

                    $date1 = new DateTime(date("Y-m-d"));
                    $date2 = new DateTime($date_last_for_counter);
                    $days  = $date2->diff($date1)->format('%a');
                    $operan = "";
                    if ($date1 < $date2) {
                        $operan = "-";
                    }
                    $days = $operan . $days;

                    $renvisFilter = [
                        'id_contact' => $id_contact,
                        'filter' => 'Toko Aktif',
                        'nama' => $contactData['nama'],
                        'type_renvis' => 'Toko Aktif',
                        'last_visit' => $last_visit,
                        'days' => $days,
                        'daysJatem' => '-',
                        'total_invoice' => 0,
                        'is_new' => 0,
                    ];

                    // if ($days == 0 || $days >= 7) {
                    if ($days > 3) {
                        if (array_search($id_contact, array_column($jadwalVisits, 'id_contact')) == "") {
                            array_push($jadwalVisits, $renvisFilter);
                        }
                    }
                    // }
                }
            }

            // echo json_encode($jadwalVisits);

            foreach ($jadwalVisits as $jadwalVisit) {
                $jadwalVisitData = [
                    'id_city' => $city['id_city'],
                    'id_contact' => $jadwalVisit['id_contact'],
                    'cluster_jadwal_visit' => $cluster,
                    'date_jadwal_visit' => date('Y-m-d'),
                    'filter_jadwal_visit' => $jadwalVisit['filter'],
                    'kategori_jadwal_visit' => $jadwalVisit['type_renvis'],
                    'is_new' => $jadwalVisit['is_new'],
                    'last_visit' => $jadwalVisit['last_visit'],
                    'days_jadwal_visit' => $jadwalVisit['days'],
                ];

                $save = $this->db->insert('tb_jadwal_visit', $jadwalVisitData);

                if ($save) {
                    echo json_encode(['status' => 'ok']);
                } else {
                    echo json_encode(['status' => 'failed']);
                }
            }
        }
    }
}
