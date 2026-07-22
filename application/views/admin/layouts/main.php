<!DOCTYPE html>
<html lang="vi">
<head>
	<?php $this->load->view('admin/layouts/head'); ?>
</head>
<body class="admin-app">
<script>
(function () {
	try {
		if (localStorage.getItem('webshop-admin-theme') === 'dark') {
			document.body.classList.add('admin-dark');
			document.body.setAttribute('data-bs-theme', 'dark');
		} else {
			document.body.setAttribute('data-bs-theme', 'light');
		}
	} catch (e) {}
})();
</script>
	<?php $this->load->view('admin/layouts/sidebar'); ?>
	<?php $this->load->view('admin/layouts/header'); ?>

	<main class="admin-main" id="main-content-body">
		<div class="admin-main-inner container-fluid">
			<?php $this->load->view('admin/message.php'); ?>
			<?php $this->load->view($temp, $this->data); ?>
		</div>
	</main>

	<?php $this->load->view('admin/layouts/footer'); ?>
	<?php $this->load->view('admin/layouts/scripts'); ?>
</body>
</html>
