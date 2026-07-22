<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RateLimiter {

	const SESSION_KEY = 'ai_chat_rate_log';

	public function allow($maxMessages, $windowSeconds)
	{
		$CI = get_instance();
		$now = time();
		$maxMessages = max(1, (int) $maxMessages);
		$windowSeconds = max(1, (int) $windowSeconds);

		$log = $CI->session->userdata(self::SESSION_KEY);
		if (!is_array($log)) {
			$log = array();
		}
		$log = array_values(array_filter($log, function ($ts) use ($now, $windowSeconds) {
			return ($now - $ts) <= $windowSeconds;
		}));

		if (count($log) >= $maxMessages) {
			$CI->session->set_userdata(self::SESSION_KEY, $log);
			return false;
		}

		$log[] = $now;
		$CI->session->set_userdata(self::SESSION_KEY, $log);
		return true;
	}
}
