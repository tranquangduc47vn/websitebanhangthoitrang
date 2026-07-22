<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class Tuyendung extends MY_Admin_Controller {



    public function __construct() {

        parent::__construct();

        $this->load->model('tuyendung_model');

        $this->load->library('form_validation');

        $this->load->helper('form');

    }



    private function _create_slug($str) {

        $str = trim(mb_strtolower($str, 'UTF-8'));

        $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);

        $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);

        $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);

        $str = preg_replace('/(ò|á|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);

        $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);

        $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);

        $str = preg_replace('/(đ)/', 'd', $str);

        $str = preg_replace('/[^a-z0-9-\s]/', '', $str);

        $str = preg_replace('/([\s]+)/', '-', $str);

        return trim($str, '-');

    }



    public function index() {

        $list = $this->tuyendung_model->get_list();

        $this->data['list'] = $list;

        $this->data['total'] = !empty($list) ? count($list) : 0;

        $this->render_admin('admin/tuyendung/index');

    }



    public function add() {

        if ($this->input->post()) {

            $this->form_validation->set_rules('title', 'Tiêu đề vị trí', 'required');

            

            if ($this->form_validation->run()) {

                $config['upload_path']   = './upload/';

                $config['allowed_types'] = 'gif|jpg|png|jpeg';

                $config['max_size']      = 2048;

                $this->load->library('upload', $config);

                

                $image = '';

                if ($this->upload->do_upload('image')) {

                    $upload_data = $this->upload->data();

                    $image = $upload_data['file_name'];

                }



                $data = array(

                    'title'      => $this->input->post('title'),

                    'slug'       => $this->_create_slug($this->input->post('title')),

                    'intro'      => $this->input->post('intro'),

                    'content'    => $this->input->post('content'),

                    'image'      => $image,

                    'created_at' => date('Y-m-d H:i:s')

                );



                if ($this->tuyendung_model->create($data)) {

                    $this->session->set_flashdata('message', 'Thêm dữ liệu và upload ảnh thành công!');

                } else {

                    $this->session->set_flashdata('message', 'Lưu dữ liệu thất bại.');

                }

                redirect(admin_url('tuyendung'));

            }

        }



        $this->render_admin('admin/tuyendung/add');

    }



    public function edit() {

        $id = $this->uri->rsegment(3);

        $info = $this->tuyendung_model->get_info($id);

        if (!$info) {

            $this->session->set_flashdata('message', 'Bài viết tuyển dụng này không tồn tại!');

            redirect(admin_url('tuyendung'));

        }

        $this->data['info'] = $info;



        if ($this->input->post()) {

            $this->form_validation->set_rules('title', 'Tiêu đề vị trí', 'required');

            

            if ($this->form_validation->run()) {

                $config['upload_path']   = './upload/';

                $config['allowed_types'] = 'gif|jpg|png|jpeg';

                $config['max_size']      = 2048;

                $this->load->library('upload', $config);

                

                $image = $info->image; 



                if ($this->upload->do_upload('image')) {

                    $upload_data = $this->upload->data();

                    $image = $upload_data['file_name'];



                    if (!empty($info->image)) {

                        $old_image_path = './upload/' . $info->image;

                        if (file_exists($old_image_path)) {

                            unlink($old_image_path);

                        }

                    }

                }



                $data = array(

                    'title'   => $this->input->post('title'),

                    'slug'    => $this->_create_slug($this->input->post('title')),

                    'intro'   => $this->input->post('intro'),

                    'content' => $this->input->post('content'),

                    'image'   => $image

                );



                if ($this->tuyendung_model->update($id, $data)) {

                    $this->session->set_flashdata('message', 'Cập nhật bài viết thành công!');

                } else {

                    $this->session->set_flashdata('message', 'Cập nhật dữ liệu thất bại.');

                }

                redirect(admin_url('tuyendung'));

            }

        }



        $this->render_admin('admin/tuyendung/edit');

    }



    public function delete() {

        $id = $this->uri->rsegment(3);

        $info = $this->tuyendung_model->get_info($id);

        if (!$info) {

            $this->session->set_flashdata('message', 'Bản ghi không tồn tại!');

            redirect(admin_url('tuyendung'));

        }



        $this->tuyendung_model->delete($id);

        

        if (!empty($info->image)) {

            $image_path = './upload/' . $image_path;

            if (file_exists($image_path)) {

                unlink($image_path);

            }

        }



        $this->session->set_flashdata('message', 'Xóa tin tuyển dụng thành công!');

        redirect(admin_url('tuyendung'));

    }

}

