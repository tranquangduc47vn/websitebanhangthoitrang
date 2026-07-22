<?php
$is_admin_login = ($this->uri->segment(1) === 'admin' && $this->uri->segment(2) === 'login');
$admin_asset_url = base_url('assets/admin/');
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
<title><?php
if ($is_admin_login) {
	echo 'Đăng nhập quản trị';
} else {
	$page_title = !empty($admin_header_title) ? $admin_header_title : admin_header_title();
	echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') . ' · Webshop Admin';
}
?></title>

<link href="<?php echo $admin_asset_url; ?>css/bootstrap5.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
<link href="<?php echo $admin_asset_url; ?>css/admin-theme.css?v=5" rel="stylesheet">
<link href="<?php echo $admin_asset_url; ?>css/admin-dark-mode.css?v=1" rel="stylesheet">
<?php if (!$is_admin_login) { ?>
<link href="<?php echo $admin_asset_url; ?>css/datepicker3.css" rel="stylesheet">
<script src="<?php echo $admin_asset_url; ?>vendor/ckeditor/ckeditor.js"></script>
<?php } ?>
