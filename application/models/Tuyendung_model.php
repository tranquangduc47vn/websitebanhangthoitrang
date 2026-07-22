<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tuyendung_model extends MY_Model {

    public $table = 'tuyendung';

    public function __construct() {
        parent::__construct();
    }

    public function get_list_tuyendung() {
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get($this->table);
        return $query->result_array();
    }
}
