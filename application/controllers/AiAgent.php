<?php

class AiAgent extends CI_Controller
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
        $this->load->model('MAiagent');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['title'] = 'AI Visit Report';
        $data['menuGroup'] = 'AiIntegration';
        $data['menu'] = 'AiAgent';

        $data['aiAgents'] = $this->MAiagent->getByIdDistributor($this->session->userdata('id_distributor'));

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('AiAgent/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function detail($id_ai_reportvisit)
    {
        $aiAgent = $this->MAiagent->getById($id_ai_reportvisit);
        $data['title'] = 'Setting of ' . $aiAgent['name_ai_agent'] . " Agent";
        $data['menuGroup'] = 'AiIntegration';
        $data['menu'] = 'AiAgent';

        $data['aiAgent'] = $aiAgent;
        $data['user'] = $this->MUser->getById($aiAgent['user_updated']);

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('AiAgent/Detail');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }
}
