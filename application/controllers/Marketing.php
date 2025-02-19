<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Marketing extends CI_Controller
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
        $this->load->model('MVoucher');
        $this->load->model("MMarketingMessage");
        $this->load->library('form_validation');
    }

    public function index()
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Marketing';
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Marketing/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function tukang()
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Blast Konten Tukang';
        $data['kontens'] = $this->MMarketingMessage->getTukang();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Marketing/Tukang');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function rekap_tukang()
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $daterange = $this->input->post('daterange');
        $data['title'] = 'Rekap Blast Konten Tukang';

        $dateFrom = date("Y-m-d");
        $dateTo = date("Y-m-d");
        $this->db->order_by('created_at', 'DESC');
        $watzapTukangs = $this->db->get_where('tb_watzap_tukang', ['DATE(created_at)' => $dateFrom])->result_array();

        if ($daterange != null) {
            $dates = explode('-', $daterange);
            $dateFrom = date("Y-m-d", strtotime($dates[0]));
            $dateTo = date("Y-m-d", strtotime($dates[1]));

            $this->db->order_by('created_at', 'DESC');
            $watzapTukangs = $this->db->get_where('tb_watzap_tukang', ['DATE(created_at) >=' => $dateFrom, 'DATE(created_at) <=' => $dateTo])->result_array();
        }

        $data['watzap_tukangs'] = $watzapTukangs;
        $data['dateFrom'] = $dateFrom;
        $data['dateTo'] = $dateTo;

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Marketing/RekapTukang');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function insert()
    {
        $type = $this->input->post('target_marketing_message');
        $this->form_validation->set_rules('nama_marketing_message', 'Nama Konten', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap isi form dengan lengkap");
            redirect('marketing/' . $type);
        } else {
            $insert = $this->MMarketingMessage->insert();

            if ($insert) {
                $this->session->set_flashdata('success', "Berhasil membuat konten");
                redirect('marketing/' . $type);
            } else {
                $this->session->set_flashdata('failed', "Gagal membuat konten");
                redirect('marketing/' . $type);
            }
        }
    }

    public function update($id)
    {
        $type = $this->input->post('target_marketing_message');
        $this->form_validation->set_rules('nama_marketing_message', 'Nama Konten', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap isi form dengan lengkap");
            redirect('marketing/' . $type);
        } else {
            $insert = $this->MMarketingMessage->update($id);

            if ($insert) {
                $this->session->set_flashdata('success', "Berhasil mengubah konten");
                redirect('marketing/' . $type);
            } else {
                $this->session->set_flashdata('failed', "Gagal mengubah konten");
                redirect('marketing/' . $type);
            }
        }
    }

    public function delete($id, $type)
    {
        $delete = $this->MMarketingMessage->delete($id);
        if ($delete) {
            $this->session->set_flashdata('success', "Berhasil menghapus konten");
            redirect('marketing/' . $type);
        } else {
            $this->session->set_flashdata('failed', "Gagal menghapus konten");
            redirect('marketing/' . $type);
        }
    }
}
