<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PriorityStore extends CI_Controller
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
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
    }

    public function city_list()
    {
        $data['title'] = 'Top Mortar Priority';
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('PriorityStore/CityList');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function index($id_city)
    {
        $data['title'] = 'Top Mortar Priority';
        $data['contacts'] = $this->MContact->getAllForPriority($id_city);
        $data['contactPriors'] = $this->MContact->getAllPriority($id_city);
        $data['city'] = $this->MCity->getById($id_city);
        $data['id_city'] = $id_city;
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('ProrityStore/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function add()
    {
        $post = $this->input->post();

        $id_contact = $post['id_contact'];

        $getContact = $this->MContact->getById($id_contact);

        $id_city = $getContact['id_city'];

        // Generate QR
        $this->load->library('ciqrcode');
        $config['cacheable']    = true; //boolean, the default is true
        $config['cachedir']             = './assets/'; //string, the default is application/cache/
        $config['errorlog']             = './assets/'; //string, the default is application/logs/
        $config['imagedir']             = './assets/img/qr/'; //direktori penyimpanan qr code
        $config['quality']              = true; //boolean, the default is true
        $config['size']                 = '1024'; //interger, the default is 1024
        $config['black']                = array(224, 255, 255); // array, default is array(255,255,255)
        $config['white']                = array(70, 130, 180); // array, default is array(0,0,0)
        $this->ciqrcode->initialize($config);

        $image_name = "Priority_" . $id_contact . '.png'; //buat name dari qr code sesuai dengan nim

        $link = base_url('priority/') . $id_contact;

        $params['data'] = $link; //data yang akan di jadikan QR CODE
        $params['level'] = 'H'; //H=High
        $params['size'] = 10;
        $params['savename'] = FCPATH . $config['imagedir'] . $image_name; //simpan image QR CODE ke folder assets/images/
        $this->ciqrcode->generate($params); // fungsi untuk generate QR CODE

        $update = $this->db->update('tb_contact', ['is_priority' => 1, 'qr_toko' => $image_name], ['id_contact' => $id_contact]);

        if ($update) {
            $this->session->set_flashdata('success', "Berhasil menambah toko prioritas!");
            redirect('prioritystore/' . $id_city);
        } else {
            $this->session->set_flashdata('failed', "Gagal menambah toko prioritas");
            redirect('prioritystore/' . $id_city);
        }
    }

    public function delete($id_contact)
    {
        $getContact = $this->MContact->getById($id_contact);
        $id_city = $getContact['id_city'];

        $update = $this->db->update('tb_contact', ['is_priority' => 0, 'qr_toko' => ''], ['id_contact' => $id_contact]);

        if ($update) {
            $this->session->set_flashdata('success', "Berhasil menghapus toko prioritas!");
            redirect('prioritystore/' . $id_city);
        } else {
            $this->session->set_flashdata('failed', "Gagal menghapus toko prioritas");
            redirect('prioritystore/' . $id_city);
        }
    }
}