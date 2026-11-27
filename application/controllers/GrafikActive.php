<?php

class GrafikActive extends CI_Controller
{
    public function index()
    {
        $id_city = $this->input->get('id_city') ?? 0;

        $data['title'] = 'Grafik Toko Aktif';
        $data['menuGroup'] = '';
        $data['menu'] = 'GrafikActive';

        $data['citys'] = $this->db->get_where('tb_city', ['group_city' => $this->session->userdata('group_distributor')])->result_array();
        $data['id_city'] = $id_city;

        $lastDay = date('Y-m-01');

        $data['actives'] = $this->db->select('SUM(jml_active) as jml_active, month_active, MAX(updated_at) AS updated_at')->join('tb_city', 'tb_city.id_city = tb_active_store.id_city')->where('group_city', $this->session->userdata('group_distributor'))->where("updated_at >= DATE_SUB(LAST_DAY('$lastDay'), INTERVAL 11 MONTH)", null, false)->where("DATE(updated_at) <= LAST_DAY('$lastDay')", null, false)->group_by('month_active')->order_by('updated_at', 'DESC')->get('tb_active_store')->result_array();

        if ($id_city > 0) {
            $data['actives'] = $this->db->select('SUM(jml_active) as jml_active, month_active, MAX(updated_at) AS updated_at')->join('tb_city', 'tb_city.id_city = tb_active_store.id_city')->where('id_city', $id_city)->where("updated_at >= DATE_SUB(LAST_DAY('$lastDay'), INTERVAL 11 MONTH)", null, false)->where("DATE(updated_at) <= LAST_DAY('$lastDay')", null, false)->group_by('month_active')->order_by('updated_at', 'DESC')->get('tb_active_store')->result_array();
        }

        $monthNames = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Agu',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des',
        ];

        $activeStores = [];

        foreach ($data['actives'] as $active) {
            $activeStores[(int) $active['month_active']] = (int) $active['jml_active'];
        }

        $data['chartLabels'] = [];
        $data['chartValues'] = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = strtotime("-$i months", strtotime(date('Y-m-01')));

            $month = (int) date('n', $date);
            $year = date('Y', $date);

            $data['chartLabels'][] = $monthNames[$month] . ' ' . $year;
            $data['chartValues'][] = $activeStores[$month] ?? 0;
        }

        $this->load->view('Theme/Header', $data);
        $this->load->view('Theme/Menu');
        $this->load->view('GrafikActive/Index');
        $this->load->view('Theme/Footer');
        $this->load->view('Theme/Scripts');
    }
}
