<?php

class PromoTopseller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('MPromoTopseller');
    }

    public function index()
    {
        $data['title'] = 'Banner Promo Top Seller';
        $data['menuGroup'] = 'TopSeller';
        $data['menu'] = 'Promo';
        $data['promotopsellers'] = $this->MPromoTopseller->get();

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('PromoTopseller/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }

    public function create()
    {
        $post = $this->input->post();

        $uploadImg = $this->uploadImage($post['name_promo_topseller'] . '-' . time());

        if ($uploadImg['status'] == 'success') {
            $uploadData = $uploadImg['message'];

            $imageName = base_url('assets/img/promo_img/') . $uploadData['file_name'];

            $promoSellerData = [
                'name_promo_topseller' => $post['name_promo_topseller'],
                'img_promo_topseller' => $imageName,
                'detail_promo_topseller' => $post['detail_promo_topseller'],
            ];

            $save = $this->MPromoTopseller->create($promoSellerData);

            if ($save) {
                $this->session->set_flashdata('success', "Berhasil menyimpan promo");
                redirect('promoseller');
            } else {
                $this->session->set_flashdata('failed', "Gagal menyimpan promo");
                redirect('promoseller');
            }
        } else {
            $this->session->set_flashdata('failed', "Error upload file: " . $uploadImg['message']);
            redirect('promoseller');
        }
    }

    public function update($id_promo_topseller)
    {
        $post = $this->input->post();

        $promoTopseller = $this->MPromoTopseller->getById($id_promo_topseller);

        $uploadImg = $this->uploadImage($post['name_promo_topseller'] . '-' . time());

        $imageName = $promoTopseller['img_promo_topseller'];

        if ($uploadImg['status'] == 'success') {
            $uploadData = $uploadImg['message'];
            $imageName = base_url('assets/img/promo_img/') . $uploadData['file_name'];
        }

        $promoSellerData = [
            'name_promo_topseller' => $post['name_promo_topseller'],
            'img_promo_topseller' => $imageName,
            'detail_promo_topseller' => $post['detail_promo_topseller'],
        ];

        $save = $this->MPromoTopseller->update($id_promo_topseller, $promoSellerData);

        if ($save) {
            $this->session->set_flashdata('success', "Berhasil menyimpan promo");
            redirect('promoseller');
        } else {
            $this->session->set_flashdata('failed', "Gagal menyimpan promo");
            redirect('promoseller');
        }
    }

    public function delete($id_promo_topseller)
    {
        $save = $this->MPromoTopseller->delete($id_promo_topseller);

        if ($save) {
            $this->session->set_flashdata('success', "Berhasil menghapus promo");
            redirect('promoseller');
        } else {
            $this->session->set_flashdata('failed', "Gagal menghapus promo");
            redirect('promoseller');
        }
    }

    public function uploadImage($nama)
    {
        $file_name = str_replace(' ', '-', $nama);
        $config['upload_path']          = FCPATH . '/assets/img/promo_img/';
        $config['allowed_types']        = 'gif|jpg|jpeg|png';
        $config['file_name']            = $file_name;
        $config['overwrite']            = true;
        $config['max_size']             = 12000; //10MB

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('img_promo_topseller')) {
            $data['error'] = $this->upload->display_errors();

            return ['status' => 'error', 'message' => $this->upload->display_errors()];
        } else {
            $uploaded_data = $this->upload->data();

            return ['status' => 'success', 'message' => $uploaded_data];
        }
    }
}
