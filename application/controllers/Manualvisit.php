<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Manualvisit extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $this->load->model('MCity');
        $this->load->model('MContact');
        $this->load->model('MUser');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['title'] = 'Manual Visit';
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Manualvisit/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function city($id_city)
    {
        $data['title'] = 'Manual Visit';
        $data['contacts'] = $this->MContact->getAll($id_city);
        $data['users'] = $this->MUser->getAllForManualRenvi($id_city);
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Manualvisit/Detail');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function insert()
    {
        $post = $this->input->post();

        $id_contact = $post['id_contact'];
        $id_user = $post['id_user'];
        $source_visit = $post['source_visit'];
        $laporan_visit = $post['laporan_visit'];
        $manual_user = $post['manual_user'];

        $data = [
            'id_contact' => $id_contact,
            'distance_visit' => 0.001,
            'laporan_visit' => $laporan_visit,
            'source_visit' => $source_visit,
            'id_user' => $id_user,
            'date_visit' => date("Y-m-d H:i:s"),
            'is_manual' => 1,
            'manual_user' => $manual_user
        ];

        $insert = $this->db->insert('tb_visit', $data);

        if ($insert) {
            $visitDate = date("Y-m-d");
            if ($source_visit == 'jatem3') {
                $this->db->query("UPDATE tb_rencana_visit SET is_visited = 1, visit_date = '$visitDate' WHERE id_contact = '$id_contact' AND type_rencana = 'jatem'");

                $this->db->query("UPDATE tb_renvis_jatem SET is_visited = 1, visit_date = '$visitDate' WHERE id_contact = '$id_contact' AND type_renvis = 'jatem3'");
            } else {
                if ($source_visit == 'jatem2') {
                    $this->db->query("UPDATE tb_renvis_jatem SET is_visited = 1, visit_date = '$visitDate' WHERE id_contact = '$id_contact' AND type_renvis = 'jatem2'");
                } else if ($source_visit == 'jatem1') {
                    $this->db->query("UPDATE tb_renvis_jatem SET is_visited = 1, visit_date = '$visitDate' WHERE id_contact = '$id_contact' AND type_renvis = 'jatem1'");
                } else if ($source_visit == 'weekly') {
                    $this->db->query("UPDATE tb_rencana_visit SET is_visited = 1, visit_date = '$visitDate' WHERE id_contact = '$id_contact' AND type_rencana = 'tagih_mingguan'");
                }
            }
            $this->session->set_flashdata('success', "Berhasil menambah visit!");
            redirect('manualvisit');
        } else {
            $this->session->set_flashdata('failed', "Gagal menambah visit!");
            redirect('manualvisit');
        }
    }
}
