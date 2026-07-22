<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class Hoptac extends MY_Admin_Controller {



    public function __construct() {

        parent::__construct();

        $this->load->model('hoptac_model');

    }



    public function index() {

        $message = $this->session->flashdata('message');

        $this->data['message'] = $message;



        $list = $this->hoptac_model->get_list();

        $this->data['list'] = $list;



        $this->render_admin('admin/hoptac/index');

    }



    public function add() {

        $this->load->library('form_validation');

        $this->load->helper('form');



        if($this->input->post()) {

            $this->form_validation->set_rules('title', 'Tiêu đề', 'required');

            

            if($this->form_validation->run()) {

                $image = '';

                if($_FILES['image']['name'] != '') {

                    $config['upload_path']   = './upload/';

                    $config['allowed_types'] = 'gif|jpg|png|jpeg';

                    $this->load->library('upload', $config);

                    if($this->upload->do_upload('image')) {

                        $upload_data = $this->upload->data();

                        $image = $upload_data['file_name'];

                    }

                }



                $data = array(

                    'title'      => $this->input->post('title'),

                    'slug'       => $this->_create_slug($this->input->post('title')),

                    'intro'      => $this->input->post('intro'),

                    'content'    => $this->input->post('content'),

                    'image'      => $image,

                    'created_at' => date('Y-m-d H:i:s')

                );



                if($this->hoptac_model->create($data)) {

                    $this->session->set_flashdata('message', 'Thêm mới dữ liệu thành công!');

                } else {

                    $this->session->set_flashdata('message', 'Không thêm được dữ liệu.');

                }

                redirect(admin_url('hoptac'));

            }

        }



        $this->render_admin('admin/hoptac/add');

    }



    public function edit() {

        $id = $this->uri->rsegment(3);

        $info = $this->hoptac_model->get_info($id);

        if(!$info) {

            $this->session->set_flashdata('message', 'Không tồn tại bài viết này.');

            redirect(admin_url('hoptac'));

        }

        $this->data['info'] = $info;



        $this->load->library('form_validation');

        $this->load->helper('form');



        if($this->input->post()) {

            $this->form_validation->set_rules('title', 'Tiêu đề', 'required');

            

            if($this->form_validation->run()) {

                $data = array(

                    'title'   => $this->input->post('title'),

                    'slug'    => $this->_create_slug($this->input->post('title')),

                    'intro'   => $this->input->post('intro'),

                    'content' => $this->input->post('content'),

                );



                if($_FILES['image']['name'] != '') {

                    $config['upload_path']   = './upload/';

                    $config['allowed_types'] = 'gif|jpg|png|jpeg';

                    $this->load->library('upload', $config);

                    if($this->upload->do_upload('image')) {

                        $upload_data = $this->upload->data();

                        $data['image'] = $upload_data['file_name'];

                        

                        if(file_exists('./upload/'.$info->image) && $info->image != '') {

                            unlink('./upload/'.$info->image);

                        }

                    }

                }



                if($this->hoptac_model->update($id, $data)) {

                    $this->session->set_flashdata('message', 'Cập nhật dữ liệu thành công!');

                }

                redirect(admin_url('hoptac'));

            }

        }



        $this->render_admin('admin/hoptac/edit');

    }



    public function delete() {

        $id = $this->uri->rsegment(3);

        $info = $this->hoptac_model->get_info($id);

        if(!$info) {

            $this->session->set_flashdata('message', 'Không tồn tại dữ liệu cần xóa.');

            redirect(admin_url('hoptac'));

        }

        

        if($this->hoptac_model->delete($id)) {

            if(file_exists('./upload/'.$info->image) && $info->image != '') {

                unlink('./upload/'.$info->image);

            }

            $this->session->set_flashdata('message', 'Xóa bài viết thành công!');

        }

        redirect(admin_url('hoptac'));

    }



    private function _create_slug($str) {

        $str = trim(mb_strtolower($str));

        $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);

        $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);

        $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);

        $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);

        $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);

        $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);

        $str = preg_replace('/(đ)/', 'd', $str);

        $str = preg_replace('/[^a-z0-9-\s]/', '', $str);

        $str = preg_replace('/([\s]+)/', '-', $str);

        return $str;

    }

}

