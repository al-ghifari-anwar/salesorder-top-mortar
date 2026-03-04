<?php

class AiVisitReport extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $this->load->model('MCity');
        $this->load->model('MContact');
        $this->load->model('MVisit');
        $this->load->model('MUser');
        $this->load->model('MAivisitreport');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $dateFrom = date('Y-m-d');
        $dateTo = date("Y-m-d");

        if (isset($_GET['daterange'])) {
            $daterange = $_GET['daterange'];
            $dates = explode('-', $daterange);
            $dateFrom = date("Y-m-d", strtotime($dates[0]));
            $dateTo = date("Y-m-d", strtotime($dates[1]));
        }

        $data['title'] = 'AI Visit Report';
        $data['menuGroup'] = 'AiIntegration';
        $data['menu'] = 'AiVisitReport';

        $data['dateFrom'] = $dateFrom;
        $data['dateTo'] = $dateTo;
        $data['aiVisitReports'] = $this->MAivisitreport->getByDateRange($dateFrom, $dateTo);

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('AiVisitReport/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function detail($id_ai_reportvisit)
    {
        $aiVisitReport = $this->MAivisitreport->getById($id_ai_reportvisit);
        $data['title'] = 'AI Visit Report';
        $data['menuGroup'] = 'AiIntegration';
        $data['menu'] = 'AiVisitReport';

        $data['aiVisitReport'] = $aiVisitReport;
        $data['contact'] = $this->MContact->getById($aiVisitReport['id_contact']);
        $data['user'] = $this->MUser->getById($aiVisitReport['id_user']);
        $id_visit = $aiVisitReport['id_visit'];
        $data['visit'] = $this->db->get_where('tb_visit', ['id_visit' => $id_visit])->row_array();

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('AiVisitReport/Detail');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }
}
