<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class News extends MY_Admin_Controller {



	function __construct()

	{

		parent::__construct();

		$this->load->model('news_model');

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

		$input['order'] = array('id' , 'DESC');

		$list = $this->news_model->get_list($input);

		$this->data['list'] = $list;



		$this->render_admin('admin/posts/index');

	}



	public function add()

	{

		$this->form_validation->set_error_delimiters('<div class="alert alert-danger" role="alert" style="padding:5px;border-bottom:0px;">', '</div>');



		if ($this->input->post()) {

			$this->form_validation->set_rules('title', 'Tiêu đề tin tức', 'required');

			$this->form_validation->set_rules('content', 'Nội dung chi tiết', 'required');

			

			if ($this->form_validation->run()) {

				$path = './upload/news/';

				

				$image_link = $this->upload_library->upload($path, 'image');

				

				if (empty($image_link)) {

					$image_link = isset($_FILES['image']['name']) ? $_FILES['image']['name'] : '';

				}

				if (empty($image_link)) {

					$image_link = 'default.jpg'; 

				}



				$data = array(

					'title'      => $this->input->post('title'),

					'intro'      => $this->input->post('intro'),

					'content'    => $this->input->post('content'),

					'image_link' => $image_link,

					'created'    => date('Y-m-d H:i:s')

				);

				

				if ($this->news_model->create($data)) {

					$this->session->set_flashdata('message_success', 'Thêm tin tức thành công');

				} else {

					$this->db->insert('news', $data);

					$this->session->set_flashdata('message_success', 'Thêm tin tức thành công');

				}

				redirect(admin_url('news'));

			}

		}



		$this->render_admin('admin/posts/add');

	}



	public function edit()

	{

		$id = $this->uri->segment(4);

		$news = $this->news_model->get_info($id);

		if (empty($news)) {

			$this->session->set_flashdata('message_fail', 'Bài viết không tồn tại');

			redirect(admin_url('news'));

		}

		$this->data['news'] = $news;



		if ($this->input->post()) {

			$this->form_validation->set_rules('title', 'Tiêu đề tin tức', 'required');

			$this->form_validation->set_rules('content', 'Nội dung chi tiết', 'required');

			

			if ($this->form_validation->run()) {

				$data = array(

					'title'   => $this->input->post('title'),

					'intro'   => $this->input->post('intro'),

					'content' => $this->input->post('content')

				);

				

				$path = './upload/news/';

				$image_link = $this->upload_library->upload($path, 'image');

				

				if (empty($image_link)) {

					$image_link = isset($_FILES['image']['name']) ? $_FILES['image']['name'] : '';

				}

				

				if (!empty($image_link)) {

					$data['image_link'] = $image_link;

					$old_image = './upload/news/' . $news->image_link;

					if (file_exists($old_image) && is_file($old_image)) {

						unlink($old_image);

					}

				}

				

				if ($this->news_model->update($id, $data)) {

					$this->session->set_flashdata('message_success', 'Cập nhật tin tức thành công');

				} else {

					$this->session->set_flashdata('message_fail', 'Cập nhật tin tức thất bại');

				}

				redirect(admin_url('news'));

			}

		}



		$this->render_admin('admin/posts/edit');

	}



	public function del()

	{

		$id = $this->uri->segment(4);

		$news = $this->news_model->get_info($id);

		

		if (empty($news)) {

			$this->session->set_flashdata('message_fail', 'Bài viết không tồn tại');

			redirect(admin_url('news'));

		}

		

		if ($this->news_model->delete($id)) {

			$image = './upload/news/' . $news->image_link;

			if (file_exists($image) && is_file($image)) {

				unlink($image);

			}

			$this->session->set_flashdata('message_success', 'Xóa bài viết thành công');

		} else {

			$this->session->set_flashdata('message_fail', 'Xóa bài viết thất bại');

		}

		redirect(admin_url('news'));

	}

}

