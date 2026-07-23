<?php
/**
 * Gửi email thử qua Gmail SMTP.
 *
 *   php tests/test_smtp.php                    → chỉ kiểm tra config
 *   php tests/test_smtp.php you@gmail.com      → gửi email test
 */
define('BASEPATH', true);
define('APPPATH', __DIR__ . '/../application/');
define('FCPATH', __DIR__ . '/../');

require FCPATH . 'vendor/autoload.php';

use PHPMailer\PHPMailer\Exception as MailException;
use PHPMailer\PHPMailer\PHPMailer;

$to = isset($argv[1]) ? trim($argv[1]) : '';

include APPPATH . 'config/mail.php';
$cfg = isset($config['mail']) ? $config['mail'] : array();

echo "=== SMTP config check ===\n";
echo 'mail.local.php: ' . (is_file(APPPATH . 'config/mail.local.php') ? 'YES' : 'NO (copy from mail.local.example.php)') . "\n";
echo 'smtp_host: ' . ($cfg['smtp_host'] ?? '(empty)') . "\n";
echo 'smtp_port: ' . ($cfg['smtp_port'] ?? '(empty)') . "\n";
echo 'smtp_user: ' . ($cfg['smtp_user'] ?? '(empty)') . "\n";
echo 'from_email: ' . ($cfg['from_email'] ?? '(empty)') . "\n";
echo 'from_name: ' . ($cfg['from_name'] ?? '(empty)') . "\n";
echo 'smtp_pass: ' . (empty($cfg['smtp_pass']) ? '(empty)' : '*** set ***') . "\n";

if (empty($cfg['smtp_user']) || empty($cfg['smtp_pass'])) {
	echo "\nFAIL — Chưa cấu hình SMTP. Tạo file application/config/mail.local.php\n";
	exit(1);
}

if ($to === '') {
	echo "\nOK — Config đủ để gửi mail.\n";
	echo "Chạy: php tests/test_smtp.php your@gmail.com\n";
	exit(0);
}

if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
	echo "\nFAIL — Email nhận không hợp lệ: {$to}\n";
	exit(1);
}

echo "\n=== Sending test email to {$to} ===\n";

try {
	$mail = new PHPMailer(true);
	$mail->isSMTP();
	$mail->Host = $cfg['smtp_host'];
	$mail->SMTPAuth = true;
	$mail->Username = $cfg['smtp_user'];
	$mail->Password = $cfg['smtp_pass'];
	$mail->SMTPSecure = $cfg['smtp_secure'];
	$mail->Port = (int) $cfg['smtp_port'];
	$mail->CharSet = 'UTF-8';
	$mail->SMTPDebug = 0;

	$mail->setFrom($cfg['from_email'], $cfg['from_name']);
	$mail->addAddress($to);
	$mail->Subject = '[Webshop] Test SMTP — ' . date('Y-m-d H:i:s');
	$mail->isHTML(true);
	$mail->Body = '<p>Email test từ webshop. Nếu bạn nhận được mail này, SMTP hoạt động OK.</p>';
	$mail->AltBody = 'Email test tu webshop. SMTP OK.';

	$mail->send();
	echo "PASS — Đã gửi email test. Kiểm tra hộp thư (và cả Spam).\n";
	exit(0);
} catch (MailException $e) {
	echo 'FAIL — ' . $e->getMessage() . "\n";
	echo "Gợi ý: dùng App Password (16 ký tự), bật 2FA Gmail, from_email = smtp_user.\n";
	exit(1);
} catch (Exception $e) {
	echo 'FAIL — ' . $e->getMessage() . "\n";
	exit(1);
}
