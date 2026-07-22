<!DOCTYPE html>
<html lang="en">
<head>
	<?php $this->load->view('site/layouts/head', $this->data); ?>
</head>
<body>
	<?php $this->load->view('site/layouts/header', $this->data); ?>

	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 clearpadding" style="margin-top: 15px;">
				<?php $this->load->view('admin/message.php'); ?>

				<?php
				$jm_hide_sidebar = in_array($temp, array('site/cart/index', 'site/order/index', 'site/order/checkout_qr', 'site/user/index.php'), true);
				if (!$jm_hide_sidebar && $temp !== 'site/tuyen_dung_list' && $temp !== 'site/tuyen_dung_detail'):
				?>
					<?php $this->load->view('site/sidebar', $this->data); ?>
				<?php endif; ?>

				<?php $this->load->view($temp, $this->data); ?>

			</div>
		</div>
	</div>

	<?php $this->load->view('site/layouts/footer', $this->data); ?>
	<?php $this->load->view('site/layouts/scripts', $this->data); ?>
</body>
</html>
