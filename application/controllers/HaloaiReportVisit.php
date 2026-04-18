<?php

class HaloaiReportVisit extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->output->set_content_type('application/json');
        $this->load->model('MContact');
        $this->load->model('MUser');
        $this->load->model('MSuratJalan');
        $this->load->model('MDetailSuratJalan');
        $this->load->model('MInvoice');
        $this->load->model('MPayment');
        $this->load->model('MVoucher');
        $this->load->model('HTelegram');
    }

    public function process()
    {
        $post = json_decode(file_get_contents('php://input'), true) != null ? json_decode(file_get_contents('php://input'), true) : $this->input->post();

        $nomorhp = $post['nomorhp'];

        // Contact
        $contact = $this->MContact->getByNomorhp($nomorhp);
        $id_contact = $contact['id_contact'];

        // Last Visit
        $lastVisit = $this->db->get_where('tb_visit', ['id_contact' => $id_contact, 'DATE(date_visit)' => date('Y-m-d')])->row_array();

        $id_visit = $lastVisit['id_visit'];

        $approveVisit = $this->db->update('tb_visit', ['is_approved' => 1], ['id_visit' => $id_visit]);

        if ($contact['store_status'] == 'active') {
            // 
            $id_contact = $contact['id_contact'];
            $id_distributor = $contact['id_distributor'];
            $id_city = $contact['id_city'];

            // Checklist Visit
            $checklistVisit = $this->db->get_where('tb_visit_answer', ['id_visit' => $lastVisit['id_visit']])->result_array();

            $id_visit = $lastVisit['id_visit'];

            // Check Report First
            // $checkReportVisit = $this->db->get_where('tb_ai_report_visit', ['id_visit' => $id_visit])->row_array();

            // Voucher
            $vouchers = $this->MVoucher->getByIdContactForHaloAI($id_contact);

            $vouchersStr = "";
            $voucherExp = "";
            foreach ($vouchers as $voucher) {
                $vouchersStr .= $voucher['no_voucher'] . ",";
                // if (!empty($voucherExp)) {
                $voucherExp = date('d F Y', strtotime($voucher['exp_date']));
                // }
                // $voucherExp = date('d F Y', strtotime($voucher['exp_date']));
            }

            $jmlVoucher = count($vouchers) . "";

            $storeData = [
                'toko' => [
                    'nama' => $contact['nama'],
                    'pemilik' => $contact['store_owner'],
                    'tanggal_terakhir_dikunjungi' => date("Y-m-d", strtotime($lastVisit['date_visit'])),
                    'status' => $contact['store_status'],
                    'jml_voucher' => $jmlVoucher,
                    'voucher_expired' => $voucherExp,
                    'visit_checklist' => $checklistVisit,
                ],
                'tanggal_hari_ini' => date('Y-m-d'),
                'laporan_sales' => $lastVisit['laporan_visit'],
            ];

            $result = [
                'code' => 200,
                'status' => 'ok',
                'msg' => 'Store need to analyze',
                'data' => $storeData,
            ];
        } else {
            $result = [
                'code' => 400,
                'status' => 'failed',
                'msg' => 'Store is active, no need to analyze',
            ];
        }


        return $this->output->set_output(json_encode($result));
    }
}
