<?php

class Hobi extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Hobi';
        $data['menuGroup'] = 'Data';
        $data['menu'] = 'Hobi';

        $data['hobis'] = $this->db->order_by('path_hobi', 'ASC')->get_where('tb_hobi')->result_array();

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Hobi/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function create()
    {
        $post = $this->input->post();

        $hobiData = [
            'name_hobi' => $post['name_hobi'],
            'id_parent_hobi' => $post['id_parent_hobi'],
        ];

        $save = $this->db->insert('tb_hobi', $hobiData);

        if ($save) {
            $id_hobi = $this->db->insert_id();

            $hobiData = [
                'path_hobi' => $post['path_hobi'] . '/' . $id_hobi,
            ];

            $save = $this->db->update('tb_hobi', $hobiData, ['id_hobi' => $id_hobi]);

            $this->session->set_flashdata('success', 'Berhasil menyimpan data');
            return redirect('hobi');
        } else {
            $this->session->set_flashdata('failed', 'Gagal menyimpan data');
            return redirect('hobi');
        }
    }

    public function update($id_hobi)
    {
        $post = $this->input->post();

        $hobiData = [
            'name_hobi' => $post['name_hobi'],
            'id_parent_hobi' => $post['id_parent_hobi'],
        ];

        $save = $this->db->update('tb_hobi', $hobiData, ['id_hobi' => $id_hobi]);

        if ($save) {
            $this->session->set_flashdata('success', 'Berhasil menyimpan data');
            return redirect('hobi');
        } else {
            $this->session->set_flashdata('failed', 'Gagal menyimpan data');
            return redirect('hobi');
        }
    }
}
