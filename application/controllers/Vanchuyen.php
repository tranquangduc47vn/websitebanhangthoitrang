<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vanchuyen extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('vanchuyen_model');
        $this->load->model('slider_model');
    }

    public function index() {
        $list = $this->vanchuyen_model->get_list();
        $this->data['list'] = $list;

        $slider = $this->slider_model->get_list();
        $this->data['slider'] = $slider; 

        $this->data['temp'] = 'site/vanchuyen/index';
        $this->load->view('site/layout', $this->data); 
    }

    public function view() {
        $slug = $this->uri->rsegment(3);
        
        $input = array('where' => array('slug' => $slug));
        $info = $this->vanchuyen_model->get_list($input);
        
        if(empty($info)) {
            redirect(base_url());
        }
        
        $this->data['info'] = $info[0];

        $slider = $this->slider_model->get_list();
        $this->data['slider'] = $slider;

        $this->data['temp'] = 'site/vanchuyen/view';
        $this->load->view('site/layout', $this->data);
    }
}
