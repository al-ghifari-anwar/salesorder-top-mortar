<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Masterproduk extends CI_Controller
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
        $data['title'] = 'Master Produk';
        $data['masterproduks'] = $this->db->get_where('tb_master_produk', ['id_distributor' => $this->session->userdata('id_distributor')])->result_array();
        $data['satuans'] = $this->db->get('tb_satuan')->result_array();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Masterproduk/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function create()
    {
        $this->form_validation->set_rules('name_master_produk', 'Nama', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap lengkapi form");
            redirect('masterproduk');
        } else {
            $post = $this->input->post();

            $img_master_produk = '-';

            $uploadImg = $this->uploadImage($post['name_master_produk'] . '-' . time());

            if ($uploadImg['status'] == 'success') {
                $uploadData = $uploadImg['message'];
                $img_master_produk = base_url('assets/img/produk_img/') . $uploadData['file_name'];
            }

            $dataMasterproduk = [
                'id_satuan' => $post['id_satuan'],
                'name_master_produk' => $post['name_master_produk'],
                'img_master_produk' => $img_master_produk,
                'id_distributor' => $this->session->userdata('id_distributor'),
                'updated_at' => date("Y-m-d H:i:s")
            ];

            $insert = $this->db->insert('tb_master_produk', $dataMasterproduk);

            if ($insert) {
                $this->session->set_flashdata('success', "Berhasil menyimpan data masterproduk!");
                redirect('masterproduk');
            } else {
                $this->session->set_flashdata('failed', "Gagal menyimpan data masterproduk!");
                redirect('masterproduk');
            }
        }
    }

    public function update($id)
    {
        $this->form_validation->set_rules('name_master_produk', 'Nama', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap lengkapi form");
            redirect('masterproduk');
        } else {
            $post = $this->input->post();

            $masterProduk = $this->db->get_where('tb_master_produk', ['id_master_produk' => $id])->row_array();

            $img_master_produk = $masterProduk['img_master_produk'];

            $uploadImg = $this->uploadImage($post['name_master_produk'] . '-' . time());

            if ($uploadImg['status'] == 'success') {
                $uploadData = $uploadImg['message'];
                $img_master_produk = base_url('assets/img/produk_img/') . $uploadData['file_name'];
            }

            $dataMasterproduk = [
                'id_satuan' => $post['id_satuan'],
                'name_master_produk' => $post['name_master_produk'],
                'img_master_produk' => $img_master_produk,
                'id_distributor' => $this->session->userdata('id_distributor'),
                'updated_at' => date("Y-m-d H:i:s")
            ];

            $insert = $this->db->update('tb_master_produk', $dataMasterproduk, ['id_master_produk' => $id]);

            if ($insert) {
                $this->session->set_flashdata('success', "Berhasil mengubah data masterproduk!");
                redirect('masterproduk');
            } else {
                $this->session->set_flashdata('failed', "Gagal mengubah data masterproduk!");
                redirect('masterproduk');
            }
        }
    }

    public function delete($id)
    {
        // $insert = $this->db->delete('tb_master_produk', ['id_master_produk' => $id]);

        $insert = false;

        if ($insert) {
            $this->session->set_flashdata('success', "Berhasil menghapus data masterproduk!");
            redirect('masterproduk');
        } else {
            $this->session->set_flashdata('failed', "Gagal menghapus data masterproduk!");
            redirect('masterproduk');
        }
    }

    public function sync()
    {
        $this->db->group_by('nama_produk');
        $produks = $this->db->get('tb_produk')->result_array();

        foreach ($produks as $produk) {
            $nama_produk = $produk['nama_produk'];

            $masterProduk = $this->db->get_where('tb_master_produk', ['name_master_produk' => $nama_produk])->row_array();

            if ($masterProduk == null) {
                $id_city = $produk['id_city'];
                $city = $this->db->get_where('tb_city', ['id_city' => $id_city])->row_array();

                if ($city != null) {

                    $dataMaster = [
                        'id_distributor' => $city['id_distributor'],
                        'id_satuan' => $produk['id_satuan'],
                        'name_master_produk' => $produk['nama_produk'],
                        'updated_at' => date("Y-m-d H:i:s")
                    ];

                    $insertMaster = $this->db->insert('tb_master_produk', $dataMaster);

                    if ($insertMaster) {
                        $id_master_produk = $this->db->insert_id();

                        $dataProduk = [
                            'id_master_produk' => $id_master_produk
                        ];

                        $updateProduk = $this->db->update('tb_produk', $dataProduk, ['nama_produk' => $nama_produk]);
                    }
                }
            } else {
                $id_master_produk = $masterProduk['id_master_produk'];

                $dataProduk = [
                    'id_master_produk' => $id_master_produk
                ];

                $updateProduk = $this->db->update('tb_produk', $dataProduk, ['nama_produk' => $nama_produk]);
            }
        }

        $this->session->set_flashdata('success', "Berhasil sync data master produk!");
        redirect('masterproduk');
    }

    public function uploadImage($nama)
    {
        $file_name = str_replace(' ', '-', $nama);
        $config['upload_path']          = FCPATH . '/assets/img/produk_img/';
        $config['allowed_types']        = 'gif|jpg|jpeg|png';
        $config['file_name']            = $file_name;
        $config['overwrite']            = true;
        $config['max_size']             = 12000; //10MB

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('img_master_produk')) {
            $data['error'] = $this->upload->display_errors();

            return ['status' => 'error', 'message' => $this->upload->display_errors()];
        } else {
            $uploaded_data = $this->upload->data();

            return ['status' => 'success', 'message' => $uploaded_data];
        }
    }
}
