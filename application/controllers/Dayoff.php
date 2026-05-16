<?php

class Dayoff extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Target Visit';
        $data['menuGroup'] = 'Sales';
        $data['menu'] = 'TargetVisit';

        $this->db->join('tb_city', 'tb_city.id_city = tb_user.id_city');
        $data['users'] = $this->db->get_where('tb_user', ['phone_user !=' => 0, 'level_user' => 'sales'])->result_array();

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Dayoff/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function create()
    {
        $post = $this->input->post();

        $dayoffData = [
            'id_user' => $post['id_user'],
            'date_day_off' => $post['date_day_off'],
            'desc_day_off' => $post['desc_day_off'],
        ];

        $save = $this->db->insert('tb_day_off', $dayoffData);

        if ($save) {
            $this->session->set_flashdata('success', 'Berhasil menambah data day off');
            redirect('dayoff');
        } else {
            $this->session->set_flashdata('failed', 'Gagal menambah data day off');
            redirect('dayoff');
        }
    }
}
