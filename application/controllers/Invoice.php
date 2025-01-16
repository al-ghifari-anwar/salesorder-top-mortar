<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Invoice extends CI_Controller
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
        $this->load->library('form_validation');
        // $this->load->library('phpqrcode/qrlib');
    }

    public function index()
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Invoice';
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Invoice/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function invoice_by_city($id_city)
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Invoice';
        $data['cities'] = $this->MCity->getAll();
        $data['city'] = $this->MCity->getById($id_city);
        $data['invoice'] = $this->MInvoice->getAll($id_city);
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Invoice/List');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function sent()
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Sent Invoice';
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Invoice/Sent');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function sent_by_city($id_city)
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Invoice';
        $data['cities'] = $this->MCity->getAll();
        $data['city'] = $this->MCity->getById($id_city);
        $data['invoice'] = $this->MInvoice->getAll($id_city);
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Invoice/SentByCity');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function print($id)
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $this->load->library('ciqrcode');
        $invoice = $this->MInvoice->getById($id);
        $data['invoice'] = $invoice;
        $data['store'] = $this->MContact->getById($invoice['id_contact']);
        $data['kendaraan'] = $this->MKendaraan->getById($invoice['id_kendaraan']);
        $data['courier'] = $this->MUser->getById($invoice['id_courier']);
        $data['produk'] = $this->MDetailSuratJalan->getAll($invoice['id_surat_jalan']);
        // Generate QR
        $config['cacheable']    = true; //boolean, the default is true
        $config['cachedir']             = './assets/'; //string, the default is application/cache/
        $config['errorlog']             = './assets/'; //string, the default is application/logs/
        $config['imagedir']             = './assets/img/qr/'; //direktori penyimpanan qr code
        $config['quality']              = true; //boolean, the default is true
        $config['size']                 = '1024'; //interger, the default is 1024
        $config['black']                = array(224, 255, 255); // array, default is array(255,255,255)
        $config['white']                = array(70, 130, 180); // array, default is array(0,0,0)
        $this->ciqrcode->initialize($config);

        $image_name = $invoice['id_invoice'] . '.png'; //buat name dari qr code sesuai dengan nim

        $params['data'] = base_url('invoice-confirm/') . $invoice['id_invoice']; //data yang akan di jadikan QR CODE
        $params['level'] = 'H'; //H=High
        $params['size'] = 10;
        $params['savename'] = FCPATH . $config['imagedir'] . $image_name; //simpan image QR CODE ke folder assets/images/
        $this->ciqrcode->generate($params); // fungsi untuk generate QR CODE

        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Invoice/Print', $data, true);
        $mpdf->AddPage('P');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    public function confirm($id)
    {
        $invoice = $this->db->get_where('tb_invoice', ['id_invoice' => $id])->row_array();

        if ($invoice['is_printed'] == 0) {
            $confirm = $this->db->update('tb_invoice', ['is_printed' => 1, 'date_printed' => date("Y-m-d H:i:s")], ['id_invoice' => $id]);

            if ($confirm) {
                $this->session->set_flashdata('success', "Berhasil konfirmasi Invoice!");
                redirect('login');
            } else {
                $this->session->set_flashdata('failed', "Gagal konfirmasi Invoice!");
                redirect('login');
            }
        } else if ($invoice['is_printed'] == 1) {
            $confirm = $this->db->update('tb_invoice', ['is_rechieved' => 1, 'date_rechieved' => date("Y-m-d H:i:s")], ['id_invoice' => $id]);

            if ($confirm) {
                $this->session->set_flashdata('success', "Terimakasih, invoice telah diterima!");
                redirect('login');
            } else {
                $this->session->set_flashdata('failed', "Gagal merubah status penerimaan Invoice!");
                redirect('login');
            }
        }
    }
}
