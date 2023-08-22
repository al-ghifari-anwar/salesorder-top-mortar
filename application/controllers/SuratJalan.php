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
        $data['suratjalan'] = $this->MSuratJalan->getAll();
        $data['toko'] = $this->MContact->getAll($id_city);
        $data['kurir'] = $this->MUser->getAll($id_city);
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
            redirect('suratjalan/' . $id);
        } else {
            $this->session->set_flashdata('failed', "Gagal megnhapus data suratjalan!");
            redirect('suratjalan/' . $id);
        }
    }
}
