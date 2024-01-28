<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MMarketingMessage extends CI_Model
{

    public $nama_marketing_message;
    public $template_id;
    public $image_marketing_message;
    public $body_marketing_message;
    public $week_marketing_message;
    public $target_status;
    public $id_distributor;

    public function getAll()
    {
        $query = $this->db->get_where('tb_marketing_message', ['id_distributor' => $this->session->userdata('id_distributor')])->result_array();
        return $query;
    }

    public function getById($id)
    {
        $query = $this->db->get_where('tb_marketing_message', ['id_marketing_message' => $id])->row_array();
        return $query;
    }

    public function insert()
    {
        $post = $this->input->post();
        $upload = $this->uploadImage($post['nama_marketing_message']);
        if ($upload != false) {
            $this->nama_marketing_message = $post['nama_marketing_message'];
            $this->template_id = $post['template_id'];
            $this->image_marketing_message = $upload['file_name'];
            $this->body_marketing_message = $post['body_marketing_message'];
            $this->week_marketing_message = $post['week_marketing_message'];
            $this->target_status = $post['target_status'];
            $this->id_distributor = $this->session->userdata('id_distributor');

            $query = $this->db->insert('tb_marketing_message', $this);

            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function update($id)
    {
        $post = $this->input->post();

        $upload = $this->uploadImage($post['nama_marketing_message']);
        if ($upload != false) {
            $this->nama_marketing_message = $post['nama_marketing_message'];
            $this->template_id = $post['template_id'];
            $this->image_marketing_message = $upload['file_name'];
            $this->body_marketing_message = $post['body_marketing_message'];
            $this->week_marketing_message = $post['week_marketing_message'];
            $this->target_status = $post['target_status'];
            $this->id_distributor = $this->session->userdata('id_distributor');

            $query = $this->db->update('tb_marketing_message', $this, ['id_marketing_message' => $id]);

            if ($query) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function delete($id)
    {
        $query = $this->db->delete('tb_marketing_message', ['id_marketing_message' => $id]);

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
        $config['max_size']             = 2048; //2MB

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('image_marketing_message')) {
            $data['error'] = $this->upload->display_errors();

            return false;
        } else {
            $uploaded_data = $this->upload->data();

            return $uploaded_data;
        }
    }
}
