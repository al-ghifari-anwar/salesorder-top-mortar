<?php

class Clustertoko extends CI_Controller
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
        $this->load->model('MRenvi');
        $this->load->model('MInvoice');
        $this->load->library('form_validation');
    }



    public function index()
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Toko Tanpa Cluster';
        $data['menuGroup'] = 'Visit';
        $data['menu'] = 'TokoNoCluster';
        if ($this->session->userdata('level_user') == 'admin_c' || $this->session->userdata('level_user') == 'sales') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Clustertoko/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function print()
    {
        $id_city = $_GET['ct'];

        $data['contacts'] = $this->db->get_where('tb_contact', ['cluster' => 0, 'id_city' => $id_city])->result_array();
        $data['contact1s'] = $this->db->get_where('tb_contact', ['cluster' => 1, 'id_city' => $id_city])->result_array();
        $data['contact2s'] = $this->db->get_where('tb_contact', ['cluster' => 2, 'id_city' => $id_city])->result_array();
        $data['contact3s'] = $this->db->get_where('tb_contact', ['cluster' => 3, 'id_city' => $id_city])->result_array();

        // $this->load->view('Jadwalvisit/Print', $data);

        // Buat direktori penyimpanan sementara
        // $folderPath = FCPATH . 'assets/tmp/renvis/';
        // Nama file berdasarkan invoice ID + timestamp
        // $fileName = 'renvi_' . $this->session->userdata('id_user') . '_' . time() . '.pdf';
        // $filePath = $folderPath . $fileName;

        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Clustertoko/Print', $data, true);
        $mpdf->AddPage('P');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
        // $mpdf->Output($filePath, \Mpdf\Output\Destination::FILE);

        // $user = $this->db->get_where('tb_user', ['id_user' => $this->session->userdata('id_user')])->row_array();
    }
}
