<?php

class HaloaiReportVisit extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->output->set_content_type('application/json');
        $this->load->model('MContact');
        $this->load->model('MUser');
        $this->load->model('MSuratJalan');
        $this->load->model('MDetailSuratJalan');
        $this->load->model('MInvoice');
        $this->load->model('MPayment');
        $this->load->model('MVoucher');
        $this->load->model('HTelegram');
    }

    public function process()
    {
        $post = json_decode(file_get_contents('php://input'), true) != null ? json_decode(file_get_contents('php://input'), true) : $this->input->post();

        $nomorhp = $post['nomorhp'];

        // Contact
        $contact = $this->MContact->getByNomorhp($nomorhp);
        $id_contact = $contact['id_contact'];

        // Last Visit
        $lastVisit = $this->db->get_where('tb_visit', ['id_contact' => $id_contact, 'DATE(date_visit)' => date('Y-m-d')])->row_array();

        $id_visit = $lastVisit['id_visit'];

        $approveVisit = $this->db->update('tb_visit', ['is_approved' => 1], ['id_visit' => $id_visit]);

        $result = [
            'code' => 200,
            'status' => 'ok',
            'msg' => 'Auto approve is done',
        ];

        return $this->output->set_output(json_encode($result));
    }
}
