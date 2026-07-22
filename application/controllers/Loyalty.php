<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Loyalty extends MY_Frontend_Controller {

	public function index()
	{
		$this->load->helper('loyalty');
		$this->data['page_title'] = 'Chính sách tích điểm & hạng thành viên';
		$this->render_frontend_sub('site/loyalty/index');
	}
}
