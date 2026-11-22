<?php

class Birokrasi extends CI_Controller
{
    public function privacyPolicy()
    {
        $this->load->view('Birokrasi/PrivacyPolicy');
    }

    public function deleteAccount()
    {
        $this->load->view('Birokrasi/DeleteAccount');
    }

    public function support()
    {
        $this->load->view('Birokrasi/Support');
    }
}
