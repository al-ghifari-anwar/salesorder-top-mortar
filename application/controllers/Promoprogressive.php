<?php

class Promoprogressive extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['title'] = 'Promo Progressive';
        $data['menuGroup'] = 'Data';
        $data['menu'] = 'PromoProgressive';

        $this->db->where('deleted_at IS NULL', null, false);
        $data['promos'] = $this->db->get_where('tb_promo_progressive', ['id_distributor' => $this->session->userdata('id_distributor')])->result_array();
        echo $this->db->last_query();
        die;

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Promoprogressive/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function create()
    {
        $post = $this->input->post();

        $this->form_validation->set_rules('name_promo_progressive', 'Nama Promo', 'required');
        $this->form_validation->set_rules('kelipatan_promo_progressive', 'Kelipatan', 'required|numeric');
        $this->form_validation->set_rules('bonus_promo_progressive', 'Bonus', 'required|numeric');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap lengkapi form");
            redirect('promoprogressive');
        } else {
            $promoData = [
                'id_distributor' => $this->session->userdata('id_distributor'),
                'name_promo_progressive' => $post['name_promo_progressive'],
                'kelipatan_promo_progressive' => $post['kelipatan_promo_progressive'],
                'bonus_promo_progressive' => $post['bonus_promo_progressive'],
            ];

            $save = $this->db->insert('tb_promo_progressive', $promoData);

            if ($save) {
                $this->session->set_flashdata('success', "Berhasil menyimpan data promo!");
                redirect('promoprogressive');
            } else {
                $this->session->set_flashdata('failed', "Gagal menyimpan data promo!");
                redirect('promoprogressive');
            }
        }
    }

    public function update($id_promo_progressive)
    {
        $post = $this->input->post();

        $this->form_validation->set_rules('name_promo_progressive', 'Nama Promo', 'required');
        $this->form_validation->set_rules('kelipatan_promo_progressive', 'Kelipatan', 'required|numeric');
        $this->form_validation->set_rules('bonus_promo_progressive', 'Bonus', 'required|numeric');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap lengkapi form");
            redirect('promoprogressive');
        } else {
            $promoData = [
                'id_distributor' => $this->session->userdata('id_distributor'),
                'name_promo_progressive' => $post['name_promo_progressive'],
                'kelipatan_promo_progressive' => $post['kelipatan_promo_progressive'],
                'bonus_promo_progressive' => $post['bonus_promo_progressive'],
            ];

            $save = $this->db->update('tb_promo_progressive', $promoData, ['id_promo_progressive' => $id_promo_progressive]);

            if ($save) {
                $this->session->set_flashdata('success', "Berhasil menyimpan data promo!");
                redirect('promoprogressive');
            } else {
                $this->session->set_flashdata('failed', "Gagal menyimpan data promo!");
                redirect('promoprogressive');
            }
        }
    }

    public function delete($id_promo_progressive)
    {
        $promoData = [
            'deleted_at' => date('Y-m-d H:i:s'),
        ];

        $save = $this->db->update('tb_promo_progressive', $promoData, ['id_promo_progressive' => $id_promo_progressive]);

        if ($save) {
            $this->session->set_flashdata('success', "Berhasil menghapus data promo!");
            redirect('promoprogressive');
        } else {
            $this->session->set_flashdata('failed', "Gagal menghapus data promo!");
            redirect('promoprogressive');
        }
    }
}
