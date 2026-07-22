<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hethongcuahang extends MY_Frontend_Controller {

    public function index()
    {
        $this->data['list_stores'] = $this->db->get('stores')->result();
        $this->data['slider'] = $this->db->get('slider')->result();

        $this->data['page_title'] = 'Hệ thống cửa hàng - JM Dress Design';
        $this->render_frontend_main('site/hethongcuahang/index');
    }
}
