<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Akunseller extends CI_Controller
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
        $this->load->model('MVoucherTukang');
        $this->load->library('form_validation');
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
    }

    public function city_list()
    {
        $data['title'] = 'Top Mortar Seller';
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Akunseller/CityList');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function index()
    {
        $data['title'] = 'Top Mortar Seller';
        // $data['contacts'] = $this->MContact->getAllForPriority($id_city);
        $data['contactPriors'] = $this->MContact->getAllTopSeller();
        // $data['city'] = $this->MCity->getById($id_city);
        // $data['id_city'] = $id_city;
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Akunseller/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function penukaran()
    {
        $data['title'] = 'Penukaran Voucher Tukang Top Mortar';
        $dateRange = $this->input->post("date_range");

        $data['vouchers'] = $this->MVoucherTukang->getForPenukaran(date("Y-m-d"), date("Y-m-d"));

        if ($dateRange) {
            $dates = explode("-", $dateRange);
            $data['vouchers'] = $this->MVoucherTukang->getForPenukaran(date("Y-m-d", strtotime($dates[0])), date("Y-m-d", strtotime($dates[1])));
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Akunseller/Penukaran');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function data_tukang()
    {
        $data['title'] = 'Validasi Voucher Tukang Top Mortar';
        $data['vouchers'] = $this->MVoucherTukang->getForValidasi();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Akunseller/DataTukang');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function delete($id_contact)
    {
        $getContact = $this->MContact->getById($id_contact);
        $id_city = $getContact['id_city'];

        $update = $this->db->update('tb_contact', ['is_priority' => 0, 'qr_toko' => '', 'quota_priority' => 0, 'is_tokopromo' => 0], ['id_contact' => $id_contact]);

        if ($update) {
            $this->session->set_flashdata('success', "Berhasil menghapus toko prioritas!");
            redirect('tokopromostore/' . $id_city);
        } else {
            $this->session->set_flashdata('failed', "Gagal menghapus toko prioritas");
            redirect('tokopromostore/' . $id_city);
        }
    }
}
