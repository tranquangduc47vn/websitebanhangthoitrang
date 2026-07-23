<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_movement_model extends MY_Model {
	var $table = 'stock_movements';
	var $order = array('id', 'DESC');
}
