<?php

class Merchandise extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $this->load->model('MMerchandise');
    }

    public function index()
    {
        $data['title'] = 'Merchandise Penukaran Poin';
        $data['menuGroup'] = 'TopSeller';
        $data['menu'] = 'MerchPoin';

        $data['merchandises'] = $this->MMerchandise->get();

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Merchandise/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function create()
    {
        $post = $this->input->post();

        $uploadImg = $this->uploadImage($post['name_merchandise'] . '-' . time());

        if ($uploadImg['status'] == 'success') {
            $uploadData = $uploadImg['message'];

            $imageName = base_url('assets/img/merch_img/') . $uploadData['file_name'];

            $merchandiseData = [
                'name_merchandise' => $post['name_merchandise'],
                'price_merchandise' => $post['price_merchandise'],
                'desc_merchandise' => $post['desc_merchandise'],
                'img_merchandise' => $imageName,
            ];

            $save = $this->MMerchandise->create($merchandiseData);

            if ($save) {
                $this->session->set_flashdata('success', "Berhasil menyimpan merchandise");
                redirect('merchandise');
            } else {
                $this->session->set_flashdata('failed', "Gagal menyimpan merchandise");
                redirect('merchandise');
            }
        } else {
            $this->session->set_flashdata('failed', "Error upload file: " . $uploadImg['message']);
            redirect('merchandise');
        }
    }

    public function update($id_merchandise)
    {
        $post = $this->input->post();

        $merchandiseTopseller = $this->MMerchandise->getById($id_merchandise);

        $uploadImg = $this->uploadImage($post['name_merchandise'] . '-' . time());

        $imageName = $merchandiseTopseller['img_merchandise'];

        if ($uploadImg['status'] == 'success') {
            $uploadData = $uploadImg['message'];
            $imageName = base_url('assets/img/merch_img/') . $uploadData['file_name'];
        }

        $merchandiseData = [
            'name_merchandise' => $post['name_merchandise'],
            'price_merchandise' => $post['price_merchandise'],
            'desc_merchandise' => $post['desc_merchandise'],
            'img_merchandise' => $imageName,
        ];

        $save = $this->MMerchandise->update($id_merchandise, $merchandiseData);

        if ($save) {
            $this->session->set_flashdata('success', "Berhasil menyimpan merchandise");
            redirect('merchandise');
        } else {
            $this->session->set_flashdata('failed', "Gagal menyimpan merchandise");
            redirect('merchandise');
        }
    }

    public function delete($id_merchandise)
    {
        $save = $this->MMerchandise->delete($id_merchandise);

        if ($save) {
            $this->session->set_flashdata('success', "Berhasil menghapus merchandise");
            redirect('merchandise');
        } else {
            $this->session->set_flashdata('failed', "Gagal menghapus merchandise");
            redirect('merchandise');
        }
    }

    public function uploadImage($nama)
    {
        $file_name = str_replace(' ', '-', $nama);
        $config['upload_path']          = FCPATH . '/assets/img/merch_img/';
        $config['allowed_types']        = 'gif|jpg|jpeg|png|JPG|JPEG|PNG|GIF';
        $config['file_name']            = $file_name;
        $config['overwrite']            = true;
        $config['max_size']             = 12000; //10MB

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('img_merchandise')) {
            $data['error'] = $this->upload->display_errors();

            return ['status' => 'error', 'message' => $this->upload->display_errors()];
        } else {
            $uploaded_data = $this->upload->data();

            return ['status' => 'success', 'message' => $uploaded_data];
        }
    }
}
