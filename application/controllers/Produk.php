<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Produk extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $this->load->model('MCity');
        $this->load->model('MProduk');
        $this->load->model('MSatuan');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['title'] = 'Produk';
        if ($this->session->userdata('level_user') == 'admin_c') {
            $data['city'] = $this->db->get_where('tb_city', ['id_city' => $this->session->userdata('id_city')])->result_array();
        } else {
            $data['city'] = $this->MCity->getAll();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Produk/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function produk_by_city($id_city)
    {
        $data['title'] = 'Produk';
        $data['cities'] = $this->MCity->getAll();
        $data['city'] = $this->MCity->getById($id_city);
        $data['produk'] = $this->MProduk->getByCity($id_city);
        $data['satuans'] = $this->MSatuan->get();
        $data['masterproduks'] = $this->db->get_where('tb_master_produk', ['id_distributor' => $this->session->userdata('id_distributor')])->result_array();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Produk/List');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function stok_by_produk($id_city, $id_produk)
    {
        $data['title'] = 'Stok Produk';
        $data['cities'] = $this->MCity->getAll();
        $data['city'] = $this->MCity->getById($id_city);
        $data['produk'] = $this->MProduk->getById($id_produk);
        $data['stok'] = $this->db->get_where('tb_stok', ['id_produk' => $id_produk])->result_array();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Produk/StokList');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function insert()
    {
        $this->form_validation->set_rules('harga_produk', 'Harga Produk', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap lengkapi form");
            redirect('produk');
        } else {
            $insert = $this->MProduk->insert();

            if ($insert) {
                $this->session->set_flashdata('success', "Berhasil menyimpan data produk!");
                redirect('produk');
            } else {
                $this->session->set_flashdata('failed', "Gagal menyimpan data produk!");
                redirect('produk');
            }
        }
    }

    public function insert_stok($id)
    {
        $this->form_validation->set_rules('jml_stok', 'Jumlah', 'required');

        $post = $this->input->post();

        $id_city = $post['id_city'];

        $id_produk = $post['id_produk'];

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap lengkapi form");
            redirect('produk/' . $id_city . "/" . $id_produk);
        } else {
            $insert = $this->db->insert('tb_stok', ['id_produk' => $id, 'jml_stok' => $this->input->post('jml_stok')]);

            if ($insert) {
                $this->session->set_flashdata('success', "Berhasil menambah stok produk!");
                redirect('produk/' . $id_city . "/" . $id_produk);
            } else {
                $this->session->set_flashdata('failed', "Gagal menambah stok produk!");
                redirect('produk/' . $id_city . "/" . $id_produk);
            }
        }
    }

    public function move_stok($id)
    {
        $this->form_validation->set_rules('jml_stok', 'Jumlah', 'required');

        $post = $this->input->post();

        $id_city = $post['id_city'];
        $id_city_tujuan = $post['id_city_tujuan'];
        $jml_stok = $post['jml_stok'];

        $id_produk = $post['id_produk'];
        $nama_produk = $post['nama_produk'];

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap lengkapi form");
            redirect('produk/' . $id_city . "/" . $id_produk);
        } else {
            $insert = $this->db->insert('tb_stok', ['id_produk' => $id, 'jml_stok' => $jml_stok, 'status_stok' => 'out']);

            if ($insert) {
                $getProduk = $this->db->get_where('tb_produk', ['nama_produk' => $nama_produk, 'id_city' => $id_city_tujuan])->row_array();
                $id_produk = $getProduk['id_produk'];

                $insert = $this->db->insert('tb_stok', ['id_produk' => $id_produk, 'jml_stok' => $jml_stok, 'status_stok' => 'in']);

                if ($insert) {
                    $this->session->set_flashdata('success', "Berhasil menambah stok produk!");
                    redirect('produk/' . $id_city . "/" . $id_produk);
                } else {
                    $this->session->set_flashdata('failed', "Gagal menambah stok produk!");
                    redirect('produk/' . $id_city . "/" . $id_produk);
                }
            } else {
                $this->session->set_flashdata('failed', "Gagal menambah stok produk!");
                redirect('produk/' . $id_city . "/" . $id_produk);
            }
        }
    }

    public function delete_stok($id_city, $id_produk, $id_stok)
    {
        $delete = $this->db->delete('tb_stok', ['id_stok' => $id_stok]);

        if ($delete) {
            $this->session->set_flashdata('success', "Berhasil menghapus stok produk!");
            redirect('produk/' . $id_city . "/" . $id_produk);
        } else {
            $this->session->set_flashdata('failed', "Gagal menghapus stok produk!");
            redirect('produk/' . $id_city . "/" . $id_produk);
        }
    }

    public function update($id)
    {
        $this->form_validation->set_rules('harga_produk', 'Harga Produk', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap lengkapi form");
            redirect('produk');
        } else {
            $insert = $this->MProduk->update($id);

            if ($insert) {
                $this->session->set_flashdata('success', "Berhasil mengubah data produk!");
                redirect('produk');
            } else {
                $this->session->set_flashdata('failed', "Gagal mengubah data produk!");
                redirect('produk');
            }
        }
    }
}
