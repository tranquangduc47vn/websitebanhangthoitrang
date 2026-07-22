<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_Loader $load
 * @property CI_Config $config
 * @property CI_URI $uri
 * @property CI_Router $router
 * @property CI_Input $input
 * @property CI_Output $output
 * @property CI_Security $security
 * @property CI_Session $session
 * @property CI_Form_validation $form_validation
 * @property CI_Upload $upload
 * @property CI_Cart $cart
 * @property CI_Pagination $pagination
 * @property CI_DB_query_builder $db
 * @property Admin_model $admin_model
 * @property Banner_model $banner_model
 * @property Catalog_model $catalog_model
 * @property Hoptac_model $hoptac_model
 * @property News_model $news_model
 * @property Order_model $order_model
 * @property Product_model $product_model
 * @property Slider_model $slider_model
 * @property Store_model $store_model
 * @property Transaction_model $transaction_model
 * @property Tuyendung_model $tuyendung_model
 * @property User_model $user_model
 * @property Vanchuyen_model $vanchuyen_model
 * @property Upload_library $upload_library
 */
class MY_Controller extends CI_Controller {
	var $data = array();
	function __construct()
	{
		parent::__construct();
		$controller = $this->uri->segment(1);
		if ($controller !== 'admin') {
				$this->load->model('catalog_model');
				$input = array();
				$input['where'] = array('parent_id' => '1');
				$input['order'] = array('sort_order', 'ASC');
				$catalog = $this->catalog_model->get_list($input);
				foreach ($catalog as $value) {
					$input= array();
					$input['where'] = array('parent_id' => $value->id);
					$input['order'] = array('sort_order', 'ASC');
					$sub = $this->catalog_model->get_list($input);
					$value->sub=$sub;
				}
				$this->data['catalog']=$catalog;
				
				$user = $this->session->userdata('user');
				$this->data['user']=$user;

				$carts = $this->session->userdata('custom_cart');
				if (!is_array($carts)) {
					$carts = array();
				}
				$total_items = 0;
				foreach ($carts as $item) {
					$total_items += isset($item['qty']) ? (int) $item['qty'] : 0;
				}
				$this->data['carts'] = $carts;
				$this->data['total_items'] = $total_items;
		}
	}
}

require_once APPPATH . 'core/MY_Admin_Controller.php';
require_once APPPATH . 'core/MY_Frontend_Controller.php';
