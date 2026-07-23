<?php
/**
 * Manual auth/mail flow checks — php tests/manual_auth_flow.php
 */
define('BASEPATH', true);
define('APPPATH', __DIR__ . '/../application/');
define('FCPATH', __DIR__ . '/../');

require FCPATH . 'vendor/autoload.php';
require APPPATH . 'helpers/password_helper.php';

$results = array();

function report($name, $pass, $detail = '')
{
	global $results;
	$results[] = array('name' => $name, 'pass' => (bool) $pass, 'detail' => $detail);
	echo ($pass ? 'PASS' : 'FAIL') . ' — ' . $name;
	if ($detail !== '') {
		echo ' (' . $detail . ')';
	}
	echo PHP_EOL;
}

$hash = hash_user_password('TestPass123');
report('password_hash creates bcrypt', (strpos($hash, '$2y$') === 0 || strpos($hash, '$2a$') === 0), substr($hash, 0, 7));
report('password_verify new hash', verify_user_password('TestPass123', $hash) === true);
report('legacy MD5 rehash flag', verify_user_password('123456', md5('123456')) === 'rehash');

$token = generate_reset_token();
report('reset token length 64 hex', strlen($token) === 64 && ctype_xdigit($token));
$hash1 = hash_reset_token($token);
report('token hash sha256 len 64', strlen($hash1) === 64);

report('PHPMailer autoload', class_exists('PHPMailer\\PHPMailer\\PHPMailer'));

require APPPATH . 'config/database.php';
$c = $db['default'];
$m = new mysqli($c['hostname'], $c['username'], $c['password'], $c['database']);
if ($m->connect_error) {
	report('DB connection', false, $m->connect_error);
} else {
	report('DB connection', true);
	$r = $m->query("SHOW TABLES LIKE 'password_resets'");
	report('password_resets table exists', $r && $r->num_rows > 0);

	$testUser = $m->query('SELECT id FROM user LIMIT 1');
	if ($testUser && $testUser->num_rows) {
		$row = $testUser->fetch_assoc();
		$uid = (int) $row['id'];
		$tok = generate_reset_token();
		$th = $m->real_escape_string(hash_reset_token($tok));
		$exp = time() + 900;
		$now = time();
		$m->query("UPDATE password_resets SET used_at={$now} WHERE user_id={$uid} AND used_at=0");
		$ins = $m->query("INSERT INTO password_resets (user_id,token_hash,expires_at,used_at,created) VALUES ({$uid},'{$th}',{$exp},0,{$now})");
		report('insert reset token', (bool) $ins);

		$sel = $m->query("SELECT id FROM password_resets WHERE token_hash='{$th}' AND used_at=0 AND expires_at>" . time());
		report('find valid token', $sel && $sel->num_rows === 1);

		$m->query("UPDATE password_resets SET used_at=" . time() . " WHERE token_hash='{$th}'");
		$sel2 = $m->query("SELECT id FROM password_resets WHERE token_hash='{$th}' AND used_at=0");
		report('one-time use (used_at set)', $sel2 && $sel2->num_rows === 0);

		$expired = hash_reset_token(generate_reset_token());
		$expPast = time() - 60;
		$m->query("INSERT INTO password_resets (user_id,token_hash,expires_at,used_at,created) VALUES ({$uid},'{$expired}',{$expPast},0,{$now})");
		$sel3 = $m->query("SELECT id FROM password_resets WHERE token_hash='{$expired}' AND used_at=0 AND expires_at>" . time());
		report('expired token rejected', $sel3 && $sel3->num_rows === 0);
	} else {
		report('insert reset token', false, 'no user row');
	}
}

$files = array(
	'application/controllers/User.php',
	'application/views/site/user/forgot_password.php',
	'application/views/site/user/reset_password.php',
	'application/libraries/Mail_service.php',
	'application/config/mail.php',
);
foreach ($files as $f) {
	report('file ' . $f, is_file(FCPATH . $f));
}

$routes = file_get_contents(APPPATH . 'config/routes.php');
report('route quen-mat-khau', strpos($routes, 'quen-mat-khau') !== false);
report('route dat-lai-mat-khau', strpos($routes, 'dat-lai-mat-khau') !== false);

report('User::forgot_password method', strpos(file_get_contents(APPPATH . 'controllers/User.php'), 'function forgot_password') !== false);
report('User::reset_password method', strpos(file_get_contents(APPPATH . 'controllers/User.php'), 'function reset_password') !== false);
report('register uses password_hash', strpos(file_get_contents(APPPATH . 'controllers/User.php'), 'hash_user_password($password)') !== false);

include APPPATH . 'config/mail.php';
$smtpOk = !empty($config['mail']['smtp_user']) && !empty($config['mail']['smtp_pass']);
report('SMTP credentials configured (live email)', $smtpOk, $smtpOk ? 'ready' : 'SKIP until mail.local.php configured');

echo PHP_EOL . '--- Summary ---' . PHP_EOL;
$pass = 0;
$fail = 0;
$skip = 0;
foreach ($results as $r) {
	if ($r['pass']) {
		$pass++;
	} elseif (strpos($r['detail'], 'SKIP') !== false || strpos($r['name'], 'SMTP') !== false) {
		$skip++;
	} else {
		$fail++;
	}
}
echo "PASS: {$pass}  FAIL: {$fail}  SKIP/optional: {$skip}" . PHP_EOL;
exit($fail > 0 ? 1 : 0);
