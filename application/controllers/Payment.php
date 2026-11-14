<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Payment extends CI_Controller
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
        $this->load->model('MPayment');
        $this->load->library('form_validation');
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
    }

    public function index()
    {
        $data['title'] = 'Rincian Pembayaran';
        $data['menuGroup'] = 'Pembayaran';
        $data['menu'] = 'RincianPembayaran';
        $data['city'] = $this->MCity->getAll();
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['toko'] = $this->MContact->getAll($this->session->userdata('id_city'));
        } else {
            $data['toko'] = $this->MContact->getAllDefault();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Payment/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function unmatch()
    {
        $data['title'] = 'Pembayaran Transit';
        $data['menuGroup'] = 'Pembayaran';
        $data['menu'] = 'Transit';
        $data['payment'] = $this->MPayment->getUnmatch();
        $data['invoice'] = $this->MInvoice->getUnpaid();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Payment/Unmatch');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function insertTransit()
    {
        $post = $this->input->post();

        $paymentData = [
            'amount_payment' => $post['amount_payment'],
            'date_payment' => date('Y-m-d H:i:s', strtotime($post['date_payment'])),
            'remark_payment' => $post['remark_payment'],
            'id_invoice' => 0,
            'source' => $post['source'],
        ];

        $save = $this->db->insert('tb_payment', $paymentData);

        if ($save) {
            $this->session->set_flashdata('success', "Success menambah transit!");
            redirect('payment-transit');
        } else {
            $this->session->set_flashdata('failed', "Failed!");
            redirect('payment-transit');
        }
    }

    public function all()
    {
        $data['title'] = 'Semua Pembayaran';
        $data['menuGroup'] = 'Pembayaran';
        $data['menu'] = 'Transit';
        $data['payment'] = $this->MPayment->getAll();
        $data['invoice'] = $this->MInvoice->getUnpaid();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Payment/All');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function unassign($id)
    {
        $unassign = $this->MPayment->unassign($id);

        if ($unassign) {
            $this->session->set_flashdata('success', "Success unassign payment!");
            redirect('all-payment');
        } else {
            $this->session->set_flashdata('failed', "Failed unassign payment!");
            redirect('all-payment');
        }
    }

    public function update($id)
    {
        $update = $this->MPayment->setPaymentInv($id);

        if ($update) {
            $this->session->set_flashdata('success', "Success!");
            redirect('payment-transit');
        } else {
            $this->session->set_flashdata('failed', "Failed!");
            redirect('payment-transit');
        }
    }


    public function remove($id)
    {
        $update = $this->MPayment->remove($id);

        if ($update) {
            $this->session->set_flashdata('success', "Success delete payment!");
            redirect('payment-transit');
        } else {
            $this->session->set_flashdata('failed', "Failed delete payment!");
            redirect('payment-transit');
        }
    }

    public function print()
    {
        $dateRange = $this->input->post("date_range");
        $id_contact = $this->input->post("id_contact");
        $id_city = $this->input->post('id_city');
        if ($dateRange) {
            $dates = explode("-", $dateRange);
            $invoice = $this->MInvoice->getAllByDate(date('Y-m-d H:i:s', strtotime($dates[0] . " 00:00:00")), date('Y-m-d H:i:s', strtotime($dates[1] . " 23:59:59")), $id_contact, $id_city);
        } else {
            // $invoice = $this->MInvoice->getAll();
        }

        // echo json_encode($invoice);
        $data['invoice'] = $invoice;
        $data['dateFrom'] = date("Y-m-d H:i:s", strtotime($dates[0] . " 00:00:00"));
        $data['dateTo'] = date("Y-m-d H:i:s", strtotime($dates[1] . " 23:59:59"));
        // PDF
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Payment/Print', $data, true);
        $mpdf->AddPage('P');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    public function import()
    {
        if (empty($_FILES['csv_mutasi']['tmp_name'])) {
            show_error('File CSV wajib diupload.');
            return;
        }

        $bank = $this->input->post('bank');

        if ($bank == 'BCA') {
            $file = fopen($_FILES['csv_mutasi']['tmp_name'], 'r');

            if ($file === false) {
                show_error('Gagal membaca file CSV.');
                return;
            }

            $rowNumber = 0;

            while (($row = fgetcsv($file, 0, ',')) !== false) {
                $rowNumber++;

                // Skip informasi rekening dan header
                if ($rowNumber <= 5) {
                    continue;
                }

                // Pastikan kolom tersedia
                if (count($row) < 6) {
                    continue;
                }

                // Ambil data CSV
                $remark = trim($row[1]);
                $amount = (float) str_replace(',', '', trim($row[3]));
                $status = strtoupper(trim($row[4]));

                // Hanya transaksi uang masuk (CR)
                if ($status !== 'CR') {
                    continue;
                }

                // ============================
                // LOGIC SIMPAN DATABASE DI SINI
                // ============================
                // $remark
                // $jumlah
                // $status
                $distributor = $this->db->get_where('tb_distributor', ['id_distributor' => $this->session->userdata('id_distributor')]);

                $checkInv = $this->MInvoice->getForStmt($amount, $this->session->userdata('id_distributor'));

                if ($checkInv) {
                    $id_invoice = $checkInv['id_invoice'];
                    $id_contact = $checkInv['id_contact'];

                    $checkPayment = $this->MPayment->getByRemarkAndAmount($remark, $amount);

                    if (!$checkPayment) {
                        $invoiceData = [
                            'status_invoice' => 'paid',
                        ];

                        $updateStatusInv = $this->db->update('tb_invoice', $invoiceData, ['id_invoice' => $id_invoice]);

                        if ($updateStatusInv) {
                            $paymentData = [
                                'amount_payment' => $amount,
                                'date_payment' => date("Y-m-d H:i:s"),
                                'remark_payment' => $remark,
                                'id_invoice' => $id_invoice,
                                'source' => $distributor['source_account'],
                            ];

                            $savePayment = $this->db->insert('tb_payment', $paymentData);

                            $id_payment = $this->db->insert_id();

                            continue;
                        }
                    }
                } else {
                    $id_invoice = 0;

                    $checkPayment = $this->MPayment->getByRemarkAndAmount($remark, $amount);

                    if (!$checkPayment) {
                        $paymentData = [
                            'amount_payment' => $amount,
                            'date_payment' => date("Y-m-d H:i:s"),
                            'remark_payment' => $remark,
                            'id_invoice' => $id_invoice,
                            'source' => $distributor['source_account'],
                        ];

                        $savePayment = $this->db->insert('tb_payment', $paymentData);

                        $id_payment = $this->db->insert_id();

                        continue;
                    }
                }
            }

            fclose($file);
        }

        redirect('payment-print');
    }
}
