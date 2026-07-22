<?php
/** Shared admin form page shell — include only inside admin/main layout */
if (!isset($admin_form_title)) $admin_form_title = 'Form';
if (!isset($admin_form_breadcrumb)) $admin_form_breadcrumb = '';
if (!isset($admin_form_back_url)) $admin_form_back_url = admin_url('home');
?>
<div class="admin-breadcrumb">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item">
				<a href="<?php echo admin_url('home'); ?>"><i class="fa-solid fa-house me-1"></i> Trang chủ</a>
			</li>
			<?php if (!empty($admin_form_breadcrumb)) { ?>
				<li class="breadcrumb-item"><a href="<?php echo $admin_form_back_url; ?>"><?php echo htmlspecialchars($admin_form_breadcrumb, ENT_QUOTES, 'UTF-8'); ?></a></li>
			<?php } ?>
			<li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($admin_form_title, ENT_QUOTES, 'UTF-8'); ?></li>
		</ol>
	</nav>
</div>

<div class="admin-card">
	<div class="admin-card-body">
