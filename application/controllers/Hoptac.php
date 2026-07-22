<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hoptac extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('hoptac_model');
        $this->load->model('slider_model');
    }

    public function index() {
        $list = $this->hoptac_model->get_list();
        $this->data['list'] = $list;

        $slider = $this->slider_model->get_list();
        $this->data['slider'] = $slider; 

        $this->data['temp'] = 'site/hoptac/index';
        $this->load->view('site/layout', $this->data); 
    }

    public function view() {
        $slug = $this->uri->rsegment(3);
        
        $input = array('where' => array('slug' => $slug));
        $info = $this->hoptac_model->get_list($input);
        
        if(empty($info)) {
            redirect(base_url());
        }
        
        $this->data['info'] = $info[0];

        $slider = $this->slider_model->get_list();
        $this->data['slider'] = $slider;

        $this->data['temp'] = 'site/hoptac/view';
        $this->load->view('site/layout', $this->data);
    }
}
