<?php

class Tagihan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // if ($this->session->userdata('id_user') == null) {
        //     redirect('login');
        // }
        $this->load->model('MTagihan');
        $this->load->model('MTagihandetail');
        $this->load->model('MDistributor');
        $this->load->model('MInvoice');
    }

    public function createTagihan()
    {
        $this->output->set_content_type('application/json');

        $distributors = $this->MDistributor->getAll();

        foreach ($distributors as $distributor) {
            $month = date('Y-m', strtotime("-1 months"));
            $id_distributor = $distributor['id_distributor'];
            $price_per_invoice = $distributor['price_per_invoice'];
            $price_abunemen = $distributor['price_abunemen'];

            $invoices = $this->MInvoice->getForTagihan($id_distributor, $month);

            $qtyInvoice = count($invoices);

            $subtotalInvoice = $qtyInvoice * $price_per_invoice;

            $tax = 0;

            $tagihanData = [
                'id_distributor' => $id_distributor,
                'date_tagihan' => date("Y-m-d H:i:s"),
            ];

            $save = $this->MTagihan->create($tagihanData);

            if (!$save) {
                $result = [
                    'code' => 400,
                    'status' => 'failed',
                    'msg' => 'Failed',
                    'detail' => $this->db->error(),
                ];

                $this->output->set_output(json_encode($result));
            } else {
                $id_tagihan = $this->db->insert_id();

                // Tagihan Invoice
                $tagihanDetailData = [
                    'id_tagihan' => $id_tagihan,
                    'type_tagihan_detail' => 'Invoice',
                    'price_tagihan_detail' => $price_per_invoice,
                    'qty_tagihan_detail' => $qtyInvoice,
                    'total_tagihan_detail' => $subtotalInvoice,
                ];

                $this->MTagihandetail->create($tagihanDetailData);

                // Tagihan Abunemen
                $tagihanDetailData = [
                    'id_tagihan' => $id_tagihan,
                    'type_tagihan_detail' => 'Abunemen',
                    'price_tagihan_detail' => $price_abunemen,
                    'qty_tagihan_detail' => 1,
                    'total_tagihan_detail' => $price_abunemen * 1,
                ];

                $this->MTagihandetail->create($tagihanDetailData);

                $no_tagihan = 'INV/MAS/TSLS/' . date("m/Y") . '/' . str_pad($id_tagihan, 4, '0', STR_PAD_LEFT);
                $subtotal_tagihan = $subtotalInvoice + $price_abunemen;

                $tagihanData = [
                    'no_tagihan' => $no_tagihan,
                    'subtotal_tagihan' => $subtotal_tagihan,
                    'tax' => $tax,
                    'total_tagihan' => $tax + $subtotal_tagihan,
                ];

                $save = $this->MTagihan->udpate($id_tagihan, $tagihanData);

                if (!$save) {
                    $result = [
                        'code' => 400,
                        'status' => 'failed',
                        'msg' => 'Failed',
                        'detail' => $this->db->error(),
                    ];

                    $this->output->set_output(json_encode($result));
                } else {
                    $result = [
                        'code' => 200,
                        'status' => 'ok',
                        'msg' => 'Suceess'
                    ];

                    $this->output->set_output(json_encode($result));
                }
            }
        }
    }
}
