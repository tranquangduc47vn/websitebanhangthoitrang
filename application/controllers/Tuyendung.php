<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tuyendung extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('tuyendung_model');
        $this->load->model('slider_model'); 
    }

    public function index() {

        $list = $this->tuyendung_model->get_list();
        $this->data['list'] = $list;

        $slider = $this->slider_model->get_list();
        $this->data['slider'] = $slider; 

        $this->data['temp'] = 'site/tuyendung/index';
        $this->load->view('site/layout', $this->data); 
    }

    public function view() {

        $slug = $this->uri->rsegment(3);
        
        $input = array('where' => array('slug' => $slug));
        $info = $this->tuyendung_model->get_list($input);
        
        if(empty($info)) {
            redirect(base_url());
        }
        
        $this->data['info'] = $info[0];


        $slider = $this->slider_model->get_list();
        $this->data['slider'] = $slider;

        $this->data['temp'] = 'site/tuyendung/view';
        $this->load->view('site/layout', $this->data);
    }
}
