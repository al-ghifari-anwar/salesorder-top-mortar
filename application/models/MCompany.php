<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MCompany extends CI_Model
{
    public $name_company;
    public $address_company;
    public $phone_company;
    public $norek_company;
    public $img_company;
    public $updated_at;

    public function get()
    {
        $query = $this->db->get_where('tb_company')->result_array();
        return $query;
    }

    public function getById($id)
    {
        $query = $this->db->get_where('tb_company', ['id_company' => $id])->row_array();
        return $query;
    }

    public function getByIdDistributor($id_dist)
    {
        $query = $this->db->get_where('tb_company', ['id_distributor' => $id_dist])->row_array();
        return $query;
    }

    public function insert()
    {
        $post = $this->input->post();
        $this->name_company = $post['name_company'];
        $this->address_company = $post['address_company'];
        $this->phone_company = $post['phone_company'];
        $this->norek_company = $post['norek_company'];
        $this->updated_at = date("Y-m-d H:i:s");

        $query = $this->db->insert('tb_company', $this);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function update($id_distributor)
    {
        $getCompany = $this->db->get_where('tb_company', ['id_distributor' => $id_distributor])->row_array();

        $post = $this->input->post();
        $this->name_company = $post['name_company'];
        $this->address_company = $post['address_company'];
        $this->phone_company = $post['phone_company'];
        $this->norek_company = $post['norek_company'];
        $this->img_company = $getCompany['img_company'];
        $upload = $this->uploadImage($post['img_company']);
        if ($upload['status'] == 'success') {
            $data = $upload['message'];
            $this->img_company = $data['file_name'];
        }
        $this->updated_at = date("Y-m-d H:i:s");

        $query = $this->db->update('tb_company', $this, ['id_distributor' => $id_distributor]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function delete($id)
    {
        $query = $this->db->delete('tb_company', ['id_company' => $id]);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function uploadImage($nama)
    {
        $file_name = str_replace(' ', '-', $nama) . date("Y-m-d_H-i-s");
        $config['upload_path']          = FCPATH . '/assets/img/company_img/';
        $config['allowed_types']        = 'gif|jpg|jpeg|png';
        $config['file_name']            = $file_name;
        $config['overwrite']            = true;
        $config['max_size']             = 5000; //5MB

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('img_company')) {
            $data['error'] = $this->upload->display_errors();

            return ['status' => 'error', 'message' => $this->upload->display_errors()];
        } else {
            $uploaded_data = $this->upload->data();

            return ['status' => 'success', 'message' => $uploaded_data];
        }
    }
}
