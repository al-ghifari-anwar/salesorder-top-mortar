<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Marketing extends CI_Controller
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
        $this->load->model('MVoucher');
        $this->load->model("MMarketingMessage");
        $this->load->library('form_validation');
    }

    public function index()
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Marketing';
        $data['templates'] = $this->getMarketingTemplate();
        $data['marketing'] = $this->MMarketingMessage->getAll();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Marketing/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function insert()
    {
        $this->form_validation->set_rules('nama_marketing_message', 'Nama Konten', 'required');
        $this->form_validation->set_rules('week_marketing_message', 'Minggu Ke', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap isi form dengan lengkap");
            redirect('marketing');
        } else {
            $insert = $this->MMarketingMessage->insert();

            if ($insert) {
                $this->session->set_flashdata('success', "Berhasil membuat konten");
                redirect('marketing');
            } else {
                $this->session->set_flashdata('failed', "Gagal membuat konten");
                redirect('marketing');
            }
        }
    }

    public function update($id)
    {
        $this->form_validation->set_rules('nama_marketing_message', 'Nama Konten', 'required');
        $this->form_validation->set_rules('week_marketing_message', 'Minggu Ke', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap isi form dengan lengkap");
            redirect('marketing');
        } else {
            $insert = $this->MMarketingMessage->update($id);

            if ($insert) {
                $this->session->set_flashdata('success', "Berhasil mengubah konten");
                redirect('marketing');
            } else {
                $this->session->set_flashdata('failed', "Gagal mengubah konten");
                redirect('marketing');
            }
        }
    }

    public function delete($id)
    {
        $delete = $this->MMarketingMessage->delete($id);
        if ($delete) {
            $this->session->set_flashdata('success', "Berhasil menghapus konten");
            redirect('marketing');
        } else {
            $this->session->set_flashdata('failed', "Gagal menghapus konten");
            redirect('marketing');
        }
    }

    public function getMarketingTemplate()
    {

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://service-chat.qontak.com/api/open/v1/templates/whatsapp",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer _GEJodr1x8u7-nSn4tZK2hNq0M5CARkRp_plNdL2tFw"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $res = json_decode($response, true);
            $template = $res['data'];
            return  $template;
        }
    }
}
