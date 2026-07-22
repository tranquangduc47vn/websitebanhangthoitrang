<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Store_model extends MY_Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'stores';
    }

    public function get_all() {
        return $this->db->get('stores')->result();
    }

    public function get_by_id($id) {
        $this->db->where('id', $id);
        return $this->db->get('stores')->row();
    }

    public function add($data) {
        return $this->db->insert('stores', $data);
    }

    public function update($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('stores', $data);
    }

    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete('stores');
    }
}
