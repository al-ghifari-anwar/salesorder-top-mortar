<?php

class Rekaptokobaru extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $this->load->model('MCity');
        $this->load->model('MUser');
        $this->load->model('MContact');
    }

    public function index()
    {
        $data['title'] = 'Rekap Toko Baru';
        $data['city'] = $this->MCity->getAll();
        // echo json_encode($data);
        // die;
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Rekaptokobaru/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function list($id_city)
    {
        $data['title'] = 'Rekap Toko Baru';
        $data['city'] = $this->MCity->getById($id_city);
        $this->db->where_in('level_user', ['sales', 'penagihan', 'mg']);
        $data['users'] = $this->db->get_where('tb_user', ['id_city' => $id_city, 'phone_user !=' => '0'])->result_array();
        // echo json_encode($data);
        // die;
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Rekaptokobaru/List');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function print()
    {
        $post = $this->input->post();

        $dateRange = $post['date_range'];
        $id_city = $post['id_city'];
        $id_user = $post['id_user'];

        $dates = explode('-', $dateRange);
        $dateFrom = date("Y-m-d", strtotime($dates[0]));
        $dateTo = date("Y-m-d", strtotime($dates[1]));


        if ($id_user == 0) {
            $this->db->where_in('level_user', ['sales', 'penagihan', 'mg']);
            $data['users'] = $this->db->get_where('tb_user', ['id_city' => $id_city, 'phone_user !=' => '0'])->result_array();
        } else {
            $data['users'] = $this->MUser->getById($id_user);
        }

        $data['id_user'] = $id_user;
        $data['city'] = $this->MCity->getById($id_city);
        $data['contacts'] = $this->MContact->getByCityAndCreated($dateFrom, $dateTo, $id_city);
        $data['dateFrom'] = $dateFrom;
        $data['dateTo'] = $dateTo;
        // PDF
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Rekaptokobaru/Print', $data, true);

        $mpdf->AddPage('L');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }
}
