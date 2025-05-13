<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sjstok extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        date_default_timezone_set('Asia/Jakarta');
    }

    public function index()
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Tambah Stok';
        $data['gudangs'] = $this->db->get_where('tb_gudang_stok', ['id_distributor' => $this->session->userdata('id_distributor')])->result_array();
        $this->db->order_by('created_at', 'DESC');
        $data['sjstoks'] = $this->db->get_where('tb_sj_stok', ['id_distributor' => $this->session->userdata('id_distributor')])->result_array();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Sjstok/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function create()
    {
        $this->form_validation->set_rules('delivery_date', 'Tgl Pengiriman', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap lengkapi form");
            redirect('sjstok');
        } else {
            $post = $this->input->post();

            $dataSjstok = [
                'id_distributor' => $this->session->userdata('id_distributor'),
                'id_gudang_stok' => $post['id_gudang_stok'],
                'delivery_date' => date('Y-m-d H:i:s', strtotime($post['delivery_date'])),
                'updated_at' => date("Y-m-d H:i:s")
            ];

            $insert = $this->db->insert('tb_sj_stok', $dataSjstok);

            $id_sj_stok = $this->db->insert_id();

            if ($insert) {
                $this->session->set_flashdata('success', "Berhasil menyimpan data pengirman stok!");
                redirect('sjstok/' . $id_sj_stok);
            } else {
                $this->session->set_flashdata('failed', "Gagal menyimpan data pengiriman stok!");
                redirect('sjstok/' . $id_sj_stok);
            }
        }
    }

    public function detail($id_sj_stok)
    {
        $sjstok = $this->db->get_where('tb_sj_stok', ['id_sj_stok' => $id_sj_stok])->row_array();
        $id_gudang_stok = $sjstok['id_gudang_stok'];

        $data['title'] = 'Detail Tambah Stok';
        $data['gudang'] = $this->db->get_where('tb_gudang_stok', ['id_gudang_stok' => $id_gudang_stok])->row_array();
        $data['citys'] = $this->db->get_where('tb_city', ['id_gudang_stok' => $id_gudang_stok])->result_array();
        $data['sjstok'] = $sjstok;
        $data['detailSjstoks'] = $this->db->get_where('tb_detail_sj_stok', ['id_sj_stok' => $id_sj_stok])->result_array();
        if ($this->session->userdata('id_distributor') == 7) {
            $data['masterProduks'] = $this->db->get_where('tb_master_produk', ['id_distributor' => 1])->result_array();
        } else {
            $data['masterProduks'] = $this->db->get_where('tb_master_produk', ['id_distributor' => $this->session->userdata('id_distributor')])->result_array();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Sjstok/Detail');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function update($id)
    {
        $this->form_validation->set_rules('name_sjstok_stok', 'Nama', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap lengkapi form");
            redirect('sjstok');
        } else {
            $post = $this->input->post();
            $dataSjstok = [
                'name_sjstok_stok' => $post['name_sjstok_stok'],
                'id_distributor' => $this->session->userdata('id_distributor'),
                'updated_at' => date("Y-m-d H:i:s")
            ];

            $insert = $this->db->update('tb_sj_stok', $dataSjstok, ['id_sj_stok' => $id]);

            if ($insert) {
                $this->session->set_flashdata('success', "Berhasil mengubah data sjstok!");
                redirect('sjstok');
            } else {
                $this->session->set_flashdata('failed', "Gagal mengubah data sjstok!");
                redirect('sjstok');
            }
        }
    }

    public function createDetail()
    {
        $this->form_validation->set_rules('qty_detail_sj_stok', 'Qty', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap lengkapi form");
            redirect('sjstok');
        } else {
            $post = $this->input->post();
            $id_sj_stok = $post['id_sj_stok'];

            $dataDetailSjstok = [
                'id_sj_stok' => $id_sj_stok,
                'id_master_produk' => $post['id_master_produk'],
                'qty_detail_sj_stok' => $post['qty_detail_sj_stok'],
                'updated_at' => date("Y-m-d H:i:s")
            ];

            $insert = $this->db->insert('tb_detail_sj_stok', $dataDetailSjstok);

            if ($insert) {
                $this->session->set_flashdata('success', "Berhasil menambah produk!");
                redirect('sjstok/' . $id_sj_stok);
            } else {
                $this->session->set_flashdata('failed', "Gagal menambah produk!");
                redirect('sjstok/' . $id_sj_stok);
            }
        }
    }

    public function updateDetail($id_detail_sj_stok)
    {
        $this->form_validation->set_rules('qty_detail_sj_stok', 'Qty', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap lengkapi form");
            redirect('sjstok');
        } else {
            $post = $this->input->post();
            $id_sj_stok = $post['id_sj_stok'];

            $dataDetailSjstok = [
                'id_sj_stok' => $id_sj_stok,
                'id_master_produk' => $post['id_master_produk'],
                'qty_detail_sj_stok' => $post['qty_detail_sj_stok'],
                'updated_at' => date("Y-m-d H:i:s")
            ];

            $insert = $this->db->update('tb_detail_sj_stok', $dataDetailSjstok, ['id_detail_sj_stok' => $id_detail_sj_stok]);

            if ($insert) {
                $this->session->set_flashdata('success', "Berhasil mengubah produk!");
                redirect('sjstok/' . $id_sj_stok);
            } else {
                $this->session->set_flashdata('failed', "Gagal mengubah produk!");
                redirect('sjstok/' . $id_sj_stok);
            }
        }
    }

    public function finish($id_sj_stok)
    {
        $dataSjstok = [
            'is_finished' => 1
        ];

        $insert = $this->db->update('tb_sj_stok', $dataSjstok, ['id_sj_stok' => $id_sj_stok]);

        if ($insert) {
            $this->session->set_flashdata('success', "Berhasil finish data sjstok!");
            redirect('sjstok');
        } else {
            $this->session->set_flashdata('failed', "Gagal finish data sjstok!");
            redirect('sjstok');
        }
    }

    public function delete($id)
    {
        $insert = $this->db->delete('tb_sj_stok', ['id_sj_stok' => $id]);

        if ($insert) {
            $this->session->set_flashdata('success', "Berhasil menghapus data sjstok!");
            redirect('sjstok');
        } else {
            $this->session->set_flashdata('failed', "Gagal menghapus data sjstok!");
            redirect('sjstok');
        }
    }

    public function deleteDetail($id)
    {
        $detailSjstok = $this->db->get_where('tb_detail_sj_stok', ['id_detail_sj_stok' => $id])->row_array();
        $insert = $this->db->delete('tb_detail_sj_stok', ['id_detail_sj_stok' => $id]);

        if ($insert) {
            $this->session->set_flashdata('success', "Berhasil menghapus data sjstok!");
            redirect('sjstok/' . $detailSjstok['id_sj_stok']);
        } else {
            $this->session->set_flashdata('failed', "Gagal menghapus data sjstok!");
            redirect('sjstok/' . $detailSjstok['id_sj_stok']);
        }
    }

    public function print($id_sj_stok)
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }

        $sjstok = $this->db->get_where('tb_sj_stok', ['id_sj_stok' => $id_sj_stok])->row_array();

        // Generate QR
        $this->load->library('ciqrcode');
        $config['cacheable']    = true; //boolean, the default is true
        $config['cachedir']             = './assets/'; //string, the default is application/cache/
        $config['errorlog']             = './assets/'; //string, the default is application/logs/
        $config['imagedir']             = './assets/img/qr/stok/'; //direktori penyimpanan qr code
        $config['quality']              = true; //boolean, the default is true
        $config['size']                 = '1024'; //interger, the default is 1024
        $config['black']                = array(224, 255, 255); // array, default is array(255,255,255)
        $config['white']                = array(70, 130, 180); // array, default is array(0,0,0)
        $this->ciqrcode->initialize($config);

        $image_name = $id_sj_stok . date('Y-m-d-H_i_s') . '.png'; //buat name dari qr code sesuai dengan nim

        $params['data'] = base_url('sjstok/rechieved/') . $sjstok['id_sj_stok']; //data yang akan di jadikan QR CODE
        $params['level'] = 'H'; //H=High
        $params['size'] = 10;
        $params['savename'] = FCPATH . $config['imagedir'] . $image_name; //simpan image QR CODE ke folder assets/images/
        $this->ciqrcode->generate($params); // fungsi untuk generate QR CODE

        $id_gudang_stok = $sjstok['id_gudang_stok'];
        $data['sjstok'] = $sjstok;
        $data['qr'] = $image_name;
        $data['detailSjstoks'] = $this->db->get_where('tb_detail_sj_stok', ['id_sj_stok' => $id_sj_stok])->result_array();
        $data['gudang'] = $this->db->get_where('tb_gudang_stok', ['id_gudang_stok' => $id_gudang_stok])->row_array();

        // $this->load->view('Sjstok/Print', $data);

        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        $mpdf->SetMargins(0, 0, 5);
        $html = $this->load->view('Sjstok/Print', $data, true);
        $mpdf->AddPage('P');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    public function rechieved($id_sj_stok)
    {
        $sjstok = $this->db->get_where('tb_sj_stok', ['id_sj_stok' => $id_sj_stok])->row_array();
        $id_gudang_stok = $sjstok['id_gudang_stok'];

        $data['title'] = 'Konfirmasi Penerimaan Stok';
        $data['gudang'] = $this->db->get_where('tb_gudang_stok', ['id_gudang_stok' => $id_gudang_stok])->row_array();
        $data['citys'] = $this->db->get_where('tb_city', ['id_gudang_stok' => $id_gudang_stok])->result_array();
        $data['sjstok'] = $sjstok;
        $data['detailSjstoks'] = $this->db->get_where('tb_detail_sj_stok', ['id_sj_stok' => $id_sj_stok])->result_array();
        $data['masterProduks'] = $this->db->get_where('tb_master_produk', ['id_distributor' => $this->session->userdata('id_distributor')])->result_array();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Sjstok/Rechieve');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function updateRechieved($id_detail_sj_stok)
    {
        $post = $this->input->post();
        $id_sj_stok = $post['id_sj_stok'];
        $this->form_validation->set_rules('qty_rechieved', 'Qty', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap lengkapi form");
            redirect('sjstok/rechieved/' . $id_sj_stok);
        } else {


            $dataDetailSjstok = [
                'qty_rechieved' => $post['qty_rechieved'],
                'updated_at' => date("Y-m-d H:i:s")
            ];

            $insert = $this->db->update('tb_detail_sj_stok', $dataDetailSjstok, ['id_detail_sj_stok' => $id_detail_sj_stok]);

            if ($insert) {
                $this->session->set_flashdata('success', "Berhasil mengubah adjusment!");
                redirect('sjstok/rechieved/' . $id_sj_stok);
            } else {
                $this->session->set_flashdata('failed', "Gagal mengubah adjusment!");
                redirect('sjstok/rechieved/' . $id_sj_stok);
            }
        }
    }

    public function rechieved_save($id_sj_stok)
    {
        $post = $this->input->post();

        $this->form_validation->set_rules('rechieved_name', 'Nama Penerima', 'required');
        $this->form_validation->set_rules('rechieved_phone', 'Nomor HP Penerima', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('failed', "Harap lengkapi form");
            redirect('sjstok/rechieved/' . $id_sj_stok);
        }

        $sjstok = $this->db->get_where('tb_sj_stok', ['id_sj_stok' => $id_sj_stok])->row_array();
        $detailSjstoks = $this->db->get_where('tb_detail_sj_stok', ['id_sj_stok' => $id_sj_stok])->result_array();

        $dataSjstok = [
            'is_rechieved' => 1,
            'rechieved_date' => date("Y-m-d H:i:s"),
            'rechieved_name' => $post['rechieved_name'],
            'rechieved_phone' => $post['rechieved_phone']
        ];

        $updateSjstok = $this->db->update('tb_sj_stok', $dataSjstok, ['id_sj_stok' => $id_sj_stok]);

        if ($updateSjstok) {
            foreach ($detailSjstoks as $detailSjstok) {
                $dataStok = [
                    'id_gudang_stok' => $sjstok['id_gudang_stok'],
                    'id_master_produk' => $detailSjstok['id_master_produk'],
                    'jml_stok' => $detailSjstok['qty_rechieved'],
                    'status_stok' => 'in'
                ];

                $saveStok = $this->db->insert('tb_stok', $dataStok);
            }

            $this->session->set_flashdata('success', "Berhasil konfirmasi pengiriman!");
            redirect('sjstok/rechieved/' . $id_sj_stok);
        } else {
            $this->session->set_flashdata('failed', "Gagal konfirmasi pengiriman!");
            redirect('sjstok/rechieved/' . $id_sj_stok);
        }
    }

    public function adjustment()
    {
        $data['title'] = 'Adjustment Stok';

        $this->db->order_by('created_at', 'DESC');
        $data['adjustments'] = $this->db->get_where('tb_stok', ['is_adjustment' => 1])->result_array();
        $data['gudangs'] = $this->db->get('tb_gudang_stok')->result_array();
        if ($this->session->userdata('id_distributor') == 7) {
            $data['produks'] = $this->db->get_where('tb_master_produk', ['id_distributor' => 1])->result_array();
        } else {
            $data['produks'] = $this->db->get_where('tb_master_produk', ['id_distributor' => $this->session->userdata('id_distributor')])->result_array();
        }
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Sjstok/Adjustment');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function adjustmentCreate()
    {
        $post = $this->input->post();

        $stokData = [
            'id_gudang_stok' => $post['id_gudang_stok'],
            'id_master_produk' => $post['id_master_produk'],
            'jml_stok' => $post['jml_stok'],
            'status_stok' => 'in',
            'is_adjustment' => 1,
        ];

        $save = $this->db->insert('tb_stok', $stokData);

        if ($save) {
            $this->session->set_flashdata('success', "Berhasil menambah data!");
            redirect('sjstok/adjustment');
        } else {
            $this->session->set_flashdata('failed', "Gagal menambah data!");
            redirect('sjstok/adjustment');
        }
    }

    public function adjustmentDelete($id_stok)
    {
        $save = $this->db->delete('tb_stok', ['id_stok' => $id_stok]);

        if ($save) {
            $this->session->set_flashdata('success', "Berhasil menghapus data!");
            redirect('sjstok/adjustment');
        } else {
            $this->session->set_flashdata('failed', "Gagal menghapus data!");
            redirect('sjstok/adjustment');
        }
    }
}
