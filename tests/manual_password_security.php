<?php
/**
 * Password security checks — php tests/manual_password_security.php
 */
define('BASEPATH', true);
define('APPPATH', __DIR__ . '/../application/');
define('FCPATH', __DIR__ . '/../');

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
report('hash_user_password uses bcrypt', (strpos($hash, '$2y$') === 0 || strpos($hash, '$2a$') === 0), substr($hash, 0, 7));
report('password_verify accepts bcrypt hash', verify_user_password('TestPass123', $hash) === true);
report('password_verify rejects wrong password', verify_user_password('WrongPass1', $hash) === false);
report('legacy MD5 login returns rehash', verify_user_password('123456', md5('123456')) === 'rehash');
report('legacy MD5 wrong password fails', verify_user_password('654321', md5('123456')) === false);

report('strength rejects short password', !is_strong_password('Ab1'));
report('strength rejects no letter', !is_strong_password('12345678'));
report('strength rejects no digit', !is_strong_password('Abcdefgh'));
report('strength accepts valid password', is_strong_password('TestPass123'));

$userPhp = file_get_contents(APPPATH . 'controllers/User.php');
report('User register uses strength validation', strpos($userPhp, 'callback_validate_password_strength') !== false);
report('User reset uses strength validation', substr_count($userPhp, 'callback_validate_password_strength') >= 2);
report('User has password_change', strpos($userPhp, 'function password_change') !== false);
report('User login uses verify_user_password', strpos($userPhp, 'verify_user_password($password, $user->password)') !== false);
report('User login rehash on legacy MD5', strpos($userPhp, "hash_user_password(\$password)") !== false);

$adminLogin = file_get_contents(APPPATH . 'controllers/admin/Login.php');
report('Admin login uses verify_user_password', strpos($adminLogin, 'verify_user_password') !== false);
report('Admin login no md5 where clause', strpos($adminLogin, 'md5($password)') === false);

$adminPhp = file_get_contents(APPPATH . 'controllers/admin/Admin.php');
report('Admin create uses hash_user_password', strpos($adminPhp, 'hash_user_password($password)') !== false);
report('Admin create no md5 password', strpos($adminPhp, 'md5($password)') === false);

report('password helper autoloaded', in_array('password', file(APPPATH . 'config/autoload.php'), true) || strpos(file_get_contents(APPPATH . 'config/autoload.php'), "'password'") !== false);

require APPPATH . 'config/database.php';
$c = $db['default'];
$m = new mysqli($c['hostname'], $c['username'], $c['password'], $c['database']);
if ($m->connect_error) {
	report('DB connection', false, $m->connect_error);
} else {
	report('DB connection', true);

	$testEmail = 'pwd_test_' . time() . '@example.test';
	$testHash = hash_user_password('LegacyMd5x1');
	$legacyMd5 = md5('legacyOld99');
	$now = time();

	$m->query("INSERT INTO user (name, email, password, phone, address, created) VALUES (
		'Pwd Test', '" . $m->real_escape_string($testEmail) . "', '" . $m->real_escape_string($testHash) . "', '0900000000', 'Test', {$now}
	)");
	$newId = (int) $m->insert_id;
	report('insert bcrypt test user', $newId > 0);

	if ($newId > 0) {
		$row = $m->query("SELECT password FROM user WHERE id={$newId}")->fetch_assoc();
		report('stored hash is bcrypt', strpos($row['password'], '$2y$') === 0 || strpos($row['password'], '$2a$') === 0);
		report('bcrypt user verify OK', verify_user_password('LegacyMd5x1', $row['password']) === true);
		$m->query("DELETE FROM user WHERE id={$newId}");
	}

	$legacyEmail = 'pwd_legacy_' . time() . '@example.test';
	$m->query("INSERT INTO user (name, email, password, phone, address, created) VALUES (
		'Legacy Test', '" . $m->real_escape_string($legacyEmail) . "', '" . $m->real_escape_string($legacyMd5) . "', '0900000001', 'Test', {$now}
	)");
	$legacyId = (int) $m->insert_id;
	report('insert legacy MD5 test user', $legacyId > 0);

	if ($legacyId > 0) {
		$row = $m->query("SELECT password FROM user WHERE id={$legacyId}")->fetch_assoc();
		$verifyLegacy = verify_user_password('legacyOld99', $row['password']);
		report('legacy MD5 user can authenticate', $verifyLegacy === 'rehash');
		if ($verifyLegacy === 'rehash') {
			$newHash = hash_user_password('legacyOld99');
			$m->query("UPDATE user SET password='" . $m->real_escape_string($newHash) . "' WHERE id={$legacyId}");
			$row2 = $m->query("SELECT password FROM user WHERE id={$legacyId}")->fetch_assoc();
			report('migrate MD5 to bcrypt on login simulation', strpos($row2['password'], '$2y$') === 0 || strpos($row2['password'], '$2a$') === 0);
			report('migrated hash verifies', verify_user_password('legacyOld99', $row2['password']) === true);
		}
		$m->query("DELETE FROM user WHERE id={$legacyId}");
	}
}

echo PHP_EOL . '--- Summary ---' . PHP_EOL;
$pass = 0;
$fail = 0;
foreach ($results as $r) {
	if ($r['pass']) {
		$pass++;
	} else {
		$fail++;
	}
}
echo "PASS: {$pass}  FAIL: {$fail}" . PHP_EOL;
exit($fail > 0 ? 1 : 0);
