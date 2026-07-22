<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Banner extends MY_Admin_Controller {

    protected $currentUser;

    public function __construct() {
        parent::__construct();
        $this->load->model('banner_model');
        $this->load->library('upload'); 
        
        $this->currentUser = $this->session->userdata('login');

        if (!$this->currentUser) {
            redirect(admin_url('login'));
        }

        if (!admin_can('banner.manage', $this->currentUser)) {
            $this->session->set_flashdata('message_fail', 'Bạn không có quyền quản lý khu vực Banner!');
            redirect(admin_url('home'));
        }
    }

    public function index() {
        $input = array('order' => array('sort_order', 'ASC'));
        $this->data['list'] = $this->banner_model->get_list($input);

        $this->data['message'] = $this->session->flashdata('message');
        $this->data['message_fail'] = $this->session->flashdata('message_fail');

        $this->render_admin('admin/banner/index');
    }

    public function edit() {
        $id = $this->uri->rsegment(3);
        $banner = $this->banner_model->get_info($id);
        if(!$banner) {
            $this->session->set_flashdata('message', 'Không tồn tại banner này');
            redirect(admin_url('banner'));
        }
        $this->data['banner'] = $banner;

        if($this->input->post('submit')) {
            $this->load->library('upload_library');
            $upload_path = './upload/slider';
            $upload_data = $this->upload_library->upload($upload_path, 'image');
            
            $data = array(
                'name' => $this->input->post('name'),
                'link' => $this->input->post('link')
            );

            if(isset($upload_data['file_name'])) {
                $data['image_link'] = $upload_data['file_name'];
            }

            if($this->banner_model->update($banner->id, $data)) {
                $this->session->set_flashdata('message', 'Cập nhật dữ liệu thành công');
            } else {
                $this->session->set_flashdata('message', 'Không có temporary thay đổi nào được thực hiện');
            }
            redirect(admin_url('banner'));
        }

        $this->render_admin('admin/banner/edit');
    }
}
