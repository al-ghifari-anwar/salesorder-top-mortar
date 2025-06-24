<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Renvimg extends CI_Controller
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
        $data['title'] = 'Rencana Visit MG';
        if ($this->session->userdata('level_user') == 'admin_c' || $this->session->userdata('level_user') == 'sales') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Renvimg/CityList');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function index($id_city)
    {
        $data['title'] = 'Rencana Visit MG';
        $data['toko'] = $this->MContact->getAllForRenviMg($id_city);
        $this->db->select("tb_rencana_visit.*, tb_contact.nama, tb_contact.nomorhp, tb_contact.id_city, tb_contact.store_status, tb_contact.store_owner, tb_contact.maps_url, tb_contact.created_at AS created_at_store, tb_contact.reputation");
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_rencana_visit.id_contact');
        $this->db->where('tb_sontact.id_contact NOT IN (SELECT id_contact FROM tb_bad_score WHERE is_approved = 1)', null, false);
        $this->db->group_by('tb_rencana_visit.id_contact');
        $data['renvimg'] = $this->db->get_where('tb_rencana_visit', ['id_city' => $id_city, 'type_rencana' => 'mg', 'is_visited' => 0])->result_array();
        $data['id_city'] = $id_city;
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Renvimg/Index');
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
            'type_rencana' => 'mg',
            'id_distributor' => $contact['id_distributor'],
            'id_invoice' => 0
        ];

        $insert = $this->db->insert('tb_rencana_visit', $data);

        if ($insert) {
            $this->session->set_flashdata('success', "Berhasil menambah data rencana visit!");
            redirect('renvimg/' . $contact['id_city']);
        } else {
            $this->session->set_flashdata('failed', "Gagal menambah data rencana visit!");
            redirect('renvimg/' . $contact['id_city']);
        }
    }

    public function delete($id_contact)
    {
        // $insert = $this->db->delete('tb_rencana_visit', ['id_antrian_renvimg' => $id]);

        $insert = $this->db->update('tb_rencana_visit', ['is_visited' => 1], ['id_contact' => $id_contact, 'type_rencana' => 'mg']);

        if ($insert) {
            $this->session->set_flashdata('success', "Berhasil megnhapus data renvi!");
            redirect('renvimg');
        } else {
            $this->session->set_flashdata('failed', "Gagal megnhapus data renvi!");
            redirect('renvimg');
        }
    }

    public function print($id_city)
    {
        $data['city'] = $this->MCity->getById($id_city);
        $data['contacts'] = $this->MContact->getAllNoFilter($id_city);
        // PDF
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Renvimg/Print', $data, true);
        $mpdf->AddPage('P');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }
}
