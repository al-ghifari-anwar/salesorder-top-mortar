<?php

class MVisitQuestion extends CI_Model
{
    public $text_question;
    public $is_required;
    public $answer_type;
    public $answer_option;
    public $id_distributor;
    public $udpated_at;

    public function get()
    {
        $this->db->order_by('created_at', 'DESC');
        $result = $this->db->get('tb_visit_question')->result_array();

        return $result;
    }

    public function getById($id)
    {
        $result = $this->db->get_where('tb_visit_question', ['id_visit_question' => $id])->row_array();

        return $result;
    }

    public function create()
    {
        $post = $this->input->post();
        $this->text_question = $post['text_question'];
        $this->is_required = 0;
        $this->answer_type = $post['answer_type'];
        $this->answer_option = $post['answer_option'] == "" ? null : $post['answer_option'];
        $this->id_distributor = $this->session->userdata('id_distributor');
        $this->udpated_at = date("Y-m-d H:i:s");

        $result = $this->db->insert('tb_visit_question', $this);

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function update($id)
    {
        $post = $this->input->post();
        $this->text_question = $post['text_question'];
        $this->is_required = 0;
        $this->answer_type = $post['answer_type'];
        $this->answer_option = $post['answer_option'] == "" ? null : $post['answer_option'];
        $this->id_distributor = $this->session->userdata('id_distributor');
        $this->udpated_at = date("Y-m-d H:i:s");

        $result = $this->db->update('tb_visit_question', $this, ['id_visit_question' => $id]);

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function destroy($id)
    {
        $result = $this->db->delete('tb_visit_question', ['id_visit_question' => $id]);

        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}
