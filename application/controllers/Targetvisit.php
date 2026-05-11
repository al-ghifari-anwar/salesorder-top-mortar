<?php

class Targetvisit extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MCity');
        $this->load->model('MUser');
    }

    public function index()
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Target Visit';
        $data['menuGroup'] = 'Sales';
        $data['menu'] = 'TargetVisit';
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else if ($this->session->userdata('level_user') == 'salesspv') {
            $userCity = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->row_array();
            $nama_city = trim(preg_replace("/\\d+/", "", $userCity['nama_city']));
            $data['city'] = $this->db->like('nama_city', $nama_city)->get_where('tb_city', ['id_distributor' => $this->session->userdata('id_distributor')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Targetvisit/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function list($id_city)
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Target Visit';
        $data['menuGroup'] = 'Sales';
        $data['menu'] = 'TargetVisit';

        $data['city'] = $this->MCity->getById($id_city);
        $data['users'] = $this->db->get_where('tb_user', ['id_city' => $id_city, 'phone_user !=' => 0, 'level_user' => 'sales'])->result_array();

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Targetvisit/List');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function print()
    {
        $get = $this->input->get();

        $id_user = $get['id_user'];
        $daterange = explode('-', $get['daterange']);

        $dateFrom = date('Y-m-d', strtotime($daterange[0]));
        $dateTo = date('Y-m-d', strtotime($daterange[1]));

        $data['dateFrom'] = $dateFrom;
        $data['dateTo'] = $dateTo;
        $data['user'] = $this->MUser->getById($id_user);

        // Get Visit Group By
        $this->db->not_like('source_visit', 'absen');
        $this->db->join('tb_contact', 'tb_contact.id_contact = tb_visit.id_contact');
        $this->db->group_by('tb_visit.id_contact');
        $data['groupedVisits'] = $this->db->get_where('tb_visit', ['tb_visit.id_user' => $id_user, 'DATE(date_visit) >=' => $dateFrom, 'DATE(date_visit) <=' => $dateTo])->result_array();

        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Targetvisit/Print', $data, true);

        $mpdf->AddPage('L');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }
}
