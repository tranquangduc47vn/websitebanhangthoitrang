<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__ . '/AiProviderInterface.php';

// No API key — AiAssistantService applies fallback_to_staff.
class NullProvider implements AiProviderInterface {

	public function complete(array $messages, array $options = array())
	{
		return array(
			'ok' => false,
			'content' => '',
			'error' => 'provider_not_configured',
		);
	}

	public function name()
	{
		return 'null';
	}
}
