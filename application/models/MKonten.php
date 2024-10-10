<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MKonten extends CI_Model
{

    public $title_konten;
    public $img_konten;
    public $link_konten;
    public $updated_at;

    public function getAll()
    {
        $query = $this->db->get('tb_konten')->result_array();
        return $query;
    }

    public function getById($id)
    {
        $query = $this->db->get_where('tb_konten', ['id_konten' => $id])->row_array();
        return $query;
    }

    public function insert()
    {
        $post = $this->input->post();
        $upload = $this->uploadImage($post['img_konten']);
        if ($upload['status'] == 'success') {
            $this->title_konten = $post['title_konten'];
            $this->img_konten = $post['img_konten'];
            $this->link_konten = $post['link_konten'];
            $this->updated_at = $post['updated_at'];

            $query = $this->db->insert('tb_konten', $this);

            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->session->set_flashdata('failed', "Error: " . $upload['message']);
            redirect('konten');
        }
    }

    public function update($id)
    {
        $post = $this->input->post();
        $getKonten = $this->db->get_where('tb_konten', ['id_konten' => $id])->row_array();
        $this->title_konten = $post['title_konten'];
        $this->img_konten = $getKonten['img_konten'];
        $this->link_konten = $post['link_konten'];
        $this->updated_at = $post['updated_at'];
        $upload = $this->uploadImage($post['img_konten']);
        if ($upload['status'] == 'success') {
            $data = $upload['message'];
            $this->img_konten = $data['img_konten'];
        }

        $query = $this->db->update('tb_konten', $this, ['id_konten' => $id]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function delete($id)
    {
        $query = $this->db->delete('tb_konten', ['id_konten' => $id]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function uploadImage($nama)
    {
        $file_name = str_replace(' ', '-', $nama);
        $config['upload_path']          = FCPATH . '/assets/img/content_img/';
        $config['allowed_types']        = 'gif|jpg|jpeg|png';
        $config['file_name']            = $file_name;
        $config['overwrite']            = true;
        $config['max_size']             = 5000; //5MB

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('img_konten')) {
            $data['error'] = $this->upload->display_errors();

            return ['status' => 'error', 'message' => $this->upload->display_errors()];
        } else {
            $uploaded_data = $this->upload->data();

            return ['status' => 'success', 'message' => $uploaded_data];
        }
    }
}
