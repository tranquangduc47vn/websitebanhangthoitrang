<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tintuc extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function index() {
        $this->load->model('slider_model');
        $input_slider = array();
        $input_slider['order'] = array('sort_order', 'DESC');
        $this->data['slider'] = $this->slider_model->get_list($input_slider);

        // Fallback bảng tin: news, posts, hoặc tintuc
        $table_name = '';
        if ($this->db->table_exists('news')) {
            $table_name = 'news';
        } elseif ($this->db->table_exists('posts')) {
            $table_name = 'posts';
        } elseif ($this->db->table_exists('tintuc')) {
            $table_name = 'tintuc';
        }

        if (!empty($table_name)) {
            $this->db->order_by('id', 'DESC');
            $query = $this->db->get($table_name);
            $this->data['list_news'] = $query->result_array();
        } else {
            $this->data['list_news'] = array();
        }

        $this->data['page_title'] = "Tin tức & Sự kiện - JM Dress Design";

        $this->data['temp'] = 'site/tintuc_index';
        
        $this->load->view('site/layout', $this->data);
    }
}
