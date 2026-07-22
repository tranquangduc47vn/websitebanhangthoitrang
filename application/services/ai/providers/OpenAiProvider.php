<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__ . '/AiProviderInterface.php';

class OpenAiProvider implements AiProviderInterface {

	protected $apiKey;
	protected $model;
	protected $endpoint;
	protected $timeout;
	protected $maxTokens;
	protected $temperature;

	public function __construct(array $config)
	{
		$this->apiKey = isset($config['api_key']) ? (string) $config['api_key'] : '';
		$this->model = isset($config['model']) ? $config['model'] : 'gpt-4o-mini';
		$this->endpoint = isset($config['endpoint']) ? $config['endpoint'] : 'https://api.openai.com/v1/chat/completions';
		$this->timeout = isset($config['timeout']) ? (int) $config['timeout'] : 15;
		$this->maxTokens = isset($config['max_tokens']) ? (int) $config['max_tokens'] : 500;
		$this->temperature = isset($config['temperature']) ? (float) $config['temperature'] : 0.4;
	}

	public function complete(array $messages, array $options = array())
	{
		if ($this->apiKey === '') {
			return array(
				'ok' => false,
				'content' => '',
				'error' => 'missing_api_key',
			);
		}

		if (!function_exists('curl_init')) {
			return array(
				'ok' => false,
				'content' => '',
				'error' => 'curl_not_available',
			);
		}

		$payload = array(
			'model' => $this->model,
			'messages' => $messages,
			'max_tokens' => isset($options['max_tokens']) ? (int) $options['max_tokens'] : $this->maxTokens,
			'temperature' => isset($options['temperature']) ? (float) $options['temperature'] : $this->temperature,
		);

		$ch = curl_init($this->endpoint);
		curl_setopt_array($ch, array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'Authorization: Bearer ' . $this->apiKey,
			),
			CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
			CURLOPT_TIMEOUT => $this->timeout,
			CURLOPT_CONNECTTIMEOUT => min(10, $this->timeout),
		));

		$response = curl_exec($ch);
		$errno = curl_errno($ch);
		$error = curl_error($ch);
		$httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($errno !== 0) {
			log_message('error', 'AiAssistant OpenAiProvider curl error: ' . $error);
			return array('ok' => false, 'content' => '', 'error' => 'network_error');
		}

		$decoded = json_decode((string) $response, true);

		if ($httpCode < 200 || $httpCode >= 300 || !is_array($decoded)) {
			log_message('error', 'AiAssistant OpenAiProvider bad response (' . $httpCode . '): ' . substr((string) $response, 0, 500));
			return array('ok' => false, 'content' => '', 'error' => 'bad_response');
		}

		if (isset($decoded['error'])) {
			$message = isset($decoded['error']['message']) ? $decoded['error']['message'] : 'unknown_error';
			log_message('error', 'AiAssistant OpenAiProvider API error: ' . $message);
			return array('ok' => false, 'content' => '', 'error' => 'api_error');
		}

		$content = '';
		if (isset($decoded['choices'][0]['message']['content'])) {
			$content = trim((string) $decoded['choices'][0]['message']['content']);
		}

		if ($content === '') {
			return array('ok' => false, 'content' => '', 'error' => 'empty_response');
		}

		return array('ok' => true, 'content' => $content, 'error' => null);
	}

	public function name()
	{
		return 'openai:' . $this->model;
	}
}
