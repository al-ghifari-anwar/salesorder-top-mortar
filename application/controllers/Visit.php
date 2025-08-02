<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Visit extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('MCity');
        $this->load->model('MProduk');
        $this->load->model('MSuratJalan');
        $this->load->model('MDetailSuratJalan');
        $this->load->model('MInvoice');
        $this->load->model('MContact');
        $this->load->model('MKendaraan');
        $this->load->model('MUser');
        $this->load->model('MVisit');
        $this->load->model('MProyek');
        $this->load->library('form_validation');
        // $this->load->library('phpqrcode/qrlib');
    }

    public function index()
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Visit';
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Visit/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function visit_by_city($id_city)
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }

        $dateRange = $this->input->post("date_range");
        $id_user = $this->input->post("id_user");
        $bulan = $this->input->post("bulan");

        if ($bulan) {
            $dates = explode("-", $dateRange);
            $data['visit'] = $this->MVisit->getByCityAndDate($id_city, $id_user, $bulan);
        } else {
            // $invoice = $this->MInvoice->getAll();
            $data['visit'] = $this->MVisit->getAllByCity($id_city);
        }
        $data['proyeks'] = $this->MProyek->getAll();
        $data['title'] = 'Visit';
        $data['cities'] = $this->MCity->getAll();
        $data['city'] = $this->MCity->getById($id_city);
        $data['id_city'] = $id_city;
        // echo json_encode($data);
        // die;
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Visit/List');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function approve($id, $id_city)
    {
        $approve = $this->MVisit->approve($id);

        $getVisit = $this->db->get_where('tb_visit', ['id_visit' => $id])->row_array();
        $id_contact = $getVisit['id_contact'];

        if ($approve) {
            $post = $this->input->post();
            if (isset($post['id_proyek'])) {
                $id_proyek = $post['id_proyek'];
                $this->db->update('tb_contact', ['id_proyek' => $id_proyek], ['id_contact' => $id_contact]);
            }
            $this->session->set_flashdata('success', "Berhasil approve visit!");
            redirect('visit/' . $id_city);
        } else {
            $this->session->set_flashdata('failed', "Gagal approve visit!");
            redirect('visit/' . $id_city);
        }
    }

    public function approve2($id, $id_city)
    {
        $post = $this->input->post();

        $approveData = [
            'is_approved_2' => 1,
            'approve_message_2' => $post['approve_message_2'],
        ];

        $approve = $this->db->update('tb_visit', $approveData, ['id_visit' => $id]);

        $getVisit = $this->db->get_where('tb_visit', ['id_visit' => $id])->row_array();
        $id_contact = $getVisit['id_contact'];

        if ($approve) {
            $post = $this->input->post();
            if (isset($post['id_proyek'])) {
                $id_proyek = $post['id_proyek'];
                $this->db->update('tb_contact', ['id_proyek' => $id_proyek], ['id_contact' => $id_contact]);
            }
            $this->session->set_flashdata('success', "Berhasil approve visit!");
            redirect('visit/' . $id_city);
        } else {
            $this->session->set_flashdata('failed', "Gagal approve visit!");
            redirect('visit/' . $id_city);
        }
    }

    public function lapkurir_city_list()
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Visit';
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Lapkurir/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function lapkurir_by_city($id_city)
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }

        $dateRange = $this->input->post("date_range");
        $id_user = $this->input->post("id_user");

        if ($dateRange) {
            $dates = explode("-", $dateRange);
            $data['visit'] = $this->MVisit->getByCityAndDateCourier($id_city, date('Y-m-d H:i:s', strtotime($dates[0] . " 00:00:00")), date('Y-m-d H:i:s', strtotime($dates[1] . " 23:59:59")), $id_user);
        } else {
            // $invoice = $this->MInvoice->getAll();
            $data['visit'] = $this->MVisit->getKurirByCity($id_city);
        }
        $data['title'] = 'Visit';
        $data['cities'] = $this->MCity->getAll();
        $data['city'] = $this->MCity->getById($id_city);
        $data['id_city'] = $id_city;
        // echo json_encode($data);
        // die;
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Lapkurir/List');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function lap_absen($id_city, $type)
    {
        $post = $this->input->post();
        $month = $post['bulan'];

        $data['city'] = $this->MCity->getById($id_city);
        $data['month'] = $month;
        $data['type'] = $type;
        // PDF
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        if ($type == 'courier') {
            $data['user'] = $this->MVisit->getGroupedCourierGlobal($id_city, $month, $type);
            $html = $this->load->view('Visit/Print', $data, true);
        } else if ($type == 'sales') {
            $data['user'] = $this->MVisit->getGroupedContactGlobal($id_city, $month, $type);
            $html = $this->load->view('Visit/PrintSales', $data, true);
        }
        $mpdf->AddPage('L');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    public function rencana_visit()
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Visit';
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('RencanaVisit/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function rencana_visit_by_city($id_city)
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }

        $dateRange = $this->input->post("date_range");
        $id_user = $this->input->post("id_user");
        $bulan = $this->input->post("bulan");

        if ($dateRange) {
            $dates = explode("-", $dateRange);
            $dateFrom = date('Y-m-d', $dates[0]);
            $dateTo = date('Y-m-d', $dates[1]);
            $data['visit'] = $this->MVisit->getByCityAndDate($id_city, $id_user, $dateFrom, $dateTo);
        } else {
            // $invoice = $this->MInvoice->getAll();
            $data['visit'] = $this->MVisit->getAllByCity($id_city);
        }
        $data['title'] = 'Visit';
        $data['cities'] = $this->MCity->getAll();
        $data['city'] = $this->MCity->getById($id_city);
        $data['id_city'] = $id_city;
        // echo json_encode($data);
        // die;
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('RencanaVisit/List');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function lap_absen_renvis($id_city, $type)
    {
        $post = $this->input->post();
        // $month = $post['bulan'];

        $dateRange = $post['date_range'];

        $dates = explode('-', $dateRange);
        $dateFrom = date("Y-m-d", strtotime($dates[0]));
        $dateTo = date("Y-m-d", strtotime($dates[1]));

        $data['city'] = $this->MCity->getById($id_city);
        $data['user'] = $this->MVisit->getGroupedContactGlobal($id_city, $type, $dateFrom, $dateTo);
        $data['dateFrom'] = $dateFrom;
        $data['dateTo'] = $dateTo;
        $data['type'] = $type;
        // PDF
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        if ($type == 'courier') {
            $html = $this->load->view('Visit/Print', $data, true);
        } else if ($type == 'sales') {
            $html = $this->load->view('RencanaVisit/PrintSales', $data, true);
        }
        $mpdf->AddPage('L');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }
}
