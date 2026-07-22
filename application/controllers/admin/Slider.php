<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Slider extends MY_Admin_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('slider_model');
		$this->load->library('form_validation');
		$this->load->helper('form');
		$this->load->library('upload');
		$this->load->library('upload_library');
	}

	public function index()
	{
		$message_success = $this->session->flashdata('message_success');
		$this->data['message_success'] = $message_success;

		$message_fail = $this->session->flashdata('message_fail');
		$this->data['message_fail'] = $message_fail;

		$input = array();
		$input['order'] = array('id' , 'ASC');
		$slider = $this->slider_model->get_list($input);
		$this->data['slider'] = $slider;

		$this->render_admin('admin/slider/index');
	}

	public function add()
	{
		$this->form_validation->set_error_delimiters('<div class="alert alert-danger" role="alert" style="padding:5px;border-bottom:0px;">', '</div>');

		if ($this->input->post()) {
			$this->form_validation->set_rules('name', 'Tên slider', 'required');
			$this->form_validation->set_rules('link', 'Liên kết', 'required');
			
			if ($this->form_validation->run()) {
				$path = './upload/slider/';
				
				$image_link = $this->upload_library->upload($path, 'image');
				
				if (empty($image_link)) {
					$image_link = isset($_FILES['image']['name']) ? $_FILES['image']['name'] : '';
				}
				if (empty($image_link)) {
					$image_link = 'default.jpg';
				}

				$data = array(
					'name'       => $this->input->post('name'),
					'image_link' => $image_link,
					'link'       => $this->input->post('link'),
					'sort_order' => $this->input->post('sort_order'),
					'created'    => date('Y-m-d H:i:s')
				);
				
				if ($this->slider_model->create($data)) {
					$this->session->set_flashdata('message_success', 'Thêm slider thành công');
				} else {
					$this->db->insert('slider', $data);
					$this->session->set_flashdata('message_success', 'Ép lưu slider vào DB thành công');
				}
				redirect(admin_url('slider'));
			}
		}

		$this->render_admin('admin/slider/add');
	}

	public function edit()
	{
		$id = $this->uri->segment(4);
		$slider = $this->slider_model->get_info($id);
		if (empty($slider)) {
			$this->session->set_flashdata('message_fail', 'Slider không tồn tại');
			redirect(admin_url('slider'));
		}
		$this->data['slider'] = $slider;

		if ($this->input->post()) {
			$this->form_validation->set_rules('name', 'Tên slider', 'required');
			$this->form_validation->set_rules('link', 'Liên kết', 'required');
			
			if ($this->form_validation->run()) {
				$data = array(
					'name'       => $this->input->post('name'),
					'link'       => $this->input->post('link'),
					'sort_order' => $this->input->post('sort_order')
				);
				
				$path = './upload/slider/';
				$image_link = $this->upload_library->upload($path, 'image');
				
				if (empty($image_link)) {
					$image_link = isset($_FILES['image']['name']) ? $_FILES['image']['name'] : '';
				}
				
				if (!empty($image_link)) {
					$data['image_link'] = $image_link;
					$old_image = './upload/slider/' . $slider->image_link;
					if (file_exists($old_image) && is_file($old_image)) {
						unlink($old_image);
					}
				}
				
				if ($this->slider_model->update($id, $data)) {
					$this->session->set_flashdata('message_success', 'Thay đổi slider thành công');
				} else {
					$this->session->set_flashdata('message_fail', 'Thay đổi slider thất bại');
				}
				redirect(admin_url('slider'));
			}
		}

		$this->render_admin('admin/slider/edit');
	}

	public function del()
	{
		$id = $this->uri->segment(4);
		$slider = $this->slider_model->get_info($id);
		
		if (empty($slider)) {
			$this->session->set_flashdata('message_fail', 'Slider không tồn tại');
			redirect(admin_url('slider'));
		}
		
		if ($this->slider_model->delete($id)) {
			$image = './upload/slider/' . $slider->image_link;
			if (file_exists($image) && is_file($image)) {
				unlink($image);
			}
			$this->session->set_flashdata('message_success', 'Xóa Slider thành công');
		} else {
			$this->session->set_flashdata('message_fail', 'Xóa Slider thất bại');
		}
		redirect(admin_url('slider'));
	}
}