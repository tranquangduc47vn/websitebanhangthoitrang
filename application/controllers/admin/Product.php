<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends MY_Admin_Controller {

	protected $currentUser;

	function __construct()
	{
		parent::__construct();
		$this->load->helper('permission');
		$this->load->model('product_model');
		$this->load->model('catalog_model');
		$this->load->model('product_variant_model');
		$this->load->library('product_service');
		$this->load->library('form_validation');
		$this->load->helper('form');
		$this->load->library('upload');
		$this->load->library('upload_library');

		$this->currentUser = $this->session->userdata('login');

		if (!$this->currentUser) {
			redirect(admin_url('login'));
		}

		if (!admin_can('product.manage', $this->currentUser)) {
			$this->session->set_flashdata('message_fail', 'Bạn không có quyền truy cập khu vực Sản phẩm!');
			redirect(admin_url('home')); 
		}
	}

	public function index()
	{
		$this->data['message_success'] = $this->session->flashdata('message_success');
		$this->data['message_fail'] = $this->session->flashdata('message_fail');

		$search_name = $this->input->get('name');
		$search_catalog = $this->input->get('catalog_id');
		
		$input_total = array();
		if (!empty($search_name)) {
			$input_total['like'] = array('name', $search_name);
		}
		if (!empty($search_catalog)) {
			$input_total['where'] = array('catalog_id' => $search_catalog);
		}
		$total = $this->product_model->get_total($input_total);
		$this->data['total'] = $total;

		$this->load->library('pagination');
		$config = array();
		$base_url = admin_url('product/index');
		$per = 10;
		$uri = 4;
		
		$config = pagination($base_url, $total, $per, $uri);
		$config['page_query_string'] = FALSE;
		$config['reuse_query_string'] = FALSE;

		$admin_query = array();
		if (!empty($search_name)) {
			$admin_query['name'] = $search_name;
		}
		if (!empty($search_catalog)) {
			$admin_query['catalog_id'] = (int) $search_catalog;
		}
		if (!empty($admin_query)) {
			$config['suffix'] = '?' . http_build_query($admin_query);
		}
		
		$this->pagination->initialize($config);

		$segment = isset($this->uri->segments['4']) ? $this->uri->segments['4'] : NULL;
		$segment = intval($segment);
		
		$input = array();
		$input['limit'] = array($config['per_page'], $segment);
		
		if (!empty($search_name)) {
			$input['like'] = array('product.name', $search_name);
		}
		if (!empty($search_catalog)) {
			$input['where'] = array('product.catalog_id' => $search_catalog);
		}

		$this->db->select('
			product.id as id,
			product.name as name,
			price,
			discount,
			image_link,
			view,
			buyed,
			rate_count,
			rate_total,
			color,
			size,
			quantity,
			catalog.name as namecatalog
		');
		$this->db->join('catalog', 'catalog.id = product.catalog_id');
		
		$product = $this->product_model->get_list($input);
		$this->data['product'] = $product;
		
		$this->data['catalog'] = $this->list_catalog();
		
		$this->data['search_name'] = $search_name;
		$this->data['search_catalog'] = $search_catalog;
		
		$this->render_admin('admin/products/index');
	}

	public function add()
	{
		$this->data['catalog'] = $this->list_catalog();
		$this->form_validation->set_error_delimiters('<div class="alert alert-danger" role="alert" style="padding:5px;border-bottom:0px;">', '</div>');

		if ($this->input->post()) {
			$this->form_validation->set_rules('name','Tên sản phẩm','required');
			$this->form_validation->set_rules('catalog_id','Danh mục','required');
			$this->form_validation->set_rules('price','Giá sản phẩm','required');
			
			if ($this->form_validation->run()) {
				$path = './upload/product/';
				$image_link = $this->upload_library->upload($path, 'image');
				if (empty($image_link)) {
					$this->session->set_flashdata(
						'message_fail',
						'Không tải được ảnh đại diện. ' . $this->upload_library->last_error()
					);
				} else {
				$image_list = array();
				$image_list = $this->upload_library->upload_file($path,'list_image');
				if (!empty($this->upload_library->last_error()) && empty($image_list)) {
					$this->session->set_flashdata('message_fail', 'Ảnh kèm theo: ' . $this->upload_library->last_error());
				}
				$image_list = json_encode($image_list);

				$color_array = $this->input->post('color');
				if (is_array($color_array) && !empty($color_array)) {
					$color_string = implode(',', $color_array);
				} else {
					$color_string = NULL;
				}

				$size_array = $this->input->post('size');
				if (is_array($size_array) && !empty($size_array)) {
					$size_string = implode(',', $size_array);
				} else {
					$size_string = NULL;
				}

				$price_value = $this->_parse_price($this->input->post('price'));
				$discount_fields = $this->_build_discount_fields(
					$price_value,
					$this->input->post('discount')
				);

				$data = array(
					'name'        => $this->input->post('name'),
					'image_link'  => $image_link,
					'image_list'  => $image_list,
					'content'     => $this->input->post('content'),
					'catalog_id'  => $this->input->post('catalog_id'),
					'price'       => $price_value,
					'discount'    => $discount_fields['discount'],
					'discount_type' => $discount_fields['discount_type'],
					'discount_percent' => $discount_fields['discount_percent'],
					'color'       => $color_string,
					'size'        => $size_string,
					'quantity'    => 0,
					'status'      => 1,
					'created'     => now()
				);

				if ($this->product_model->create($data)) {
					$product_id = (int) $this->db->insert_id();
					$code = 'SP' . str_pad((string) $product_id, 5, '0', STR_PAD_LEFT);
					$this->product_model->update($product_id, array('code' => $code));

					$colors = is_array($color_array) ? $color_array : array();
					$sizes = is_array($size_array) ? $size_array : array();
					$this->product_service->sync_variants($product_id, $colors, $sizes, $price_value);

					$this->session->set_flashdata(
						'message_success',
						'Sản phẩm đã tạo thành công. Tồn kho hiện tại = 0. Hãy tạo phiếu nhập để cập nhật số lượng thực tế.'
					);
				}else{
					$this->session->set_flashdata('message_fail', 'Thêm sản phẩm thất bại');
				}
				redirect(admin_url('product'));
				}
			}
		}

		$this->render_admin('admin/products/add');
	}

	public function edit()
	{
		$id = $this->uri->rsegment(3);
		$product = $this->product_model->get_info($id);
		if(!$product) {
			$this->session->set_flashdata('message_fail', 'Không tồn tại sản phẩm này');
			redirect(admin_url('product'));
		}

		$this->form_validation->set_rules('name', 'Tên sản phẩm', 'required');
		$this->form_validation->set_rules('catalog_id', 'Danh mục', 'required');
		$this->form_validation->set_rules('price', 'Giá tiền', 'required');

	if($this->form_validation->run())
	{
		$price    = $this->_parse_price($this->input->post('price'));
		$discount_fields = $this->_build_discount_fields(
			$price,
			$this->input->post('discount')
		);
		$discount = $discount_fields['discount'];

		$color_array = $this->input->post('color'); 
		if (is_array($color_array) && !empty($color_array)) {
			$color_string = implode(',', $color_array);
		} else {
			$color_string = NULL;
		}

		$size_array = $this->input->post('size'); 
		if (is_array($size_array) && !empty($size_array)) {
			$size_string = implode(',', $size_array);
		} else {
			$size_string = NULL;
		}

		$data = array(
			'name'       => $this->input->post('name'),
			'catalog_id' => $this->input->post('catalog_id'),
			'price'      => $price,
			'discount'   => $discount,
			'discount_type' => $discount_fields['discount_type'],
			'discount_percent' => $discount_fields['discount_percent'],
			'content'    => $this->input->post('content'),
			'color'      => $color_string,
			'size'       => $size_string,
		);

		$this->load->library('upload_library');
		$upload_path = './upload/product/';

		$this->_apply_product_images_on_edit($product, $upload_path, $data);

		if($this->product_model->update($product->id, $data)) {
			$colors = is_array($color_array) ? $color_array : array();
			$sizes = is_array($size_array) ? $size_array : array();
			$this->product_service->sync_variants($product->id, $colors, $sizes, $price);
			$this->session->set_flashdata('message_success', 'Cập nhật sản phẩm thành công!');
		} else {
			$this->session->set_flashdata('message_fail', 'Có lỗi xảy ra, không thể cập nhật.');
		}
		redirect(admin_url('product'));
	}

		$this->load->model('catalog_model');
		$this->data['catalog'] = $this->list_catalog();
		$this->data['product'] = $product;
		$this->data['variants'] = $this->product_variant_model->get_by_product($product->id);

		$this->render_admin('admin/products/edit');
	}

	public function del()
	{
		if (!$this->input->is_ajax_request() && !$this->input->post()) {
			show_404();
			return;
		}

		if (!admin_can('product.delete_single', $this->currentUser)) {
			echo 'failer';
			return;
		}

		$id = (int) $this->input->post('id');
		if ($id <= 0) {
			echo 'failer';
			return;
		}

		echo $this->_delete_products_by_ids(array($id)) > 0 ? 'success' : 'failer';
	}

	public function bulk_del()
	{
		if (!$this->input->post()) {
			show_404();
			return;
		}

		if (!admin_can('product.bulk_delete', $this->currentUser)) {
			if ($this->input->is_ajax_request()) {
				echo 'failer';
				return;
			}
			$this->session->set_flashdata('message_fail', 'Tài khoản nhân viên (MOD) không có quyền xóa hàng loạt sản phẩm!');
			redirect(admin_url('product'));
			return;
		}

		$checkbox = $this->input->post('checkbox');
		if (!is_array($checkbox) || empty($checkbox)) {
			if ($this->input->is_ajax_request()) {
				echo 'failer';
				return;
			}
			$this->session->set_flashdata('message_fail', 'Không có sản phẩm nào được chọn');
			redirect(admin_url('product'));
			return;
		}

		$deleted = $this->_delete_products_by_ids($checkbox);

		if ($this->input->is_ajax_request()) {
			echo $deleted > 0 ? 'success' : 'failer';
			return;
		}

		$selected = count(array_filter(array_map('intval', $checkbox)));
		if ($deleted > 0) {
			$msg = 'Xóa ' . $deleted . ' sản phẩm thành công';
			if ($deleted < $selected) {
				$msg .= ' (' . ($selected - $deleted) . ' sản phẩm không xóa được)';
			}
			$this->session->set_flashdata('message_success', $msg);
		} else {
			$this->session->set_flashdata('message_fail', 'Không xóa được sản phẩm đã chọn. Vui lòng thử lại.');
		}
		redirect(admin_url('product'));
	}

	private function _delete_products_by_ids(array $ids)
	{
		$deleted = 0;
		$ids = array_values(array_unique(array_filter(array_map('intval', $ids))));

		foreach ($ids as $id) {
			if ($id <= 0) {
				continue;
			}

			$product = $this->product_model->get_info($id);
			if (!$product) {
				continue;
			}

			$this->_delete_product_related($id);
			$this->_delete_product_assets($product);

			$this->db->where('id', $id);
			$this->db->delete('product');
			if ($this->db->affected_rows() > 0) {
				$deleted++;
			}
		}

		return $deleted;
	}

	private function _delete_product_related($product_id)
	{
		$product_id = (int) $product_id;
		if ($product_id <= 0) {
			return;
		}

		$this->db->where('product_id', $product_id)->delete('order');

		if ($this->db->table_exists('product_review')) {
			$this->db->where('product_id', $product_id)->delete('product_review');
		}

		if ($this->db->table_exists('product_colors')) {
			$this->db->where('product_id', $product_id)->delete('product_colors');
		}
	}

	private function _parse_price($raw)
	{
		return (int) str_replace(',', '', (string) $raw);
	}

	private function _parse_discount_amount($price, $raw_discount)
	{
		$fields = $this->_build_discount_fields($price, $raw_discount);
		return (int) $fields['discount'];
	}

	private function _build_discount_fields($price, $raw_discount)
	{
		$price = (int) $price;
		$raw = str_replace(',', '', (string) $raw_discount);

		if ($raw === '' || !is_numeric($raw)) {
			return array(
				'discount' => 0,
				'discount_type' => 'percent',
				'discount_percent' => 0,
			);
		}

		$percent = (float) $raw;
		if ($percent < 0) {
			$percent = 0;
		}
		if ($percent > 100) {
			$percent = 100;
		}

		$amount = ($price > 0) ? (int) round($price * $percent / 100) : 0;

		return array(
			'discount' => $amount,
			'discount_type' => 'percent',
			'discount_percent' => (int) round($percent),
		);
	}

	private function _sanitize_image_filename($name)
	{
		$name = basename(str_replace('\\', '/', (string) $name));
		if ($name === '' || strpos($name, '..') !== false) {
			return '';
		}
		return $name;
	}

	private function _apply_product_images_on_edit($product, $upload_path, array &$data)
	{
		$main = $this->_sanitize_image_filename($this->input->post('product_image_main'));
		$list = json_decode($this->input->post('product_image_list'), true);
		if (!is_array($list)) {
			$list = array();
		}

		$list = array_values(array_unique(array_filter(array_map(array($this, '_sanitize_image_filename'), $list))));
		$list = array_values(array_filter($list, function ($filename) use ($main) {
			return $filename !== '' && $filename !== $main;
		}));

		$removed = $this->input->post('product_images_remove');
		if (!is_array($removed)) {
			$removed = array();
		}
		$removed = array_unique(array_filter(array_map(array($this, '_sanitize_image_filename'), $removed)));

		if (!empty($_FILES['image']['name'])) {
			$uploaded_main = $this->upload_library->upload($upload_path, 'image');
			if (empty($uploaded_main)) {
				$this->session->set_flashdata(
					'message_fail',
					'Không cập nhật được ảnh đại diện. ' . $this->upload_library->last_error()
				);
				redirect(admin_url('product/edit/' . $product->id));
				return;
			}
			if ($main !== '' && $main !== $uploaded_main) {
				$removed[] = $main;
			}
			$main = $this->_sanitize_image_filename($uploaded_main);
		}

		if (!empty($_FILES['list_image']['name'][0])) {
			$new_gallery = $this->upload_library->upload_file($upload_path, 'list_image');
			if (!empty($new_gallery)) {
				foreach ($new_gallery as $filename) {
					$filename = $this->_sanitize_image_filename($filename);
					if ($filename !== '' && $filename !== $main) {
						$list[] = $filename;
					}
				}
				$list = array_values(array_unique($list));
			}
		}

		$data['image_link'] = $main !== '' ? $main : null;
		$data['image_list'] = json_encode($list);

		$still_used = $list;
		if ($main !== '') {
			$still_used[] = $main;
		}

		$upload_dir = rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'product' . DIRECTORY_SEPARATOR;
		foreach ($removed as $filename) {
			if (in_array($filename, $still_used, true)) {
				continue;
			}
			$path = $upload_dir . $filename;
			if (is_file($path)) {
				@unlink($path);
			}
		}
	}

	private function _delete_product_assets($product)
	{
		if (!$product) {
			return;
		}

		$upload_dir = rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'product' . DIRECTORY_SEPARATOR;
		$files = array();

		if (!empty($product->image_link)) {
			$files[] = $product->image_link;
		}

		$image_list = json_decode($product->image_list, true);
		if (is_array($image_list)) {
			foreach ($image_list as $img_value) {
				if (is_string($img_value) && $img_value !== '') {
					$files[] = $img_value;
				}
			}
		}

		$files = array_unique(array_filter(array_map(function ($name) {
			return ltrim(str_replace('\\', '/', (string) $name), '/');
		}, $files)));

		foreach ($files as $file_name) {
			if ($file_name === '' || strpos($file_name, '..') !== false) {
				continue;
			}
			$path = $upload_dir . $file_name;
			if (is_file($path)) {
				@unlink($path);
			}
		}
	}

	protected function list_catalog()
	{
		$input = array();
		$input['where'] = array('parent_id' => '1');
		$input['order'] = array('sort_order' , 'asc');
		$catalog = $this->catalog_model->get_list($input);
		foreach ($catalog as $value) {
			$input['where'] = array('parent_id' => $value->id);
			$subs = $this->catalog_model->get_list($input);
			$value->sub = $subs;
		}
		return $catalog;
	}
}