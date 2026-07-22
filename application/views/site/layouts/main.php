<!DOCTYPE html>
<html lang="en">
<head>
	<?php $this->load->view('site/layouts/head', $this->data); ?>
</head>
<body>

	<?php $this->load->view('site/layouts/header', $this->data); ?>

	<div class="container">
		<?php $this->load->view('site/layouts/slider', $this->data); ?>
		<?php $this->load->view($temp, $this->data); ?>
	</div>

	<?php $this->load->view('site/layouts/footer', $this->data); ?>

	<?php $this->load->view('site/layouts/scripts', $this->data); ?>
</body>
</html>
