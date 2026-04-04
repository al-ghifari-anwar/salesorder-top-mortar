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
        $data['aiModels'] = $this->aiModels();

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('AiAgent/Detail');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function updateSetting()
    {
        $post = $this->input->post();

        $id_ai_agent = $post['id_ai_agent'];

        $this->form_validation->set_rules('name_ai_agent', 'Nama Agent', 'required');
        // $this->form_validation->set_rules('usage_ai_agent', 'Agent Usage', 'requierd');
        $this->form_validation->set_rules('temperature_ai_agent', 'Temperature', 'required|numeric');
        $this->form_validation->set_rules('max_output_token_ai_agent', 'Max Output Token', 'required|numeric');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', $this->form_validation->error_string());
            redirect('ai-agent/detail/' . $id_ai_agent);
        } else {
            $aiAgentData = [
                'name_ai_agent' => $post['name_ai_agent'],
                'model_ai_agent' => $post['model_ai_agent'],
                'temperature_ai_agent' => $post['temperature_ai_agent'],
                'max_output_token_ai_agent' => $post['max_output_token_ai_agent'],
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $save = $this->db->update('tb_ai_agent', $aiAgentData, ['id_ai_agent' => $id_ai_agent]);

            if ($save) {
                $this->session->set_flashdata('success', "Berhasil memperbarui AI Setting");
                redirect('ai-agent/detail/' . $id_ai_agent);
            } else {
                $this->session->set_flashdata('failed', "Gagal memperbarui AI Setting");
                redirect('ai-agent/detail/' . $id_ai_agent);
            }
        }
    }

    public function updatePrompt()
    {
        $post = $this->input->post();

        $id_ai_agent = $post['id_ai_agent'];

        $this->form_validation->set_rules('prompt_md', 'Prompt Markdown', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', $this->form_validation->error_string());
            redirect('ai-agent/detail/' . $id_ai_agent);
        } else {
            $aiAgentData = [
                'base64_prompt' => base64_encode($post['prompt_md']),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $save = $this->db->update('tb_ai_agent', $aiAgentData, ['id_ai_agent' => $id_ai_agent]);

            if ($save) {
                $this->session->set_flashdata('success', "Berhasil memperbarui AI Setting");
                redirect('ai-agent/detail/' . $id_ai_agent);
            } else {
                $this->session->set_flashdata('failed', "Gagal memperbarui AI Setting");
                redirect('ai-agent/detail/' . $id_ai_agent);
            }
        }
    }

    private function aiModels()
    {
        $aiModels = [
            [
                'name' => 'gpt-4o-mini',
                'input' => 0.15,
                'output' => 0.6,
            ],
            [
                'name' => 'gpt-4o',
                'input' => 2.5,
                'output' => 10,
            ],
            [
                'name' => 'gpt-4.1-nano',
                'input' => 0.1,
                'output' => 0.4,
            ],
            [
                'name' => 'gpt-4.1-mini',
                'input' => 0.4,
                'output' => 1.6,
            ],
            [
                'name' => 'gpt-4.1',
                'input' => 2,
                'output' => 8,
            ],
            [
                'name' => 'gpt-5-nano',
                'input' => 0.05,
                'output' => 0.4,
            ],
            [
                'name' => 'gpt-5-mini',
                'input' => 0.25,
                'output' => 2.0,
            ],
            [
                'name' => 'gpt-5',
                'input' => 1.25,
                'output' => 10.0,
            ],
            [
                'name' => 'gpt-5-pro',
                'input' => 15,
                'output' => 120,
            ],
            [
                'name' => 'gpt-5.1',
                'input' => 1.25,
                'output' => 10,
            ],
            [
                'name' => 'gpt-5.2',
                'input' => 1.75,
                'output' => 14,
            ],
        ];

        return $aiModels;
    }
}
