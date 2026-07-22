<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class News extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('news_model');
    }

    public function index()
    {
        $input = array();

        $sort = $this->input->get('sort');

        if ($sort == 'price_asc') {
            $input['order'] = array('price', 'ASC');
        } elseif ($sort == 'price_desc') {
            $input['order'] = array('price', 'DESC');
        } else {
            $input['order'] = array('id', 'DESC');
        }

        $this->load->library('pagination');

        $total = $this->news_model->get_total($input);

        $config = array(
            'base_url' => build_news_index_url(),
            'total_rows' => $total,
            'per_page' => 8,
            'uri_segment' => 2,

            'reuse_query_string' => TRUE, // giữ ?sort khi phân trang

            'full_tag_open'  => '<div class="jm-modern-pagination"><ul>',
            'full_tag_close' => '</ul></div>',

            'first_link' => false,
            'last_link'  => false,

            'next_link' => 'Next',
            'prev_link' => 'Prev',

            'cur_tag_open'  => '<li class="active"><span>',
            'cur_tag_close' => '</span></li>',

            'num_tag_open'  => '<li>',
            'num_tag_close' => '</li>',
        );

        $this->pagination->initialize($config);

        $offset = $this->uri->segment(2);
        $offset = intval($offset);

        $input['limit'] = array($config['per_page'], $offset);

        $this->data['product_list'] = $this->news_model->get_list($input);

        $this->data['canonical_url'] = build_news_index_url($offset > 0 ? (int) ($offset / 8) + 1 : null);

        $this->data['temp'] = 'site/news/index';
        $this->load->view('site/layoutsub', $this->data);
    }

    public function view() {

        // segment(3) thay vì rsegment
        $id = $this->uri->segment(3);

        $post = $this->news_model->get_info($id);

        if (!$post) {
            redirect(base_url());
        }

        $this->load->model('slider_model');
        $input_slider = array();
        $input_slider['order'] = array('sort_order', 'DESC');
        $this->data['slider'] = $this->slider_model->get_list($input_slider);

        $this->data['post'] = $post;
        $this->data['page_title'] = $post->title;

        $input_news = array();
        $input_news['order'] = array('id', 'DESC');
        $input_news['limit'] = array(6, 0);
        $this->data['other_news'] = $this->news_model->get_list($input_news);

        $this->data['canonical_url'] = build_news_post_url($post);

        $this->data['temp'] = 'site/news_detail';
        $this->load->view('site/layout', $this->data);
    }
}