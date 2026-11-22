<?php

class AddAdvancedVisit extends CI_Controller
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
        $this->load->model('MRenvi');
        $this->load->model('MInvoice');
        $this->load->model('HTelegram');
        $this->load->library('form_validation');
    }

    public function index()
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Advanced Visit Tambahan';
        $data['menuGroup'] = 'Visit';
        $data['menu'] = 'AddAdvancedVisit';
        if ($this->session->userdata('level_user') == 'admin_c' || $this->session->userdata('level_user') == 'sales') {
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
        $this->load->view('AddAdvancedvisit/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function list($id_city)
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }

        $cluster = 0;
        $minDayCluster = 0;
        if (date('D') == 'Mon' || date('D') == 'Thu') {
            $cluster = 1;
        } else if (date('D') == 'Tue' || date('D') == 'Fri') {
            $cluster = 2;
        } else if (date('D') == 'Wed' || date('D') == 'Sat') {
            $cluster = 3;
        }

        if (date('D') == 'Mon') {
            $minDayCluster = 4;
            $dayName = 'senin';
        } else if (date('D') == 'Tue') {
            $minDayCluster = 4;
            $dayName = 'selasa';
        } else if (date('D') == 'Wed') {
            $minDayCluster = 4;
            $dayName = 'rabu';
        } else if (date('D') == 'Thu') {
            $minDayCluster = 3;
            $dayName = 'kamis';
        } else if (date('D') == 'Fri') {
            $minDayCluster = 3;
            $dayName = 'jumat';
        } else if (date('D') == 'Sat') {
            $minDayCluster = 3;
            $dayName = 'sabtu';
        }

        $data['cluster'] = $cluster;
        $data['dayName'] = $dayName;

        $data['title'] = 'Advanced Visit Tambahan';
        $data['menuGroup'] = 'Visit';
        $data['menu'] = 'AddJadwalVisit';
        $data['city'] = $this->db->get_where('tb_city', ['id_city' => $id_city])->row_array();
        $data['contacts'] = $this->db->where('id_city', $id_city)->where('cluster !=', 0)->get('tb_contact')->result_array();
        $data['jadwalVisitTambahans'] = $this->db->get_where('tb_step_renvi', ['date_step_renvi' => date('Y-m-d'), 'is_tambahan' => 1, 'id_city' => $id_city])->result_array();

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('AddAdvancedvisit/List');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function create()
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }

        $post = $this->input->post();

        $contact = $this->db->where('id_contact', $post['id_contact'])->get('tb_contact')->row_array();

        $id_contact = $contact['id_contact'];

        $id_renvi = 0;
        $is_active = 0;
        $source_step_renvi = $post['source_step_renvi'];

        // Last Visit
        // Last of All Type Visit
        $allTypLastVisit = $this->db->order_by('date_visit', 'DESC')->get_where('tb_visit', ['id_contact' => $id_contact])->row_array();
        $date1allVisit = new DateTime(date("Y-m-d"));
        $date2allVisit = new DateTime($allTypLastVisit['date_visit']);
        $daysAllVisit  = $date2allVisit->diff($date1allVisit)->format('%a');
        $operan = "";
        if ($date1allVisit < $date2allVisit) {
            $operan = "-";
        }
        $daysAllVisit = $operan . $daysAllVisit;

        // Number Step Renvi
        $number_step_renvi = 0;

        if ($post['source_step_renvi'] == 'data') {
            $number_step_renvi = 6;
        } else if ($post['source_step_renvi'] == 'active') {
            $number_step_renvi = 7;
        } else if ($post['source_step_renvi'] == 'passive') {
            $number_step_renvi = 5;
        } else if ($post['source_step_renvi'] == 'jatem1') {
            $number_step_renvi = 2;
            $is_active = 1;
        }

        // Get Renvi Jatem
        if ($post['source_step_renvi'] == 'jatem1') {
            $renvisJatem = $this->db->where('id_contact', $contact['id_contact'])->order_by('id_renvis_jatem', 'DESC')->where('is_visited', 0)->get('tb_renvis_jatem')->row_array();

            if ($renvisJatem) {
                $id_renvi = $renvisJatem['id_renvis_jatem'];
                $is_active = 1;
            } else {
                $source_step_renvi = $contact['store_status'];
                $is_active = 0;

                if ($contact['store_status'] == 'data') {
                    $number_step_renvi = 6;
                } else if ($contact['store_status'] == 'active') {
                    $number_step_renvi = 7;
                } else if ($contact['store_status'] == 'passive') {
                    $number_step_renvi = 5;
                }
            }
        }

        $stepRenviData = [
            'id_city' => $post['id_city'],
            'id_contact' => $post['id_contact'],
            'id_renvi' => $id_renvi,
            'id_visit' => 0,
            'cluster_step_renvi' => $contact['cluster'],
            'date_step_renvi' => date('Y-m-d'),
            'number_step_renvi' => $number_step_renvi,
            'source_step_renvi' => $source_step_renvi,
            'filter_step_renvi' => $source_step_renvi . "_tambahan",
            'is_new' => 0,
            'last_visit_step_renvi' => $allTypLastVisit['date_visit'],
            'days_step_renvi' => $date2allVisit,
            'days_jatem_step_renvi' => '-',
            'total_invoice' => 0,
            'is_active' => $is_active,
            'is_tambahan' => 1,
        ];

        $save = $this->db->insert('tb_step_renvi', $stepRenviData);

        if ($save) {
            $this->session->set_flashdata('success', "Berhasil tambah renvi");
            redirect('add-advancedvisit/' . $post['id_city']);
        } else {
            $this->session->set_flashdata('failed', "Terjadi kesalahan, harap coba lagi");
            redirect('add-advancedvisit/' . $post['id_city']);
        }
    }

    public function delete($id_step_renvi)
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }

        $jadwalvisit = $this->db->get_where('tb_step_renvi', ['id_step_renvi' => $id_step_renvi])->row_array();

        $save = $this->db->delete('tb_step_renvi', ['id_step_renvi' => $id_step_renvi]);

        if ($save) {
            $this->session->set_flashdata('success', "Berhasil hapus renvi");
            redirect('add-advancedvisit/' . $jadwalvisit['id_city']);
        } else {
            $this->session->set_flashdata('failed', "Terjadi kesalahan, harap coba lagi");
            redirect('add-advancedvisit/' . $jadwalvisit['id_city']);
        }
    }
}
