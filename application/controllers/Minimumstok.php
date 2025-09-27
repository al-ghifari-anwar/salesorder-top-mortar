<?php


class Minimumstok extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $this->load->library('form_validation');
    }

    public function index($id_gudang_stok)
    {
        $gudang = $this->db->get_where('tb_gudang_stok', ['id_gudang_stok' => $id_gudang_stok])->row_array();

        $data['title'] = 'Stok Minimal ' . $gudang['name_gudang_stok'];
        $data['menuGroup'] = 'Data';
        $data['menu'] = 'Gudang';
        $data['gudang'] = $gudang;

        $this->db->join('tb_master_produk', 'tb_master_produk.id_master_produk = tb_minimum_stok.id_master_produk');
        $data['minimums'] = $this->db->get_where('tb_minimum_stok', ['id_gudang_stok' => $id_gudang_stok])->result_array();

        $data['produks'] = $this->db->get_where('tb_master_produk', ['id_distributor' => $this->session->userdata('id_distributor')])->result_array();

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Minimumstok/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function create()
    {
        $post = $this->input->post();

        $id_gudang_stok = $post['id_gudang_stok'];
        $id_master_produk = $post['id_master_produk'];

        if ($post['minimum_stok'] == 0) {
            $this->session->set_flashdata('failed', "Stok minimal tidak boleh 0!");
            return redirect('minimumstok/' . $id_gudang_stok);
        }


        $cekMinimum = $this->db->get_where('tb_minimum_stok', ['id_gudang_stok' => $id_gudang_stok, 'id_master_produk' => $id_master_produk])->row_array();

        if ($cekMinimum != null) {
            $this->session->set_flashdata('failed', "Stok minimal untuk produk tersebut sudah ada. Silahkan update untuk merubahnya");
            return redirect('minimumstok/' . $id_gudang_stok);
        }

        $minimumStokData = [
            'id_gudang_stok' => $id_gudang_stok,
            'id_master_produk' => $id_master_produk,
            'minimum_stok' => $post['minimum_stok'],
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $save = $this->db->insert('tb_minimum_stok', $minimumStokData);

        if ($save) {
            $this->session->set_flashdata('success', "Berhasil menyimpan data");
            return redirect('minimumstok/' . $id_gudang_stok);
        } else {
            $this->session->set_flashdata('failed', "Gagal menyimpan data");
            return redirect('minimumstok/' . $id_gudang_stok);
        }
    }

    public function update()
    {
        $post = $this->input->post();

        $id_minimum_stok = $post['id_minimum_stok'];
        $id_gudang_stok = $post['id_gudang_stok'];
        $old_id_master_produk = $post['old_id_master_produk'];
        $id_master_produk = $post['id_master_produk'];

        if ($post['minimum_stok'] == 0) {
            $this->session->set_flashdata('failed', "Stok minimal tidak boleh 0!");
            return redirect('minimumstok/' . $id_gudang_stok);
        }


        if ($id_master_produk != $old_id_master_produk) {
            $cekMinimum = $this->db->get_where('tb_minimum_stok', ['id_gudang_stok' => $id_gudang_stok, 'id_master_produk' => $id_master_produk])->row_array();

            if ($cekMinimum != null) {
                $this->session->set_flashdata('failed', "Stok minimal untuk produk tersebut sudah ada. Silahkan update untuk merubahnya");
                return redirect('minimumstok/' . $id_gudang_stok);
            }
        }


        $minimumStokData = [
            'id_gudang_stok' => $id_gudang_stok,
            'id_master_produk' => $id_master_produk,
            'minimum_stok' => $post['minimum_stok'],
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $save = $this->db->update('tb_minimum_stok', $minimumStokData, ['id_minimum_stok' => $id_minimum_stok]);

        if ($save) {
            $this->session->set_flashdata('success', "Berhasil menyimpan data");
            return redirect('minimumstok/' . $id_gudang_stok);
        } else {
            $this->session->set_flashdata('failed', "Gagal menyimpan data");
            return redirect('minimumstok/' . $id_gudang_stok);
        }
    }

    public function delete($id_minimum_stok)
    {
        $miminumStok = $this->db->get_where('tb_minimum_stok', ['id_minimum_stok' => $id_minimum_stok])->row_array();
        $id_gudang_stok = $miminumStok['id_gudang_stok'];

        $delete = $this->db->delete('tb_minimum_stok', ['id_minimum_stok' => $id_minimum_stok]);

        if ($delete) {
            $this->session->set_flashdata('success', "Berhasil menyimpan data");
            return redirect('minimumstok/' . $id_gudang_stok);
        } else {
            $this->session->set_flashdata('failed', "Gagal menyimpan data");
            return redirect('minimumstok/' . $id_gudang_stok);
        }
    }
}
