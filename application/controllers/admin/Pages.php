<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class Pages extends MY_Admin_Controller {



    public function __construct() {

        parent::__construct();

        $this->load->database();

    }



    public function index() {

        $query = $this->db->get('pages');

        $this->data['list'] = $query->result_array();



        $this->render_admin('admin/pages/index');

    }



    public function add() {

        if ($this->input->post('submit')) {

            $data = array(

                'title'   => $this->input->post('title'),

                'slug'    => $this->input->post('slug'),

                'content' => $this->input->post('content')

            );

            

            if ($this->db->insert('pages', $data)) {

                $this->session->set_flashdata('message', 'Thêm trang mới thành công!');

            } else {

                $this->session->set_flashdata('message', 'Có lỗi xảy ra khi thêm mới.');

            }

            redirect(admin_url('pages'));

        }



        $this->render_admin('admin/pages/add');

    }



    public function edit() {

        $id = $this->uri->rsegment(3);

        $query = $this->db->get_where('pages', array('id' => $id), 1);

        $page = $query->row_array();



        if (!$page) {

            $this->session->set_flashdata('message', 'Không tồn tại trang này');

            redirect(admin_url('pages'));

        }



        if ($this->input->post('submit')) {

            $data = array(

                'title'   => $this->input->post('title'),

                'content' => $this->input->post('content')

            );

            

            $this->db->where('id', $id);

            if ($this->db->update('pages', $data)) {

                $this->session->set_flashdata('message', 'Cập nhật trang thành công!');

            } else {

                $this->session->set_flashdata('message', 'Có lỗi xảy ra, vui lòng thử lại.');

            }

            redirect(admin_url('pages'));

        }



        $this->data['page'] = $page;

        $this->render_admin('admin/pages/edit');

    }

}

