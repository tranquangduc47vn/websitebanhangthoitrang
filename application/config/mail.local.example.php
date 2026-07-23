<?php
/**
 * Sao chép file này thành mail.local.php rồi điền Gmail App Password:
 *   copy application\config\mail.local.example.php application\config\mail.local.php
 *
 * Không commit mail.local.php (gitignore).
 */
$mail_from_email = '';
$mail_from_name  = 'qD Design';
$mail_smtp_host  = 'smtp.gmail.com';
$mail_smtp_port  = 587;
$mail_smtp_user  = '';
$mail_smtp_pass  = '';
$mail_smtp_secure = 'tls';
