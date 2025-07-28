<?php

class TopsellerSetting extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MSettingTopseller');
    }

    public function index()
    {
        $data['title'] = 'Discount Top Seller';
        $data['settingTopseller'] = $this->MSettingTopseller->get();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('TopsellerSetting/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function update()
    {
        $post = $this->input->post();

        $id_setting_topseller = $post['id_setting_topseller'];

        $settingTopsellerData = [
            'minimum_skor' => $post['minimum_skor'],
            'minimum_order' => $post['minimum_order'],
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $save = $this->MSettingTopseller->update($settingTopsellerData, $id_setting_topseller);

        if ($save) {
            $this->session->set_flashdata('success', "Perubahan disimpan!");
            redirect('topseller/setting/global');
        } else {
            $this->session->set_flashdata('failed', "Harap coba lagi!");
            redirect('topseller/setting/global');
        }
    }
}
