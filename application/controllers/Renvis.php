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
        $data['menuGroup'] = 'InputRenvi';
        $data['menu'] = 'Renvis';
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
        $data['title'] = 'Rencana Visit';
        $data['menuGroup'] = 'InputRenvi';
        $data['menu'] = 'Renvis';
        $data['toko'] = $this->MContact->getAllForRenvis($id_city);
        $this->db->select("tb_antrian_renvis.*, tb_contact.nama, tb_contact.nomorhp, tb_contact.id_city, tb_contact.store_status, tb_contact.store_owner, tb_contact.maps_url, tb_contact.created_at AS created_at_store, tb_contact.reputation");
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_antrian_renvis.id_contact');
        $data['renvis'] = $this->db->get_where('tb_antrian_renvis', ['id_city' => $id_city])->result_array();
        $data['id_city'] = $id_city;
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
        $dateRenvis = $post['date_renvis'];
        $interval = $post['interval_renvis'];

        $data = [
            'id_contact' => $post['id_contact'],
            'id_distributor' => $contact['id_distributor'],
            'date_renvis' => $dateRenvis,
            'interval_renvis' => $interval,
            'updated_at' => date("Y-m-d H:i:s")
        ];

        $insert = $this->db->insert('tb_antrian_renvis', $data);

        if ($insert) {
            $this->session->set_flashdata('success', "Berhasil menambah data rencana visit!");
            redirect('renvis');
        } else {
            $this->session->set_flashdata('failed', "Gagal menambah data rencana visit!");
            redirect('renvis');
        }
    }

    public function delete($id, $id_contact)
    {
        $insert = $this->db->delete('tb_antrian_renvis', ['id_antrian_renvis' => $id]);

        $this->db->update('tb_rencana_visit', ['is_visited' => 1], ['id_contact' => $id_contact]);

        if ($insert) {
            $this->session->set_flashdata('success', "Berhasil megnhapus data renvi!");
            redirect('renvis');
        } else {
            $this->session->set_flashdata('failed', "Gagal megnhapus data renvi!");
            redirect('renvis');
        }
    }

    public function print($id_city)
    {
        $data['city'] = $this->MCity->getById($id_city);
        $data['contacts'] = $this->MContact->getAllNoFilter($id_city);
        $data['menuGroup'] = 'InputRenvi';
        $data['menu'] = 'Renvis';
        // PDF
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Renvis/Print', $data, true);
        $mpdf->AddPage('P');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }
}
