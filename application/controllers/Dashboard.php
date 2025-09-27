<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if ($this->session->userdata('id_user') == null) {
			redirect('login');
		}
	}

	public function index()
	{
		$data['title'] = 'Dashboard';
		$data['menuGroup'] = '';
		$data['menu'] = 'Dashboard';

		$this->load->view('Theme/Header', $data);
		$this->load->view('Theme/Menu');
		$this->load->view('Dashboard/Index');
		$this->load->view('Theme/Footer');
		$this->load->view('Theme/Scripts');
	}
}
