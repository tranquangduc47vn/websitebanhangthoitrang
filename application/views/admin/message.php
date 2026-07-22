<?php
$msg_success = $this->session->flashdata('message_success');
$msg_fail = $this->session->flashdata('message_fail');
$msg_info = $this->session->flashdata('message');
?>
<div class="admin-flash-stack">
	<?php if ($msg_success) { ?>
		<div class="alert alert-success alert-dismissible fade show" role="alert" data-admin-auto-dismiss="5000">
			<i class="fa-solid fa-circle-check me-2"></i>
			<?php echo $msg_success; ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" data-admin-dismiss="alert" aria-label="Đóng"></button>
		</div>
	<?php } ?>
	<?php if ($msg_fail) { ?>
		<div class="alert alert-danger alert-dismissible fade show" role="alert" data-admin-auto-dismiss="5000">
			<i class="fa-solid fa-triangle-exclamation me-2"></i>
			<?php echo $msg_fail; ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" data-admin-dismiss="alert" aria-label="Đóng"></button>
		</div>
	<?php } ?>
	<?php if (!$msg_success && !$msg_fail && $msg_info) { ?>
		<div class="alert alert-success alert-dismissible fade show" role="alert" data-admin-auto-dismiss="5000">
			<i class="fa-solid fa-circle-check me-2"></i>
			<?php echo $msg_info; ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" data-admin-dismiss="alert" aria-label="Đóng"></button>
		</div>
	<?php } ?>
</div>
