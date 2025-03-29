<?php

class Topevent extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
    }

    public function index()
    {
        $daterange = $this->input->post('daterange');

        $data['title'] = 'Peserta Event Lomba';

        if ($daterange) {
            $dates = explode("-", $daterange);
            $dateFrom = date("Y-m-d", strtotime($dates[0]));
            $dateTo = date("Y-m-d", strtotime($dates[1]));

            $data['topevents'] = $this->db->get_where('tb_topevent', ['DATE(created_at) >=' => $dateFrom, 'DATE(created_at) <=' => $dateTo])->result_array();
        } else {
            $date = date("Y-m-d");
            $data['topevents'] = $this->db->get_where('tb_topevent', ['DATE(created_at)' => $date])->result_array();
        }

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Topevent/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }
}
