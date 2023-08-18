<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
{

	public function index()
	{
		$data['title'] = 'Dashboard';
		$this->load->view('Theme/Header', $data);
		$this->load->view('Theme/Menu');
		$this->load->view('Dashboard/Index');
		$this->load->view('Theme/Footer');
		$this->load->view('Theme/Scripts');
	}
}
