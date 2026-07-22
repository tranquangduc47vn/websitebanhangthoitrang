<?php
defined('BASEPATH') OR exit('No direct script access allowed');

interface AiProviderInterface {
	public function complete(array $messages, array $options = array());

	public function name();
}
