<?php

class Sjretur extends CI_Controller
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
        $this->load->model('MVoucher');
        $this->load->model('Maxchathelper');
        $this->load->model('HTelegram');
        $this->load->library('form_validation');
    }

    public function city_list()
    {
        $data['title'] = 'Surat Jalan Retur';
        $data['menuGroup'] = 'SJ';
        $data['menu'] = 'SJRetur';
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Sjretur/CityList');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function index($id_city)
    {
        $get = $this->input->get();

        $dateFrom = date('Y-m-d', strtotime('-1 days'));
        $dateTo = date('Y-m-d');

        if (isset($get['daterange'])) {
            $dates = explode(' - ', $get['daterange']);
            $dateFrom = date('Y-m-d', strtotime($dates[0]));
            $dateTo = date('Y-m-d', strtotime($dates[1]));
        }

        $data['title'] = 'SJ Retur';
        $data['menuGroup'] = 'SJ';
        $data['menu'] = 'SJRetur';
        $data['sjretur'] = $this->db->join('tb_contact', 'tb_contact.id_contact = tb_sjretur.id_contact')->where('tb_contact.id_city', $id_city)->where('DATE(date_sjretur) >=', $dateFrom)->where('DATE(date_sjretur) <=', $dateTo)->get('tb_sjretur')->result_array();
        $data['toko'] = $this->MContact->getAll($id_city);
        $data['city'] = $this->MCity->getById($id_city);
        $data['gudangs'] = $this->db->get_where('tb_gudang_stok', ['id_distributor' => $this->session->userdata('id_distributor')])->result_array();
        $data['dateFrom'] = $dateFrom;
        $data['dateTo'] = $dateTo;
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Sjretur/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function create()
    {
        $post = $this->input->post();

        $sjreturData = [
            'no_sjretur' => 'RETUR-',
            'id_contact' => $post['id_contact'],
            'id_surat_jalan' => $post['id_surat_jalan'],
            'id_gudang_retur' => $post['id_gudang_retur'],
            'date_sjretur' => date('Y-m-d H:i:s'),
        ];

        $save = $this->db->insert('tb_sjretur', $sjreturData);

        if ($save) {
            $id_sjretur = $this->db->insert_id();

            $this->session->set_flashdata('success', "Berhasil membaut sj retur");
            redirect('sjretur/detail/' . $id_sjretur);
        } else {
            $this->session->set_flashdata('failed', "Gagal membaut sj retur");
            redirect('sjretur/' . $post['id_city']);
        }
    }

    public function delete($id_sjretur)
    {
        $sjretur = $this->db->join('tb_contact', 'tb_contact.id_contact = tb_sjretur.id_contact')->where('id_sjretur', $id_sjretur)->get('tb_sjretur')->row_array();

        $save = $this->db->delete('tb_sjretur', ['id_sjretur' => $id_sjretur]);

        if ($save) {
            $this->session->set_flashdata('success', "Berhasil menghapus retur");
            redirect('sjretur/' . $sjretur['id_city']);
        } else {
            $this->session->set_flashdata('failed', "Gagal menghapus retur");
            redirect('sjretur/' . $sjretur['id_city']);
        }
    }

    public function detail($id_sjretur)
    {
        $data['title'] = 'Surat Jalan';
        $data['menuGroup'] = 'SJ';
        $data['menu'] = 'SJRetur';
        $sjretur = $this->db->join('tb_contact', 'tb_contact.id_contact = tb_sjretur.id_contact')->where('id_sjretur', $id_sjretur)->get('tb_sjretur')->row_array();
        $data['sjretur'] = $sjretur;
        $toko = $this->MContact->getById($sjretur['id_contact']);
        $data['toko'] = $toko;
        $data['sjdetails'] = $this->MDetailSuratJalan->getAll($sjretur['id_surat_jalan']);
        $data['gudang'] = $this->db->where('id_gudang_stok', $sjretur['id_gudang_retur'])->get('tb_gudang_stok')->row_array();

        // Detail SJ Retur
        $this->db->join('tb_detail_surat_jalan', 'tb_detail_surat_jalan.id_detail_surat_jalan = tb_sjretur_detail.id_detail_surat_jalan');
        $this->db->join('tb_produk', 'tb_produk.id_produk = tb_detail_surat_jalan.id_produk');
        $this->db->join('tb_satuan', 'tb_satuan.id_satuan = tb_produk.id_satuan');
        $data['sjreturdetails'] = $this->db->where('id_sjretur', $id_sjretur)->get('tb_sjretur_detail')->result_array();

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Sjretur/Detail');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function createDetail()
    {
        $post = $this->input->post();

        $getSjDetail = $this->db->where('id_detail_surat_jalan', $post['id_detail_surat_jalan'])->get('tb_detail_surat_jalan')->row_array();

        if ($post['qty_sjretur_detail'] > $getSjDetail['qty_produk']) {
            $this->session->set_flashdata('failed', "Brang yang diretur tidak boleh lebih dari jumlah yang telah dikirim di surat jalan");
            redirect('sjretur/detail/' . $post['id_sjretur']);
        }

        $detailSjreturData = [
            'id_sjretur' => $post['id_sjretur'],
            'id_detail_surat_jalan' => $post['id_detail_surat_jalan'],
            'qty_sjretur_detail' => $post['qty_sjretur_detail'],
        ];

        $save = $this->db->insert('tb_sjretur_detail', $detailSjreturData);

        if ($save) {
            $this->session->set_flashdata('success', "Berhasil menambahkan produk retur");
            redirect('sjretur/detail/' . $post['id_sjretur']);
        } else {
            $this->session->set_flashdata('failed', "Gagal menambahkan produk retur");
            redirect('sjretur/detail/' . $post['id_sjretur']);
        }
    }

    public function deleteDetail($id_sjretur_detail)
    {
        $sjreturdetail = $this->db->where('id_sjretur_detail', $id_sjretur_detail)->get('tb_sjretur_detail')->row_array();

        $save = $this->db->delete('tb_sjretur_detail', ['id_sjretur_detail' => $id_sjretur_detail]);

        if ($save) {
            $this->session->set_flashdata('success', "Berhasil menghapus produk retur");
            redirect('sjretur/detail/' . $sjreturdetail['id_sjretur']);
        } else {
            $this->session->set_flashdata('failed', "Gagal menghapus produk retur");
            redirect('sjretur/detail/' . $sjreturdetail['id_sjretur']);
        }
    }

    public function finish($id_sjretur)
    {
        $sjretur = $this->db->join('tb_contact', 'tb_contact.id_contact = tb_sjretur.id_contact')->where('id_sjretur', $id_sjretur)->get('tb_sjretur')->row_array();

        $this->db->join('tb_detail_surat_jalan', 'tb_detail_surat_jalan.id_detail_surat_jalan = tb_sjretur_detail.id_detail_surat_jalan');
        $this->db->join('tb_produk', 'tb_produk.id_produk = tb_detail_surat_jalan.id_produk');
        $this->db->join('tb_satuan', 'tb_satuan.id_satuan = tb_produk.id_satuan');
        $sjreturdetails = $this->db->where('id_sjretur', $id_sjretur)->get('tb_sjretur_detail')->result_array();

        foreach ($sjreturdetails as $sjreturdetail) {
            for ($i = 0; $i < $sjreturdetail['qty_sjretur_detail']; $i++) {
                $voucherData = [
                    'id_contact' => $sjretur['id_contact'],
                    'no_voucher' => 'RTR' . str_pad($sjreturdetail['id_sjretur_detail'], 4, "0", STR_PAD_LEFT),
                    'no_fisik' => '',
                    'point_voucher' => 1,
                    'value_voucher' => $sjreturdetail['price'],
                    'is_claimed' => 1,
                    'exp_date' => date('Y-m-d H:i:s', strtotime("+1 month")),
                    'type_voucher' => 'retur',
                    'id_detail_surat_jalan' => $sjreturdetail['id_detail_surat_jalan'],
                ];

                $this->db->insert('tb_voucher', $voucherData);
            }
        }

        $this->db->update('tb_sjretur', ['is_finished' => 1], ['id_sjretur' => $id_sjretur]);

        $this->session->set_flashdata('success', "SJ retur berhasil dibuat");
        redirect('sjretur/' . $sjretur['id_city']);
    }

    public function getStoreSj($id_contact)
    {
        $suratjalans = $this->db->get_where('tb_surat_jalan', ['id_contact' => $id_contact, 'is_closing' => 1])->result_array();

        return $this->output->set_output(json_encode($suratjalans));
    }
}
