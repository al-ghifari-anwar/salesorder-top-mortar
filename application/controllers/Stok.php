<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Stok extends CI_Controller
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
        $this->load->model('MVisit');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['title'] = 'Stok Produk';
        // if ($this->session->userdata('level_user') == 'admin_c') {
        //     $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        // } else {
        $data['gudangs'] = $this->db->get('tb_gudang_stok')->result_array();
        // }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Stok/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function list($id_gudang_stok)
    {
        $data['title'] = 'Stok Produk';
        $data['id_gudang_stok'] = $id_gudang_stok;

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Stok/List');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function sync_stok()
    {
        $dateBorder = date("Y-m-d", strtotime("2025-07-20"));
        $detailSjstoks = $this->db->get_where('tb_detail_sj_stok', ['DATE(created_at) >=' => $dateBorder, 'qty_rechieved' => 0])->result_array();

        foreach ($detailSjstoks as $detailSjstok) {
            $sjstok = $this->db->get_where('tb_sj_stok', ['id_sj_stok' => $detailSjstok['id_sj_stok']])->row_array();

            if ($sjstok != null) {

                $id_detail_sj_stok = $detailSjstok['id_detail_sj_stok'];
                $id_gudang_stok = $sjstok['id_gudang_stok'];

                $detailSjStokData = [
                    'qty_rechieved' => $detailSjstok['qty_detail_sj_stok'],
                    'updated_at' => date("Y-m-d H:i:s"),
                ];

                $jmlStok = [
                    'id_gudang_stok' => $id_gudang_stok,
                    'id_master_produk' => $detailSjstok['id_master_produk'],
                    'jml_stok' => $detailSjstok['qty_detail_sj_stok'],
                    'created_at' => date("Y-m-d H:i:s"),
                    'status_stok' => 'in',
                ];

                $this->db->update('tb_detail_sj_stok', $detailSjStokData, ['id_detail_sj_stok' => $id_detail_sj_stok]);

                $this->db->insert('tb_stok', $jmlStok);
            }
        }
    }

    public function lap_stok()
    {
        $post = $this->input->post();
        $dateRange = $post["date_range"];
        $id_gudang_stok = $post["id_gudang_stok"];

        // ID DIST
        if ($this->session->userdata('id_distributor') == 7) {
            $id_distributor = 1;
        } else {
            $id_distributor = $this->session->userdata('id_distributor');
        }

        $data['gudang'] = $this->db->get_where('tb_gudang_stok', ['id_gudang_stok' => $id_gudang_stok])->row_array();
        $data['masterProduks'] = $this->db->get_where("tb_master_produk", ['id_distributor' => $id_distributor, 'name_master_produk !=' => '-'])->result_array();

        $data['dates'] = explode("-", $dateRange);
        // $this->load->view('Stok/Print', $data);
        // PDF
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Stok/Print', $data, true);
        $mpdf->AddPage('L');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }
}
