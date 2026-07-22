<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__ . '/AiProviderInterface.php';
require_once __DIR__ . '/OpenAiProvider.php';
require_once __DIR__ . '/GeminiProvider.php';
require_once __DIR__ . '/NullProvider.php';

// Chọn provider theo config; fallback NullProvider khi thiếu API key.
class ProviderFactory {

	public static function make()
	{
		$CI =& get_instance();
		$CI->config->load('ai', true);

		$provider = $CI->config->item('ai_provider', 'ai');

		switch ($provider) {
			case 'openai':
				$apiKey = (string) $CI->config->item('ai_openai_api_key', 'ai');
				if ($apiKey === '') {
					return new NullProvider();
				}
				return new OpenAiProvider(array(
					'api_key' => $apiKey,
					'model' => $CI->config->item('ai_openai_model', 'ai'),
					'endpoint' => $CI->config->item('ai_openai_endpoint', 'ai'),
					'timeout' => $CI->config->item('ai_openai_timeout', 'ai'),
					'max_tokens' => $CI->config->item('ai_openai_max_tokens', 'ai'),
					'temperature' => $CI->config->item('ai_openai_temperature', 'ai'),
				));

			case 'gemini':
				$geminiKey = (string) $CI->config->item('ai_gemini_api_key', 'ai');
				if ($geminiKey === '') {
					return new NullProvider();
				}
				return new GeminiProvider(array(
					'api_key' => $geminiKey,
					'model' => $CI->config->item('ai_gemini_model', 'ai'),
					'endpoint' => $CI->config->item('ai_gemini_endpoint', 'ai'),
					'timeout' => $CI->config->item('ai_gemini_timeout', 'ai'),
					'max_tokens' => $CI->config->item('ai_gemini_max_tokens', 'ai'),
					'temperature' => $CI->config->item('ai_gemini_temperature', 'ai'),
				));

			default:
				return new NullProvider();
		}
	}
}
