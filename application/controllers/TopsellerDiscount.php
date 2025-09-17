<?php

class TopsellerDiscount extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MDiscountApp');
        if ($this->session->userdata('id_user') == null) {
            redirect('login');
        }
    }

    public function index()
    {
        $data['title'] = 'Discount Top Seller';
        $data['discountApp'] = $this->MDiscountApp->get();
        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('TopsellerDiscount/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function update()
    {
        $post = $this->input->post();

        $id_discount_app = $post['id_discount_app'];

        $discountAppData = [
            'amount_discount_app' => $post['amount_discount_app'],
            'minimum_order' => $post['minimum_order'],
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $save = $this->MDiscountApp->update($discountAppData, $id_discount_app);

        if ($save) {
            $this->session->set_flashdata('success', "Perubahan disimpan!");
            redirect('topseller/setting/discount');
        } else {
            $this->session->set_flashdata('failed', "Harap coba lagi!");
            redirect('topseller/setting/discount');
        }
    }
}
