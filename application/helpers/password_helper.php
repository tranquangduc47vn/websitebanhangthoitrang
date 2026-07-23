<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('password_strength_message')) {
	/**
	 * @return string Empty if OK, otherwise Vietnamese error message.
	 */
	function password_strength_message($plain)
	{
		$plain = (string) $plain;
		if (strlen($plain) < 8) {
			return 'Mật khẩu phải có ít nhất 8 ký tự.';
		}
		if (!preg_match('/[A-Za-zÀ-ỹ]/u', $plain)) {
			return 'Mật khẩu phải có ít nhất 1 chữ cái.';
		}
		if (!preg_match('/\d/', $plain)) {
			return 'Mật khẩu phải có ít nhất 1 chữ số.';
		}
		return '';
	}
}

if (!function_exists('is_strong_password')) {
	function is_strong_password($plain)
	{
		return password_strength_message($plain) === '';
	}
}

if (!function_exists('hash_user_password')) {
	function hash_user_password($plain)
	{
		return password_hash((string) $plain, PASSWORD_DEFAULT);
	}
}

if (!function_exists('verify_user_password')) {
	/**
	 * @return bool|string true = OK, false = fail, 'rehash' = legacy MD5 OK, needs upgrade
	 */
	function verify_user_password($plain, $stored)
	{
		$stored = (string) $stored;
		if ($stored === '') {
			return false;
		}

		if (password_verify((string) $plain, $stored)) {
			if (password_needs_rehash($stored, PASSWORD_DEFAULT)) {
				return 'rehash';
			}
			return true;
		}

		if (strlen($stored) === 32 && ctype_xdigit($stored) && hash_equals($stored, md5((string) $plain))) {
			return 'rehash';
		}

		return false;
	}
}

if (!function_exists('generate_reset_token')) {
	function generate_reset_token()
	{
		return bin2hex(random_bytes(32));
	}
}

if (!function_exists('hash_reset_token')) {
	function hash_reset_token($token)
	{
		return hash('sha256', (string) $token);
	}
}
