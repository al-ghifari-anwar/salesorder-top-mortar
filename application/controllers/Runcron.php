<?php

class Runcron extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MDistributor');
        $this->load->model('MInvoice');
        $this->load->model('MCompany');
        $this->load->model('MPayment');
    }

    public function stmt()
    {
        $this->output->set_content_type('application/json');

        $distributors = $this->MDistributor->getAll();

        foreach ($distributors as $distributor) {
            if ($distributor['api_key'] != '0') {
                $id_distributor = $distributor['id_distributor'];
                $api_key = $distributor['api_key'];
                $source_account = $distributor['source_account'];

                $company = $this->MCompany->getByIdDistributor($id_distributor);
                $name_company = $company['name_company'];
                $norek_company = $company['norek_company'];

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://central.topmortarindonesia.com/stmt',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                        'x-api-key: ' . $api_key,
                        'x-timestamp: ' . date("Y-m-d H:i:s")
                    ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);

                $res = json_decode($response, true);

                if ($res['code'] != 200) {
                    $result = [
                        'code' => 400,
                        'status' => 'failed',
                        'msg' => 'Failed to get stmt',
                    ];

                    return $this->output->set_output(json_encode($result));
                }

                $resData = $res['data'];
                $stmtDatas = $resData['detailData'];

                foreach ($stmtDatas as $stmtData) {
                    if ($stmtData['type'] == 'CREDIT') {
                        $amountData = $stmtData['amount'];
                        $amountValue = $amountData['value'];
                        $transactionDate = date("Y-m-d H:i:s", strtotime($stmtData['transactionDate']));
                        $remark = str_replace("'", " ", $stmtData['remark']) . " - Date: " . $stmtData['transactionDate'];

                        $checkInv = $this->MInvoice->getForStmt($amountValue, $id_distributor);

                        if ($checkInv) {
                            $id_invoice = $checkInv['id_invoice'];
                            $id_contact = $checkInv['id_contact'];

                            $checkPayment = $this->MPayment->getByRemark($remark);

                            if ($checkPayment) {
                                // $result = [
                                //     'code' => 400,
                                //     'status' => 'failed',
                                //     'msg' => 'Payment already saved',
                                // ];

                                // return $this->output->set_output(json_encode($result));
                            } else {
                                $invoiceData = [
                                    'status_invoice' => 'paid',
                                ];

                                $updateStatusInv = $this->db->update('tb_invoice', $invoiceData, ['id_invoice' => $id_invoice]);

                                if (!$updateStatusInv) {
                                    // $result = [
                                    //     'code' => 400,
                                    //     'status' => 'failed',
                                    //     'msg' => 'Failed to update invoice status',
                                    //     'detail' => $this->db->error()
                                    // ];

                                    // return $this->output->set_output(json_encode($result));
                                } else {
                                    $renviData = [
                                        'is_visited' => 1,
                                        'visit_date' => date('Y-m-d H:i:s'),
                                    ];

                                    $removeRenvi = $this->db->update('tb_rencana_visit', $renviData, ['id_contact' => $id_contact, 'type_renvi' => 'jatem']);

                                    $removeRenviPenagihan = $this->db->update('tb_renvis_jatem', $renviData, ['id_invoice' => $id_invoice]);

                                    $removeRenviMingguan = $this->db->update('tb_rencana_visit', $renviData, ['id_invoice' => $id_invoice, 'type_renvi' => 'tagih_mingguan']);

                                    $paymentData = [
                                        'amount' => $amountValue,
                                        'transactionDate' => date("Y-m-d H:i:s"),
                                        'remark' => $remark,
                                        'id_invoice' => $id_invoice,
                                        'source' => $source_account,
                                    ];

                                    $savePayment = $this->db->insert('tb_payment', $paymentData);

                                    if (!$savePayment) {
                                        // $result = [
                                        //     'code' => 400,
                                        //     'status' => 'failed',
                                        //     'msg' => 'Status set, but payment not saved',
                                        //     'detail' => $this->db->error()
                                        // ];

                                        // return $this->output->set_output(json_encode($result));
                                    } else {
                                        if ($amountValue != 30000) {
                                            // !! Send 
                                            $curl = curl_init();

                                            curl_setopt_array($curl, array(
                                                CURLOPT_URL => 'https://central.topmortarindonesia.com/intra',
                                                CURLOPT_RETURNTRANSFER => true,
                                                CURLOPT_ENCODING => '',
                                                CURLOPT_MAXREDIRS => 10,
                                                CURLOPT_TIMEOUT => 0,
                                                CURLOPT_FOLLOWLOCATION => true,
                                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                                CURLOPT_CUSTOMREQUEST => 'POST',
                                                CURLOPT_POSTFIELDS => array('amount' => $amountValue, 'toName' => $name_company, 'toAccount' => $norek_company, 'remark' => 'Auto Tf Inv' . substr($name_company, 0, 9)),
                                                CURLOPT_HTTPHEADER => array(
                                                    'x-api-key: ' . $api_key,
                                                    'x-timestamp: ' . date("Y-m-d H:i:s")
                                                ),
                                            ));

                                            $response = curl_exec($curl);

                                            curl_close($curl);

                                            $res = json_decode($response, true);

                                            $resData = $res['data'];

                                            if ($resData['code'] != 200) {
                                                // $result = [
                                                //     'code' => 400,
                                                //     'status' => 'failed',
                                                //     'msg' => 'All saved, but payment not transfered',
                                                // ];

                                                // return $this->output->set_output(json_encode($result));
                                            } else {

                                                $statusIntra = $resData['responseMessage'] == 'Successful' ? 'success' : 'failed';

                                                $logData = [
                                                    'source_account' => $source_account,
                                                    'to_account' => $norek_company,
                                                    'amount_log_bca' => $amountValue,
                                                    'status_log_bca' => $statusIntra,
                                                    'ref_log_bca' => $resData['partnerReferenceNo'],
                                                ];

                                                $saveLog = $this->db->insert('tb_log_bca', $logData);

                                                if (!$saveLog) {
                                                    // $result = [
                                                    //     'code' => 400,
                                                    //     'status' => 'failed',
                                                    //     'msg' => 'All saved, payment transfered, log not saved',
                                                    // ];

                                                    // return $this->output->set_output(json_encode($result));
                                                } else {
                                                    // $result = [
                                                    //     'code' => 200,
                                                    //     'status' => 'ok',
                                                    //     'msg' => 'Aman',
                                                    // ];

                                                    // return $this->output->set_output(json_encode($result));
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            $id_invoice = 0;

                            $checkPayment = $this->MPayment->getByRemark($remark);

                            if ($checkPayment) {
                                // $result = [
                                //     'code' => 400,
                                //     'status' => 'failed',
                                //     'msg' => 'Payment already saved',
                                // ];

                                // return $this->output->set_output(json_encode($result));
                            } else {
                                $paymentData = [
                                    'amount' => $amountValue,
                                    'transactionDate' => date("Y-m-d H:i:s"),
                                    'remark' => $remark,
                                    'id_invoice' => $id_invoice,
                                    'source' => $source_account,
                                ];

                                $savePayment = $this->db->insert('tb_payment', $paymentData);

                                if (!$savePayment) {
                                    // $result = [
                                    //     'code' => 400,
                                    //     'status' => 'failed',
                                    //     'msg' => 'Status set, but payment not saved',
                                    //     'detail' => $this->db->error()
                                    // ];

                                    // return $this->output->set_output(json_encode($result));
                                } else {
                                    if ($amountValue != 30000) {
                                        // !! Send 
                                        $curl = curl_init();

                                        curl_setopt_array($curl, array(
                                            CURLOPT_URL => 'https://central.topmortarindonesia.com/intra',
                                            CURLOPT_RETURNTRANSFER => true,
                                            CURLOPT_ENCODING => '',
                                            CURLOPT_MAXREDIRS => 10,
                                            CURLOPT_TIMEOUT => 0,
                                            CURLOPT_FOLLOWLOCATION => true,
                                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                            CURLOPT_CUSTOMREQUEST => 'POST',
                                            CURLOPT_POSTFIELDS => array('amount' => $amountValue, 'toName' => $name_company, 'toAccount' => $norek_company, 'remark' => 'Auto Tf Inv' . substr($name_company, 0, 9)),
                                            CURLOPT_HTTPHEADER => array(
                                                'x-api-key: ' . $api_key,
                                                'x-timestamp: ' . date("Y-m-d H:i:s")
                                            ),
                                        ));

                                        $response = curl_exec($curl);

                                        curl_close($curl);

                                        $res = json_decode($response, true);

                                        $resData = $res['data'];

                                        if ($resData['code'] != 200) {
                                            // $result = [
                                            //     'code' => 400,
                                            //     'status' => 'failed',
                                            //     'msg' => 'All saved, but payment not transfered',
                                            // ];

                                            // return $this->output->set_output(json_encode($result));
                                        } else {

                                            $statusIntra = $resData['responseMessage'] == 'Successful' ? 'success' : 'failed';

                                            $logData = [
                                                'source_account' => $source_account,
                                                'to_account' => $norek_company,
                                                'amount_log_bca' => $amountValue,
                                                'status_log_bca' => $statusIntra,
                                                'ref_log_bca' => $resData['partnerReferenceNo'],
                                            ];

                                            $saveLog = $this->db->insert('tb_log_bca', $logData);

                                            if (!$saveLog) {
                                                // $result = [
                                                //     'code' => 400,
                                                //     'status' => 'failed',
                                                //     'msg' => 'All saved, payment transfered, log not saved',
                                                // ];

                                                // return $this->output->set_output(json_encode($result));
                                            } else {
                                                // $result = [
                                                //     'code' => 200,
                                                //     'status' => 'ok',
                                                //     'msg' => 'Aman',
                                                // ];

                                                // return $this->output->set_output(json_encode($result));
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $result = [
            'code' => 200,
            'status' => 'ok',
            'msg' => 'Aman',
        ];

        return $this->output->set_output(json_encode($result));
    }
}
