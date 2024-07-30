<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Vctukang extends CI_Controller
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
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Vctukang/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function verify()
    {
        $no_seri = $this->input->post("no_seri");

        if ($no_seri == null) {
            $this->session->set_flashdata('failed', "Nomor seri tidak boleh kosong!");
            redirect('vctukang');
        } else {
            $this->db->join('tb_city', 'tb_city.id_city = tb_tukang.id_city');
            $getTukang = $this->db->get_where('tb_tukang', ['nomorhp' => $no_seri])->row_array();

            if (!$getTukang) {
                $this->session->set_flashdata('failed', "Nomor seri tidak terdaftar!");
                redirect('vctukang');
            } else {
                $getClaimed = $this->db->get_where('tb_voucher_tukang', ['no_seri' => $no_seri])->row_array();

                if ($getClaimed) {
                    $this->session->set_flashdata('failed', "Nomor seri sudah pernah di claim!");
                    redirect('vctukang');
                } else {
                    $id_distributor = $getTukang['id_distributor'];
                    $wa_token = '_GEJodr1x8u7-nSn4tZK2hNq0M5CARkRp_plNdL2tFw';
                    $template_id = '781b4601-fba6-4c69-81ad-164a680ecce7';
                    $qontak = $this->db->get_where('tb_qontak', ['id_distributor' => $id_distributor])->row_array();
                    $integration_id = $qontak['integration_id'];

                    // Generate QR
                    $this->load->library('ciqrcode');
                    $config['cacheable']    = true; //boolean, the default is true
                    $config['cachedir']             = './assets/'; //string, the default is application/cache/
                    $config['errorlog']             = './assets/'; //string, the default is application/logs/
                    $config['imagedir']             = './assets/img/qr-vctukang/'; //direktori penyimpanan qr code
                    $config['quality']              = true; //boolean, the default is true
                    $config['size']                 = '1024'; //interger, the default is 1024
                    $config['black']                = array(224, 255, 255); // array, default is array(255,255,255)
                    $config['white']                = array(70, 130, 180); // array, default is array(0,0,0)
                    $this->ciqrcode->initialize($config);

                    $image_name = $no_seri['id_invoice'] . '.png'; //buat name dari qr code sesuai dengan nim

                    $params['data'] = base_url('vctukang/') . $getTukang['id_tukang']; //data yang akan di jadikan QR CODE
                    $params['level'] = 'H'; //H=High
                    $params['size'] = 10;
                    $params['savename'] = FCPATH . $config['imagedir'] . $image_name; //simpan image QR CODE ke folder assets/images/
                    $this->ciqrcode->generate($params); // fungsi untuk generate QR CODE
                }
            }
        }
    }

    public function toko($id_tukang)
    {
        $data['title'] = 'Voucher';
        $data['tukang'] = $this->db->get_where('tb_tukang', ['id_tukang' => $id_tukang])->row_array();
        $data['banks'] = $this->db->get('tb_bank')->result_array();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Vctukang/Toko');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function list_voucher($id_city)
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Voucher List';
        $data['city'] = $this->MCity->getAll();
        $data['voucher'] = $this->MVoucher->getByCity($id_city);
        $data['contact'] = $this->MContact->getAllForVouchers($id_city);
        $data['id_city'] = $id_city;
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Voucher/List');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function claim()
    {
        $this->form_validation->set_rules('no_voucher1', 'Nomor Voucher', 'required');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'Claim Voucher';
            $this->load->view('Theme/Header', $data);
            $this->load->view('Theme/Menu');
            $this->load->view('Voucher/ClaimForm');
            $this->load->view('Theme/Footer');
            $this->load->view('Theme/Scripts');
        } else {
            $data['title'] = 'Claim Voucher';
            $claimed = $this->MVoucher->getByNomor();
            $data['claimed'] = $claimed;
            $data['toko'] = $this->MContact->getById($claimed['id_contact']);

            $this->load->view('Theme/Header', $data);
            $this->load->view('Theme/Menu');
            $this->load->view('Voucher/Claimed');
            $this->load->view('Theme/Footer');
            $this->load->view('Theme/Scripts');
        }
    }
}
