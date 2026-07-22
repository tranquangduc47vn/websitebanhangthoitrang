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
		
		if ($this->input->post() && !$this->input->is_ajax_request())
		{
			// Chỉ ADMIN xóa hàng loạt
			if (!admin_can('product.bulk_delete', $this->currentUser)) {
				$this->session->set_flashdata('message_fail', 'Tài khoản nhân viên (MOD) không có quyền xóa hàng loạt sản phẩm!');
				redirect(admin_url('product'));
			}

			$checkbox = $this->input->post('checkbox');
			if (is_array($checkbox) && !empty($checkbox)) {
				foreach ($checkbox as $value) {
					$product = $this->product_model->get_info($value);
					if ($product) {
						$image = './upload/product/'.$product->image_link;
						if (file_exists($image)) {
							unlink($image);
						}
						$image_list = json_decode($product->image_list);
						if (is_array($image_list)) {
							foreach ($image_list as $img_value) {
								$image = './upload/product/'.$img_value;
								if (file_exists($image)) {
									unlink($image);
								}
							}
						}
					}
				}
				$this->db->where_in('id',$checkbox);
				$this->db->delete('product');

				$flag = $this->db->affected_rows();
				if ($flag > 0) {
					$this->session->set_flashdata('message_success', 'Xóa '.$flag.' sản phẩm thành công');
				} else {
					$this->session->set_flashdata('message_fail', 'Xóa sản phẩm thất bại');
				}
			}
			redirect(admin_url('product'));
		}

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
		$config['reuse_query_string'] = TRUE;
		
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

				$data = array(
					'name'        => $this->input->post('name'),
					'image_link'  => $image_link,
					'image_list'  => $image_list,
					'content'     => $this->input->post('content'),
					'catalog_id'  => $this->input->post('catalog_id'),
					'price'       => str_replace(',','',$this->input->post('price')),
					'discount'    => str_replace(',','',$this->input->post('discount')),
					'color'       => $color_string,
					'size'        => $size_string,
					'quantity'    => intval($this->input->post('quantity')), 
					'created'     => now()
				);

				if ($this->product_model->create($data)) {
					$this->session->set_flashdata('message_success', 'Thêm sản phẩm thành công');
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
		$price    = str_replace(',', '', $this->input->post('price'));
		$discount = str_replace(',', '', $this->input->post('discount'));

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
			'content'    => $this->input->post('content'),
			'color'      => $color_string,
			'size'       => $size_string,
			'quantity'   => intval($this->input->post('quantity')) 
		);

		$this->load->library('upload_library');
		$upload_path = './upload/product';
		
		$upload_path = './upload/product/';

		if (!empty($_FILES['image']['name'])) {

		    $image_link = $this->upload_library->upload($upload_path, 'image');

		    if (!empty($image_link)) {

		        if (!empty($product->image_link) && file_exists($upload_path.$product->image_link)) {
		            unlink($upload_path.$product->image_link);
		        }

		        $data['image_link'] = $image_link;
		    } else {
		        $this->session->set_flashdata(
		            'message_fail',
		            'Không cập nhật được ảnh đại diện. ' . $this->upload_library->last_error()
		        );
		        redirect(admin_url('product/edit/'.$product->id));
		        return;
		    }
		}

		if (!empty($_FILES['list_image']['name'][0])) {
			$image_list = $this->upload_library->upload_file($upload_path, 'list_image');
			
			if(!empty($image_list)) {
				$data['image_list'] = json_encode($image_list);
				
				$old_images = json_decode($product->image_list);
				if(is_array($old_images)) {
					foreach($old_images as $old_img) {
						if(!empty($old_img) && file_exists($upload_path.'/'.$old_img)) {
							unlink($upload_path.'/'.$old_img);
						}
					}
				}
			} else {
				$data['image_list'] = $product->image_list;
			}
		} else {
			// Không upload ảnh kèm mới thì giữ JSON cũ
			$data['image_list'] = $product->image_list;
		}

		if($this->product_model->update($product->id, $data)) {
			$this->session->set_flashdata('message_success', 'Cập nhật sản phẩm thành công!');
		} else {
			$this->session->set_flashdata('message_fail', 'Có lỗi xảy ra, không thể cập nhật.');
		}
		redirect(admin_url('product'));
	}

		$this->load->model('catalog_model');
		$this->data['catalog'] = $this->list_catalog();
		$this->data['product'] = $product;

		$this->render_admin('admin/products/edit');
	}

	public function del()
	{
		if (!admin_can('product.delete_single', $this->currentUser)) {
			echo 'failer';
			return;
		}

		$id = isset($_POST['id'])?$_POST['id']:'NULL';
		$product = $this->product_model->get_info($id);
		
		if ($product && $this->product_model->delete($id)) {
			$image = './upload/product/'.$product->image_link;
			if (file_exists($image)) {
				unlink($image);
			}
			$image_list = json_decode($product->image_list);
			if (is_array($image_list)) {
				foreach ($image_list as $value) {
					$image = './upload/product/'.$value;
					if (file_exists($image)) {
						unlink($image);
					}
				}
			}
			echo 'success';
		}else{
			echo 'failer';
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