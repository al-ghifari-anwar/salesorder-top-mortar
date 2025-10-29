<?php

class Confirmqrsak extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $this->load->model('MQrsak');
        $this->load->model('MQrsakDetail');
        $this->load->model('MUser');
    }

    public function index()
    {
        $data['title'] = 'Scan QR Uang Tunai';
        $data['menuGroup'] = 'Marketing';
        $data['menu'] = 'Confirmqrsak';

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Confirmqrsak/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function scan()
    {
        $post = $this->input->post();

        if (empty($post['code_qrsak_detail'])) {
            $this->session->set_flashdata('failed', "QR wajib di scan!");
            return redirect('confirmqrsak');
        }

        $qrsak_detail = $this->MQrsakDetail->getByCode($post['code_qrsak_detail']);

        if (!$qrsak_detail) {
            $this->session->set_flashdata('failed', "QR tidak valid!");
            return redirect('confirmqrsak');
        } else {
            if ($qrsak_detail['is_confirm'] == 1) {
                $this->session->set_flashdata('failed', "QR sudah confirm!");
                return redirect('confirmqrsak');
            } else {
                $qrsakDetailData = [
                    'is_confirm' => 1,
                    'confirm_date' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                $save = $this->MQrsakDetail->update($qrsak_detail['id_qrsak_detail'], $qrsakDetailData);

                if (!$save) {
                    $this->session->set_flashdata('failed', "Terjadi kesalahan harap scan ulang!");
                    return redirect('confirmqrsak');
                } else {
                    $this->session->set_flashdata('success', "Berhasil scan!");
                    return redirect('confirmqrsak');
                }
            }
        }
    }
}
