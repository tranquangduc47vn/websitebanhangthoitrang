<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__ . '/AiProviderInterface.php';

// Gemini via cURL; messages from PromptBuilder, no DB access from model.
class GeminiProvider implements AiProviderInterface {

	protected $apiKey;
	protected $model;
	protected $endpoint;
	protected $timeout;
	protected $maxTokens;
	protected $temperature;

	public function __construct(array $config)
	{
		$this->apiKey = isset($config['api_key']) ? (string) $config['api_key'] : '';
		$this->model = (isset($config['model']) && $config['model'] !== '') ? $config['model'] : 'gemini-3.1-flash-lite';
		$this->endpoint = (isset($config['endpoint']) && $config['endpoint'] !== '')
			? rtrim($config['endpoint'], '/')
			: 'https://generativelanguage.googleapis.com/v1beta/models';
		$this->timeout = isset($config['timeout']) ? (int) $config['timeout'] : 10;
		$this->maxTokens = isset($config['max_tokens']) ? (int) $config['max_tokens'] : 1024;
		$this->temperature = isset($config['temperature']) ? (float) $config['temperature'] : 0.4;
	}

	public function complete(array $messages, array $options = array())
	{
		if ($this->apiKey === '') {
			return array('ok' => false, 'content' => '', 'error' => 'missing_api_key');
		}

		if (!function_exists('curl_init')) {
			return array('ok' => false, 'content' => '', 'error' => 'curl_not_available');
		}

		$parsed = $this->convertMessages($messages);
		if (empty($parsed['contents'])) {
			return array('ok' => false, 'content' => '', 'error' => 'empty_messages');
		}

		$payload = array(
			'contents' => $parsed['contents'],
			'generationConfig' => array(
				'temperature' => isset($options['temperature']) ? (float) $options['temperature'] : $this->temperature,
				'maxOutputTokens' => isset($options['max_tokens']) ? (int) $options['max_tokens'] : $this->maxTokens,
			),
		);
		if ($parsed['system'] !== '') {
			$payload['systemInstruction'] = array(
				'parts' => array(array('text' => $parsed['system'])),
			);
		}

		$url = $this->endpoint . '/' . $this->model . ':generateContent';

		$ch = curl_init($url);
		curl_setopt_array($ch, array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				// API key in header, not query string (avoid URL logs).
				'x-goog-api-key: ' . $this->apiKey,
			),
			CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
			CURLOPT_TIMEOUT => $this->timeout,
			CURLOPT_CONNECTTIMEOUT => min(10, $this->timeout),
		));

		$response = curl_exec($ch);
		$errno = curl_errno($ch);
		$curlError = curl_error($ch);
		$httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		// Never log API key or URL with key.
		if ($errno !== 0) {
			$errorType = ($errno === CURLE_OPERATION_TIMEDOUT) ? 'timeout' : 'network_error';
			log_message('error', 'AiAssistant GeminiProvider curl error (' . $errno . '): ' . $curlError);
			return array('ok' => false, 'content' => '', 'error' => $errorType);
		}

		if ($httpCode === 429) {
			log_message('error', 'AiAssistant GeminiProvider rate limited (HTTP 429).');
			return array('ok' => false, 'content' => '', 'error' => 'rate_limited');
		}

		if ($httpCode >= 500) {
			log_message('error', 'AiAssistant GeminiProvider server error (HTTP ' . $httpCode . ').');
			return array('ok' => false, 'content' => '', 'error' => 'server_error');
		}

		$decoded = json_decode((string) $response, true);

		if ($httpCode < 200 || $httpCode >= 300 || !is_array($decoded)) {
			log_message('error', 'AiAssistant GeminiProvider bad response (HTTP ' . $httpCode . '): ' . substr((string) $response, 0, 500));
			return array('ok' => false, 'content' => '', 'error' => 'bad_response');
		}

		if (isset($decoded['error'])) {
			$message = isset($decoded['error']['message']) ? $decoded['error']['message'] : 'unknown_error';
			log_message('error', 'AiAssistant GeminiProvider API error: ' . $message);
			return array('ok' => false, 'content' => '', 'error' => 'api_error');
		}

		$content = $this->extractContent($decoded);
		if ($content === '') {
			$finishReason = isset($decoded['candidates'][0]['finishReason']) ? $decoded['candidates'][0]['finishReason'] : 'unknown';
			log_message('error', 'AiAssistant GeminiProvider empty content (finishReason: ' . $finishReason . ').');
			return array('ok' => false, 'content' => '', 'error' => 'empty_response');
		}

		return array('ok' => true, 'content' => $content, 'error' => null);
	}

	public function name()
	{
		return 'gemini:' . $this->model;
	}

	protected function extractContent(array $decoded)
	{
		if (!isset($decoded['candidates'][0]['content']['parts']) || !is_array($decoded['candidates'][0]['content']['parts'])) {
			return '';
		}
		$texts = array();
		foreach ($decoded['candidates'][0]['content']['parts'] as $part) {
			if (isset($part['text'])) {
				$texts[] = (string) $part['text'];
			}
		}
		return trim(implode("\n", $texts));
	}

	protected function convertMessages(array $messages)
	{
		$systemParts = array();
		$contents = array();

		foreach ($messages as $msg) {
			$role = isset($msg['role']) ? $msg['role'] : 'user';
			$text = isset($msg['content']) ? trim((string) $msg['content']) : '';
			if ($text === '') {
				continue;
			}

			if ($role === 'system') {
				$systemParts[] = $text;
				continue;
			}

			$geminiRole = ($role === 'assistant') ? 'model' : 'user';

			// Merge consecutive same-role turns (Gemini requires alternating user/model).
			$lastIndex = count($contents) - 1;
			if ($lastIndex >= 0 && $contents[$lastIndex]['role'] === $geminiRole) {
				$contents[$lastIndex]['parts'][0]['text'] .= "\n" . $text;
			} else {
				$contents[] = array('role' => $geminiRole, 'parts' => array(array('text' => $text)));
			}
		}

		// First turn must be user.
		if (!empty($contents) && $contents[0]['role'] !== 'user') {
			array_unshift($contents, array('role' => 'user', 'parts' => array(array('text' => ' '))));
		}

		return array(
			'system' => implode("\n\n", $systemParts),
			'contents' => $contents,
		);
	}
}
