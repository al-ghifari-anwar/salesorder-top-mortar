<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SuratJalan extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $this->load->model('MSuratJalan');
        $this->load->model('MContact');
        $this->load->model('MProduk');
        $this->load->model('MDetailSuratJalan');
        $this->load->model('MUser');
        $this->load->model('MCity');
        $this->load->model('MKendaraan');
        $this->load->library('form_validation');
    }

    public function city_list()
    {
        $data['title'] = 'Produk';
        $data['city'] = $this->MCity->getAll();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('SuratJalan/CityList');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function index($id_city)
    {
        $data['title'] = 'Surat Jalan';
        $data['suratjalan'] = $this->MSuratJalan->getByCity($id_city);
        $data['toko'] = $this->MContact->getAll($id_city);
        $data['kurir'] = $this->MUser->getAll($id_city);
        $data['kendaraan'] = $this->MKendaraan->getAll();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('SuratJalan/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function detail($id)
    {
        $data['title'] = 'Surat Jalan';
        $data['suratjalan'] = $this->MSuratJalan->getById($id);
        $suratjalan = $this->MSuratJalan->getById($id);
        $data['toko'] = $this->MContact->getById($suratjalan['id_contact']);
        $toko = $this->MContact->getById($suratjalan['id_contact']);
        $data['produk'] = $this->MProduk->getByCity($toko['id_city']);
        $data['detail'] = $this->MDetailSuratJalan->getAll($suratjalan['id_surat_jalan']);
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('SuratJalan/Detail');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function not_closing()
    {
        $data['title'] = 'Surat Jalan Belum Colsing';
        $data['suratjalan'] = $this->MSuratJalan->getNotClosing();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('SuratJalan/NotClosing');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function insert()
    {
        $this->form_validation->set_rules('order_number', 'Order Number', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap lengkapi form!");
            redirect('surat-jalan');
        } else {
            $this->MSuratJalan->insert();
        }
    }

    public function insertdetail()
    {
        $this->form_validation->set_rules('qty_produk', 'QTY', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap lengkapi form!");
            redirect('surat-jalan/' . $this->input->post('id_surat_jalan'));
        } else {
            $this->MDetailSuratJalan->insert();
        }
    }

    public function updatedetail($id)
    {
        $this->form_validation->set_rules('qty_produk', 'QTY', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap lengkapi form");
            redirect('surat-jalan/' . $this->input->post('id_surat_jalan'));
        } else {
            $insert = $this->MDetailSuratJalan->update($id);

            if ($insert) {
                $this->session->set_flashdata('success', "Berhasil mengubah data produk!");
                redirect('surat-jalan/' . $this->input->post('id_surat_jalan'));
            } else {
                $this->session->set_flashdata('failed', "Gagal mengubah data produk!");
                redirect('surat-jalan/' . $this->input->post('id_surat_jalan'));
            }
        }
    }

    public function finish($id)
    {
        $suratjalan = $this->MSuratJalan->getById($id);

        $wa_token = 'xz5922BoBI6I9ECLKVZjPMm-7-0sqx0cjIqVVeuWURI';
        $template_id = '32b18403-e0ee-4cfc-9e2e-b28b95f24e37';
        $integration_id = '31c076d5-ac80-4204-adc9-964c9b0c590b';

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://service-chat.qontak.com/api/open/v1/broadcasts/whatsapp/direct',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                    "to_number": "' . $suratjalan['phone_user'] . '",
                    "to_name": "' . $suratjalan['full_name'] . '",
                    "message_template_id": "' . $template_id . '",
                    "channel_integration_id": "' . $integration_id . '",
                    "language": {
                        "code": "id"
                    },
                    "parameters": {
                        "body": [
                        {
                            "key": "1",
                            "value": "nama",
                            "value_text": "' . $suratjalan['full_name'] . '"
                        },
                        {
                            "key": "2",
                            "value": "store",
                            "value_text": "' . $suratjalan['nama'] . '"
                        },
                        {
                            "key": "3",
                            "value": "address",
                            "value_text": "' . $suratjalan['address'] . ', ' . $suratjalan['nama_city'] . '"
                        },
                        {
                            "key": "4",
                            "value": "no_surat",
                            "value_text": "' . $suratjalan['no_surat_jalan'] . '"
                        }
                        ]
                    }
                    }',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $wa_token,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $res = json_decode($response, true);

        $status = $res['status'];

        if ($status == "success") {
            $this->db->update('tb_surat_jalan', ['is_finished' => 1], ['id_surat_jalan' => $suratjalan['id_surat_jalan']]);
            $this->session->set_flashdata('success', "Surat jalan berhasil dibuat!");
            redirect('surat-jalan');
        } else {
            $this->session->set_flashdata('failed', "Surat jalan gagal dibuat!");
            redirect('surat-jalan');
        }
    }

    public function deletedetail($id)
    {
        $insert = $this->MDetailSuratJalan->delete($id);

        if ($insert) {
            $this->session->set_flashdata('success', "Berhasil megnhapus data produk!");
            redirect('surat-jalan/' . $this->input->post('id_surat_jalan'));
        } else {
            $this->session->set_flashdata('failed', "Gagal megnhapus data produk!");
            redirect('surat-jalan/' . $this->input->post('id_surat_jalan'));
        }
    }

    public function delete($id)
    {
        $insert = $this->MSuratJalan->delete($id);

        if ($insert) {
            $this->session->set_flashdata('success', "Berhasil megnhapus data suratjalan!");
            redirect('surat-jalan');
        } else {
            $this->session->set_flashdata('failed', "Gagal megnhapus data suratjalan!");
            redirect('surat-jalan');
        }
    }
}
