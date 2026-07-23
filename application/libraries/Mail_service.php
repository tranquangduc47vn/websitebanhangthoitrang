<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PHPMailer\PHPMailer\Exception as MailException;
use PHPMailer\PHPMailer\PHPMailer;

class Mail_service {
	protected $CI;

	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->config->load('mail');
	}

	public function send($to, $subject, $html_body, $text_body = '')
	{
		$cfg = $this->CI->config->item('mail');
		if (!is_array($cfg)) {
			log_message('error', 'Mail_service: mail config missing');
			return false;
		}

		if (empty($cfg['smtp_user']) || empty($cfg['smtp_pass'])) {
			log_message('error', 'Mail_service: SMTP credentials not configured (mail.local.php or env)');
			return false;
		}

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

			$mail->setFrom($cfg['from_email'], $cfg['from_name']);
			$mail->addAddress($to);
			$mail->Subject = $subject;
			$mail->isHTML(true);
			$mail->Body = $html_body;
			$mail->AltBody = $text_body !== '' ? $text_body : strip_tags($html_body);

			$mail->send();
			return true;
		} catch (MailException $e) {
			log_message('error', 'Mail_service send failed to ' . $to . ': ' . $e->getMessage());
			return false;
		} catch (Exception $e) {
			log_message('error', 'Mail_service unexpected error to ' . $to . ': ' . $e->getMessage());
			return false;
		}
	}

	public function send_welcome($user)
	{
		if (!$user || empty($user->email)) {
			return false;
		}

		$name = htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8');
		$subject = 'Chào mừng bạn đến với ' . shop_name();
		$html = '<div style="font-family:Arial,sans-serif;line-height:1.6;color:#222;">'
			. '<h2 style="color:#111;">Xin chào ' . $name . '!</h2>'
			. '<p>Cảm ơn bạn đã đăng ký tài khoản tại <strong>' . htmlspecialchars(shop_name(), ENT_QUOTES, 'UTF-8') . '</strong>.</p>'
			. '<p>Bạn có thể đăng nhập và mua sắm bất cứ lúc nào tại '
			. '<a href="' . base_url('dang-nhap') . '">' . base_url('dang-nhap') . '</a>.</p>'
			. '<p style="color:#888;font-size:12px;">Email tự động — vui lòng không trả lời.</p>'
			. '</div>';

		return $this->send($user->email, $subject, $html);
	}

	public function send_password_reset($user, $token)
	{
		if (!$user || empty($user->email) || $token === '') {
			return false;
		}

		$name = htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8');
		$link = base_url('dat-lai-mat-khau/' . $token);
		$subject = 'Đặt lại mật khẩu — ' . shop_name();
		$html = '<div style="font-family:Arial,sans-serif;line-height:1.6;color:#222;">'
			. '<p>Xin chào ' . $name . ',</p>'
			. '<p>Bạn (hoặc ai đó) đã yêu cầu đặt lại mật khẩu. Nhấn liên kết bên dưới trong <strong>15 phút</strong>:</p>'
			. '<p><a href="' . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . '" style="display:inline-block;padding:10px 18px;background:#111;color:#fff;text-decoration:none;border-radius:4px;">Đặt lại mật khẩu</a></p>'
			. '<p style="word-break:break-all;font-size:12px;color:#666;">' . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . '</p>'
			. '<p style="color:#888;font-size:12px;">Nếu bạn không yêu cầu, hãy bỏ qua email này. Liên kết chỉ dùng được một lần.</p>'
			. '</div>';

		$text = "Xin chao {$user->name},\n\nDat lai mat khau (15 phut, 1 lan):\n{$link}\n";

		return $this->send($user->email, $subject, $html, $text);
	}
}
