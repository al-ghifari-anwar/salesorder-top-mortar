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
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['contactPriors'] = $this->MContact->getAllTopSellerCity($this->session->userdata('id_city'));
        } else {
            $data['contactPriors'] = $this->MContact->getAllTopSeller();
        }
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
        // echo json_encode($data);
        // die;
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Akunseller/Penukaran');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function data_tukang()
    {
        $data['title'] = 'Validasi Voucher Tukang Top Mortar';
        $data['catcuss'] = $this->db->get('tb_catcus')->result_array();
        $data['skills'] = $this->db->get('tb_skill')->result_array();
        $data['vouchers'] = $this->MVoucherTukang->getForValidasi();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Akunseller/DataTukang');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function add_quota()
    {
        $post = $this->input->post();

        $id_contact = $post['id_contact'];

        $dataQuota = [
            'id_contact' => $post['id_contact'],
            'val_quota_toko' => $post['val_quota_toko'],
            'id_user' => $this->session->userdata('id_user'),
            'updated_at' => date("Y-m-d H:i:s")
        ];

        $saveQuota = $this->db->insert('tb_quota_toko', $dataQuota);

        if ($saveQuota) {
            $contact = $this->db->get_where('tb_contact', ['id_contact' => $id_contact])->row_array();
            $quotaOld = $contact['quota_priority'];
            $quotaNew = $quotaOld + $post['val_quota_toko'];

            $dataUpdateQuota = [
                'quota_priority' => $quotaNew
            ];

            $updateQuota = $this->db->update('tb_contact', $dataUpdateQuota, ['id_contact' => $id_contact]);

            if ($updateQuota) {
                $this->session->set_flashdata('success', "Berhasil menambah quota toko");
                redirect('akunseller');
            } else {
                $this->session->set_flashdata('failed', "Gagal mengupdate quota, harap coba lagi");
                redirect('akunseller');
            }
        } else {
            $this->session->set_flashdata('failed', "Gagal, harap coba lagi");
            redirect('akunseller');
        }
    }
}
