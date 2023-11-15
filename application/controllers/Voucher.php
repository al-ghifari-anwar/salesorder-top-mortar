<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Voucher extends CI_Controller
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
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['title'] = 'Voucher';
        $data['city'] = $this->MCity->getAll();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Voucher/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function regist_voucher($id_city)
    {
        $this->form_validation->set_rules('no_voucher', 'Nomor Voucher', 'required|is_unique[tb_voucher.no_voucher]');

        $data['title'] = 'Register Voucher';
        $data['id_city'] = $id_city;

        if($this->form_validation->run() == false){
            $data['store'] = $this->MContact->getAll($id_city);
            $this->load->view('Theme/Header', $data);
            $this->load->view('Theme/Menu');
            $this->load->view('Voucher/Register');
            $this->load->view('Theme/Footer');
            $this->load->view('Theme/Scripts');
        } else {
            $insert = $this->MVoucher->insert();

            if($insert){
                $this->session->set_flashdata('success', "Berhasil menyimpan voucher!");
                redirect('reg-voucher/' . $id_city);
            } else {
                $this->session->set_flashdata('failed', "Gagal menyimpan voucher!");
                redirect('reg-voucher/' . $id_city);
            }
        }
    }

    public function claim()
    {
        $this->form_validation->set_rules('no_voucher', 'Nomor Voucher', 'required');

        if($this->form_validation->run() == false){
            $data['title'] = 'Claim Voucher';
            $this->load->view('Theme/Header', $data);
            $this->load->view('Theme/Menu');
            $this->load->view('Voucher/ClaimForm');
            $this->load->view('Theme/Footer');
            $this->load->view('Theme/Scripts');
        } else {
            $this->MVoucher->getByNomor();
        }
    }
}