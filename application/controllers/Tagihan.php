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

    public function index()
    {
        $id_distributor = $this->session->userdata('id_distributor');

        $data['title'] = 'Tagihan';
        $data['menuGroup'] = '';
        $data['menu'] = '';
        $data['tagihans'] = $this->MTagihan->getByIdDistributor($id_distributor);

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Tagihan/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function detail($id_tagihan)
    {
        $tagihan = $this->MTagihan->getById($id_tagihan);
        $tagihanDetails = $this->MTagihandetail->getByIdTagihan($id_tagihan);
        $distributor = $this->MDistributor->getById($tagihan['id_distributor']);
        $id_distributor = $distributor['id_distributor'];
        $month = date('Y-m', strtotime("-1 months", strtotime($tagihan['date_tagihan'])));
        $invoices = $this->MInvoice->getForTagihan($id_distributor, $month);



        $data['title'] = 'Detail_Tagihan_#' . $tagihan['no_tagihan'];
        $data['menuGroup'] = '';
        $data['menu'] = '';
        $data['tagihan'] = $tagihan;
        $data['tagihanDetails'] = $tagihanDetails;
        $data['distributor'] = $distributor;
        $data['invoices'] = $invoices;

        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Tagihan/PrintDetail', $data, true);
        $mpdf->AddPage('P');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    public function print($id_tagihan)
    {
        $tagihan = $this->MTagihan->getById($id_tagihan);
        $tagihanDetails = $this->MTagihandetail->getByIdTagihan($id_tagihan);
        $distributor = $this->MDistributor->getById($tagihan['id_distributor']);

        $data['title'] = 'Invoice_Tagihan_#' . $tagihan['no_tagihan'];
        $data['tagihan'] = $tagihan;
        $data['tagihanDetails'] = $tagihanDetails;
        $data['distributor'] = $distributor;

        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Tagihan/Print', $data, true);
        $mpdf->AddPage('P');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
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

            if ($qtyInvoice > 0) {
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

                    return $this->output->set_output(json_encode($result));
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
                        'tax_tagihan' => $tax,
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

                        return $this->output->set_output(json_encode($result));
                    } else {
                        $result = [
                            'code' => 200,
                            'status' => 'ok',
                            'msg' => 'Suceess'
                        ];

                        return $this->output->set_output(json_encode($result));
                    }
                }
            }
        }
    }
}
