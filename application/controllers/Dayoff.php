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
}
