<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Renvis extends CI_Controller
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
        $this->load->library('form_validation');
    }

    public function city_list()
    {
        $data['title'] = 'Rencana Visit';
        if ($this->session->userdata('level_user') == 'admin_c' || $this->session->userdata('level_user') == 'sales') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Renvis/CityList');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function index($id_city)
    {
        $data['title'] = 'Surat Jalan';
        $data['toko'] = $this->MContact->getAll($id_city);
        $this->db->select("tb_rencana_visit.*, tb_contact.nama, tb_contact.nomorhp, tb_contact.id_city, tb_contact.store_status, tb_contact.store_owner, tb_contact.maps_url, tb_contact.created_at AS created_at_store, tb_contact.reputation");
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_rencana_visit.id_contact');
        $data['renvis'] = $this->db->get_where('tb_rencana_visit', ['type_rencana' => 'passive', 'id_city' => $id_city, 'is_visited' => 0])->result_array();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Renvis/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function insert()
    {
        $post = $this->input->post();

        $contact = $this->MContact->getById($post['id_contact']);

        $data = [
            'id_contact' => $post['id_contact'],
            'id_surat_jalan' => 0,
            'is_visited' => 0,
            'type_rencana' => 'passive',
            'id_distributor' => $contact['id_distributor']
        ];

        $insert = $this->db->insert('tb_rencana_visit', $data);

        if ($insert) {
            $this->session->set_flashdata('success', "Berhasil menambah data rencana visit!");
            redirect('renvis');
        } else {
            $this->session->set_flashdata('failed', "Gagal menambah data rencana visit!");
            redirect('renvis');
        }
    }

    public function delete($id)
    {
        $insert = $this->MSuratJalan->delete($id);

        if ($insert) {
            $this->session->set_flashdata('success', "Berhasil megnhapus data suratjalan!");
            redirect('surat-jalan');
        } else {
            $this->session->set_flashdata('failed', "Gagal megnhapus data suratjalan!");
            redirect('surat-jalan');
        }
    }
}