<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| Gmail SMTP — điền credentials trong mail.local.php (copy từ mail.local.example.php)
| hoặc biến môi trường GMAIL_USER / GMAIL_APP_PASSWORD / MAIL_FROM
*/
$local = APPPATH . 'config/mail.local.php';
if (is_file($local)) {
	include $local;
}

$config['mail'] = array(
	'from_email' => isset($mail_from_email) ? $mail_from_email : (getenv('MAIL_FROM') ?: 'noreply@qddesign.local'),
	'from_name'  => isset($mail_from_name) ? $mail_from_name : (defined('SHOP_NAME') ? SHOP_NAME : 'qD Design'),
	'smtp_host'  => isset($mail_smtp_host) ? $mail_smtp_host : 'smtp.gmail.com',
	'smtp_port'  => isset($mail_smtp_port) ? (int) $mail_smtp_port : 587,
	'smtp_user'  => isset($mail_smtp_user) ? $mail_smtp_user : (getenv('GMAIL_USER') ?: ''),
	'smtp_pass'  => isset($mail_smtp_pass) ? $mail_smtp_pass : (getenv('GMAIL_APP_PASSWORD') ?: ''),
	'smtp_secure'=> isset($mail_smtp_secure) ? $mail_smtp_secure : 'tls',
);
