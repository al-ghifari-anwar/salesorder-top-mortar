<?php

class Kontenmsg extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MKontenmsg');
    }

    public function index()
    {
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
        $data['title'] = 'Blast Konten';
        $data['kontenmsgs'] = $this->MKontenmsg->get();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('Kontenmsg/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function create()
    {
        $post = $this->input->post();
        $uploadImg = $this->uploadImage($post['name_kontenmsg']);

        if ($uploadImg['status'] == 'success') {
            $uploadData = $uploadImg['message'];

            $kontenmsgData = [
                'name_kontenmsg' => $post['name_kontenmsg'],
                'thumbnail_kontenmsg' => $uploadData['file_name'],
                'link_kontenmsg' => $post['link_kontenmsg'],
                'body_kontenmsg' => $post['body_kontenmsg'],
            ];

            $save = $this->MKontenmsg->create($kontenmsgData);

            if ($save) {
                $this->session->set_flashdata('success', "Berhasil menyimpan konten");
                redirect('kontenmsg');
            } else {
                $this->session->set_flashdata('failed', "Gagal menyimpan konten");
                redirect('kontenmsg');
            }
        } else {
            $this->session->set_flashdata('failed', "Error upload file: " . $uploadImg['message']);
            redirect('kontenmsg');
        }
    }

    public function update($id_kontenmsg)
    {
        $post = $this->input->post();

        $kontenmsg = $this->MKontenmsg->getById($id_kontenmsg);

        $thumbnail_kontenmsg = $kontenmsg['thumbnail_kontenmsg'];

        $uploadImg = $this->uploadImage($post['name_kontenmsg']);

        if ($uploadImg['status'] == 'success') {
            $uploadData = $uploadImg['message'];
            $thumbnail_kontenmsg = $uploadData['file_name'];
        }

        $uploadData = $uploadImg['message'];

        $kontenmsgData = [
            'name_kontenmsg' => $post['name_kontenmsg'],
            'thumbnail_kontenmsg' => $thumbnail_kontenmsg,
            'link_kontenmsg' => $post['link_kontenmsg'],
            'body_kontenmsg' => $post['body_kontenmsg'],
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $save = $this->MKontenmsg->update($id_kontenmsg, $kontenmsgData);

        if ($save) {
            $this->session->set_flashdata('success', "Berhasil menyimpan konten");
            redirect('kontenmsg');
        } else {
            $this->session->set_flashdata('failed', "Gagal menyimpan konten");
            redirect('kontenmsg');
        }
    }

    public function delete($id_kontenmsg)
    {
        $save = $this->MKontenmsg->delete($id_kontenmsg);

        if ($save) {
            $this->session->set_flashdata('success', "Berhasil menyimpan konten");
            redirect('kontenmsg');
        } else {
            $this->session->set_flashdata('failed', "Gagal menyimpan konten");
            redirect('kontenmsg');
        }
    }

    public function uploadImage($nama)
    {
        $file_name = str_replace(' ', '-', $nama);
        $config['upload_path']          = FCPATH . '/assets/img/kontenmsg_img/';
        $config['allowed_types']        = 'gif|jpg|jpeg|png';
        $config['file_name']            = $file_name;
        $config['overwrite']            = true;
        $config['max_size']             = 12000; //10MB

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('thumbnail_kontenmsg')) {
            $data['error'] = $this->upload->display_errors();

            return ['status' => 'error', 'message' => $this->upload->display_errors()];
        } else {
            $uploaded_data = $this->upload->data();

            return ['status' => 'success', 'message' => $uploaded_data];
        }
    }
}
