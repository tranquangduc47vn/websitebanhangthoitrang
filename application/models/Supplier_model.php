<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Supplier_model extends MY_Model {
	var $table = 'suppliers';
	var $order = array('name', 'ASC');
}
