<?php
$admin_name = isset($login->name) ? $login->name : 'Admin';
$avatar_char = strtoupper(substr($admin_name, 0, 1));
$login_id = isset($login->id) ? $login->id : 0;
$header_page_title = (!empty($admin_header_title))
	? $admin_header_title
	: admin_header_title();
?>

<header class="admin-header">
	<div class="admin-header-inner">
		<div class="admin-header-brand">
			<button class="btn admin-btn-icon d-lg-none" type="button"
				data-bs-toggle="offcanvas" data-bs-target="#adminSidebar"
				aria-controls="adminSidebar" aria-label="Mở menu">
				<i class="fa-solid fa-bars"></i>
			</button>

			<div class="lh-sm admin-header-page-title min-w-0 flex-grow-1">
				<span class="admin-brand-text text-truncate d-block"><?php echo htmlspecialchars($header_page_title, ENT_QUOTES, 'UTF-8'); ?></span>
			</div>
		</div>

		<div class="admin-header-actions">
			<button type="button" class="btn admin-btn-icon" id="adminThemeToggle"
				aria-pressed="false" title="Bật dark mode">
				<i class="fa-solid fa-moon" data-theme-icon aria-hidden="true"></i>
			</button>

			<a class="admin-user-link" href="<?php echo admin_url('admin/edit/' . $login_id); ?>">
				<span class="admin-avatar"><?php echo $avatar_char; ?></span>
				<span class="admin-user-meta d-none d-md-flex">
					<small>Xin chào</small>
					<strong><?php echo htmlspecialchars($admin_name, ENT_QUOTES, 'UTF-8'); ?></strong>
				</span>
			</a>

			<a class="btn btn-sm btn-primary admin-logout-btn rounded-pill px-3" id="logout"
				href="<?php echo admin_url('admin/logout'); ?>">
				<i class="fa-solid fa-right-from-bracket me-1"></i>
				<span class="d-none d-sm-inline">Đăng xuất</span>
			</a>
		</div>
	</div>
</header>
