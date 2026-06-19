<?php

class Visitcontrol extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['title'] = 'Visit Control';
        $data['menuGroup'] = 'Visit';
        $data['menu'] = 'VisitControl';

        $date = date('Y-m-d');
        if ($this->input->get("date")) {
            $date = date('Y-m-d', strtotime($this->input->get("date")));
        }

        $data['date'] = $date;
        $data['visitcontrols'] = $this->db->join('tb_user', 'tb_user.id_user = tb_visit_control.id_user')->where('DATE(date_visit_control)', $date)->where('id_distributor', $this->session->userdata('id_distributor'))->order_by('date_visit_control', 'DESC')->get('tb_visit_control')->result_array();

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Visitcontrol/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }
}
