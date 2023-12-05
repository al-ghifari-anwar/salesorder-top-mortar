<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }

    public function index()
    {
        if ($this->session->userdata('id_user') != null) {
            redirect('dashboard');
        }

        $data['title'] = 'Login';

        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('Auth/Index', $data);
        } else {
            $post = $this->input->post();
            $username = $post['username'];
            $password = md5($post['password']);

            $checkUsername = $this->db->get_where('tb_user', ['username' => $username])->row_array();

            if ($checkUsername) {
                if ($password == $checkUsername['password']) {
                    $data = [
                        'id_user' => $checkUsername['id_user'],
                        'full_name' => $checkUsername['full_name'],
                        'username' => $checkUsername['username'],
                        'level_user' => $checkUsername['level_user'],
                        'phone_user' => $checkUsername['phone_user'],
                        'id_distributor' => $checkUsername['id_distributor']
                    ];
                    $this->session->set_userdata($data);
                    redirect('Dashboard');
                } else {
                    $this->session->set_flashdata('failed', "Password salah!");
                    redirect('login');
                }
            } else {
                $this->session->set_flashdata('failed', "Username tidak terdaftar!");
                redirect('login');
            }
        }
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('dashboard');
    }
}
