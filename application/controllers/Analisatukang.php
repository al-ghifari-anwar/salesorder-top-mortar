<?php


class Analisatukang extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
    }

    public function user()
    {
        $data['title'] = 'Admin Lapangan / SPG untuk Tukang';
        $data['menuGroup'] = 'AnalisaTukang';
        $data['menu'] = 'SPG';
        $userCity = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->row_array();

        $this->db->join('tb_city', 'tb_city.id_city = tb_user.id_city');
        if ($this->session->userdata('level_user') == 'salesspv') {
            $nama_city = trim(preg_replace("/\\d+/", "", $userCity['nama_city']));
            $this->db->like('nama_city', $nama_city);
        }
        $this->db->where("level_user IN ('penagihan', 'sales', 'marketing')", NULL, FALSE);
        $data['users'] = $this->db->get_where('tb_user', ['password !=' => '0'])->result_array();

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Analisatukang/User');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function tampil($id_user)
    {
        $save = $this->db->update('tb_user', ['is_add_tukang' => 1], ['id_user' => $id_user]);

        if ($save) {
            $this->session->set_flashdata('success', "Berhasil merubah data user!");
            redirect('analisatukang/user');
        } else {
            $this->session->set_flashdata('failed', "Gagal merubah data user!");
            redirect('analisatukang/user');
        }
    }

    public function matikan($id_user)
    {
        $save = $this->db->update('tb_user', ['is_add_tukang' => 0], ['id_user' => $id_user]);

        if ($save) {
            $this->session->set_flashdata('success', "Berhasil merubah data user!");
            redirect('analisatukang/user');
        } else {
            $this->session->set_flashdata('failed', "Gagal merubah data user!");
            redirect('analisatukang/user');
        }
    }

    public function laporan()
    {
        $data['title'] = 'Rekap Target Tukang';
        $data['menuGroup'] = 'AnalisaTukang';
        $data['menu'] = 'Rekap';
        $this->db->where("level_user IN ('penagihan', 'sales', 'marketing')", NULL, FALSE);
        $data['users'] = $this->db->get_where('tb_user', ['password !=' => '0', 'is_add_tukang' => 1])->result_array();

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Analisatukang/Laporan');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function laporan_print()
    {
        $post = $this->input->post();

        $id_user = $post['id_user'];
        $daterange = $post['daterange'];
        $dates = explode('-', $daterange);

        if ($id_user > 0) {
            $this->db->where('id_user', $id_user);
        }
        $user = $this->db->get_where('tb_user', ['password !=' => '0', 'is_add_tukang' => 1])->result_array();

        $data['title'] = 'Rekap Target Tukang';
        $data['users'] = $user;
        $data['dateFrom'] = $dates[0];
        $data['dateTo'] = $dates[1];

        // Test
        // $this->load->view('Analisatukang/Print', $data);
        // PDF
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Analisatukang/Print', $data, true);
        $mpdf->AddPage('L');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }
}
