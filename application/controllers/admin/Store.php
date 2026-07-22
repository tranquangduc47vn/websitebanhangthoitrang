<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class Store extends MY_Admin_Controller {



    public $data = array();



    public function __construct() {

        parent::__construct();

        $this->load->model('store_model');

        

        // Header admin cần login khi admin_info chưa có

        if (isset($this->admin_info)) {

            $this->data['login'] = $this->admin_info; 

        } else {

            $this->data['login'] = $this->session->userdata('login'); 

        }

    }



    public function index() {

        $this->data['message_success'] = $this->session->flashdata('message_success');

        $this->data['message_fail']    = $this->session->flashdata('message_fail');

        $this->data['list_stores']     = $this->store_model->get_all();

        

        $this->render_admin('admin/store/index');

    }



    public function add() {

        if ($this->input->post()) {

            $insert_data = array(

                'name'     => $this->input->post('name'),

                'address'  => $this->input->post('address'),

                'phone'    => $this->input->post('phone'),

                'map_link' => $this->input->post('map_link'),

                'city'     => $this->input->post('city')

            );

            

            if ($this->store_model->add($insert_data)) {

                $this->session->set_flashdata('message_success', 'Thêm mới cửa hàng thành công!');

                redirect(base_url('admin/store'));

            }

        }

        

        $this->data['title'] = "Thêm cửa hàng mới";

        $this->render_admin('admin/store/add');

    }



    public function edit($id) {

        $store = $this->store_model->get_by_id($id);

        if (!$store) {

            $this->session->set_flashdata('message_fail', 'Không tồn tại cửa hàng này!');

            redirect(base_url('admin/store'));

        }



        if ($this->input->post()) {

            $update_data = array(

                'name'     => $this->input->post('name'),

                'address'  => $this->input->post('address'),

                'phone'    => $this->input->post('phone'),

                'map_link' => $this->input->post('map_link'),

                'city'     => $this->input->post('city')

            );

            

            if ($this->store_model->update($id, $update_data)) {

                $this->session->set_flashdata('message_success', 'Cập nhật thông tin thành công!');

                redirect(base_url('admin/store'));

            }

        }



        $this->data['store'] = $store;

        $this->data['title'] = "Chỉnh sửa cửa hàng";

        $this->render_admin('admin/store/edit');

    }



    public function delete($id) {

        $store = $this->store_model->get_by_id($id);

        if ($store) {

            $this->store_model->delete($id);

            $this->session->set_flashdata('message_success', 'Xóa cửa hàng thành công!');

        }

        redirect(base_url('admin/store'));

    }

}

