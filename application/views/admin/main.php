<!DOCTYPE html>
<html lang="vi">
<head>
	<?php $this->load->view('admin/head.php'); ?>
</head>
<body class="admin-app">
	<?php $this->load->view('admin/sidebar.php'); ?>
	<?php $this->load->view('admin/header.php'); ?>

	<main class="admin-main" id="main-content-body">
		<div class="admin-main-inner container-fluid">
			<?php $this->load->view('admin/message.php'); ?>
			<?php $this->load->view($temp, $this->data); ?>
		</div>
	</main>

	<?php $this->load->view('admin/footer.php'); ?>
</body>
</html>
