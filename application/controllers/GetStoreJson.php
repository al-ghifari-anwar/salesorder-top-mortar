<?php


class GetStoreJson extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MInvoice');
        $this->load->model('MVoucher');
        $this->load->model('MPayment');
    }

    public function index()
    {
        $nomorhp = $_GET['nomorhp'];

        $contact = $this->db->select('id_contact, nama, nomorhp, tgl_lahir, store_owner, address, store_status, id_city, id_promo, termin_payment, kredit_limit, hobi_contact AS hobi')->where('nomorhp', $nomorhp)->or_where('nomorhp_2', $nomorhp)->get('tb_contact')->row_array();

        $contact['termin_payment'] = $contact['termin_payment'] > 0 ? "Tempo " . $contact['termin_payment'] . " hari" : "COD";

        $id_contact = $contact['id_contact'];

        $id_promo = $contact['id_promo'];

        $promo = $this->db->get_where('tb_promo', ['id_promo' => $id_promo])->row_array();

        $kelipatan_promo_toko = $promo ? $promo['kelipatan_promo'] : 0;

        $otherPromo = $this->db->select('nama_promo')->get_where('tb_promo', ['kelipatan_promo <' => $kelipatan_promo_toko, 'is_potongan' => 1])->result_array();

        // Score toko
        $paymentScore = $this->paymentScoring($contact);

        $contact['payment_scoring'] = $paymentScore;

        // Voucher
        $vouchers = $this->MVoucher->getByIdContactForHaloAI($id_contact);

        $contact['jml_voucher'] = count($vouchers) . "";

        $vouchersStr = "";
        $voucherExp = "";
        foreach ($vouchers as $voucher) {
            $vouchersStr .= $voucher['no_voucher'] . ",";
            // if (!empty($voucherExp)) {
            $voucherExp = date('d F Y', strtotime($voucher['exp_date']));
            // }
        }

        $contact['voucher_expired'] = $voucherExp;

        $city = $this->db->select('nama_city, id_distributor')->where('id_city', $contact['id_city'])->get('tb_city')->row_array();

        $contact['kota'] = $city['nama_city'];

        $contact['promo_global'] = $promo ? $promo['nama_promo'] : null;

        $contact['promo_lain'] = $otherPromo;

        $invoices = $this->db->join('tb_surat_jalan', 'tb_surat_jalan.id_surat_jalan = tb_invoice.id_surat_jalan')->order_by('tb_invoice.date_invoice', 'DESC')->get_where('tb_invoice', ['id_contact' => $id_contact])->result_array();

        $contact['invoice'] = $invoices;

        $result = [
            'code' => 200,
            'status' => 'ok',
            'msg' => 'Success',
            'data' => $contact,
        ];

        return $this->output->set_output(json_encode($result));
    }

    public function paymentScoring($selected_contact)
    {
        // Payment Scoring
        $count_late_payment = 0;
        $invoices = $this->MInvoice->getByIdContactNoMerch($selected_contact['id_contact']);
        $payments = null;
        $array_scoring = array();
        foreach ($invoices as $invoice) {
            $id_surat_jalan = $invoice['id_surat_jalan'];
            $payments = $this->MPayment->getLastByIdInvoiceOnly($invoice['id_invoice']);

            $sj = $this->db->get_where('tb_surat_jalan', ['id_surat_jalan' => $id_surat_jalan])->row_array();

            if ($sj['is_cod'] == 0) {
                $jatuhTempo = date('Y-m-d', strtotime("+" . $selected_contact['termin_payment'] . " days", strtotime($invoice['date_invoice'])));
            } else {
                $jatuhTempo = date('Y-m-d', strtotime("+3 days", strtotime($invoice['date_invoice'])));
            }

            if ($payments) {

                foreach ($payments as $payment) {
                    $datePayment = date("Y-m-d", strtotime($payment['date_payment']));
                    if ($datePayment > $jatuhTempo) {
                        $count_late_payment += 1;
                        $date1 = new DateTime($datePayment);
                        if ($invoice['status_invoice'] == 'waiting') {
                            $date1 = new DateTime(date('Y-m-d'));
                        }
                        $date2 = new DateTime($jatuhTempo);
                        $days  = $date2->diff($date1)->format('%a');


                        $scoreData = [
                            'id_invoice' => $invoice['id_invoice'],
                            'no_invoice' => $invoice['no_invoice'],
                            'status' => 'late',
                            'days_late' => $days,
                            'date_jatem' => $jatuhTempo,
                            'date_payment' => $datePayment,
                            'percent_score' => 100 - $days,
                            'is_cod' => $sj['is_cod'],
                            'date_invoice' => $invoice['date_invoice'],
                        ];

                        array_push($array_scoring, $scoreData);
                    } else {
                        if ($invoice['status_invoice'] == 'paid') {
                            $scoreData = [
                                'id_invoice' => $invoice['id_invoice'],
                                'no_invoice' => $invoice['no_invoice'],
                                'status' => 'good',
                                'days_late' => 0,
                                'date_jatem' => $jatuhTempo,
                                'date_payment' => $datePayment,
                                'percent_score' => 100,
                                'is_cod' => $sj['is_cod'],
                                'date_invoice' => $invoice['date_invoice'],
                            ];

                            array_push($array_scoring, $scoreData);
                        } else {
                            $dateNow = date("Y-m-d");
                            if ($dateNow > $jatuhTempo) {
                                $count_late_payment += 1;
                                $date1 = new DateTime($dateNow);
                                $date2 = new DateTime($jatuhTempo);
                                $days  = $date2->diff($date1)->format('%a');

                                $scoreData = [
                                    'id_invoice' => $invoice['id_invoice'],
                                    'no_invoice' => $invoice['no_invoice'],
                                    'status' => 'late',
                                    'days_late' => $days,
                                    'date_jatem' => $jatuhTempo,
                                    'date_payment' => $datePayment,
                                    'percent_score' => 100 - $days,
                                    'is_cod' => $sj['is_cod'],
                                    'date_invoice' => $invoice['date_invoice'],
                                ];

                                array_push($array_scoring, $scoreData);
                            } else {
                                $scoreData = [
                                    'id_invoice' => $invoice['id_invoice'],
                                    'no_invoice' => $invoice['no_invoice'],
                                    'status' => 'good',
                                    'days_late' => 0,
                                    'date_jatem' => $jatuhTempo,
                                    'date_payment' => $datePayment,
                                    'percent_score' => 100,
                                    'is_cod' => $sj['is_cod'],
                                    'date_invoice' => $invoice['date_invoice'],
                                ];

                                array_push($array_scoring, $scoreData);
                            }
                        }
                    }
                }
            } else {
                if ($invoice['status_invoice'] == 'paid') {
                    $dateNow = date("Y-m-d");
                    $scoreData = [
                        'id_invoice' => $invoice['id_invoice'],
                        'no_invoice' => $invoice['no_invoice'],
                        'status' => 'good',
                        'days_late' => 0,
                        'date_jatem' => $jatuhTempo,
                        'date_payment' => $dateNow,
                        'percent_score' => 100,
                        'is_cod' => $sj['is_cod'],
                        'date_invoice' => $invoice['date_invoice'],
                    ];

                    array_push($array_scoring, $scoreData);
                } else {
                    $dateNow = date("Y-m-d");
                    if ($dateNow > $jatuhTempo) {
                        $count_late_payment += 1;
                        $date1 = new DateTime($dateNow);
                        $date2 = new DateTime($jatuhTempo);
                        $days  = $date2->diff($date1)->format('%a');

                        $scoreData = [
                            'id_invoice' => $invoice['id_invoice'],
                            'no_invoice' => $invoice['no_invoice'],
                            'status' => 'late',
                            'days_late' => $days,
                            'date_jatem' => $jatuhTempo,
                            'date_payment' => $dateNow,
                            'percent_score' => 100 - $days,
                            'is_cod' => $sj['is_cod'],
                            'date_invoice' => $invoice['date_invoice'],
                        ];

                        array_push($array_scoring, $scoreData);
                    } else {
                        $scoreData = [
                            'id_invoice' => $invoice['id_invoice'],
                            'no_invoice' => $invoice['no_invoice'],
                            'status' => 'good',
                            'days_late' => 0,
                            'date_jatem' => $jatuhTempo,
                            'date_payment' => $dateNow,
                            'percent_score' => 100,
                            'is_cod' => $sj['is_cod'],
                            'date_invoice' => $invoice['date_invoice'],
                        ];

                        array_push($array_scoring, $scoreData);
                    }
                }
            }
        }

        $count_invoice = count($array_scoring);
        if ($count_invoice == 0) {
            $count_invoice = 1;
        }
        $total_score = 0;
        foreach ($array_scoring as $scoring) {
            $total_score += $scoring['percent_score'];
        }

        $val_scoring = number_format($total_score / $count_invoice, 2, '.', '.');

        if ($val_scoring > 100) {
            $val_scoring = 100;
        } else if ($val_scoring <= 100 && $val_scoring > 0) {
            $val_scoring = $val_scoring;
        } else if ($val_scoring < 0) {
            $val_scoring = 0;
        }

        if ($selected_contact['store_status'] == 'data') {
            $val_scoring = 100;
        }

        return number_format($val_scoring, 2, '.', ',');
    }
}
