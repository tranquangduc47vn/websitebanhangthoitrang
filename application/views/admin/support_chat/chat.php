<?php
$conversation = isset($conversation) ? $conversation : null;
$messages = isset($messages) ? $messages : array();
$staff_id = isset($staff_id) ? (int) $staff_id : 0;
$staff_name = isset($staff_name) ? (string) $staff_name : 'Nhân viên';
require_once APPPATH . 'services/support/SupportChatService.php';
$svc = new SupportChatService();
$status = $conversation ? $svc->normalizeStatus($conversation->status) : 'ai_active';
$conv_id = $conversation ? (int) $conversation->id : 0;
?>

<div class="admin-breadcrumb">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo admin_url('home'); ?>">Trang chủ</a></li>
			<li class="breadcrumb-item"><a href="<?php echo admin_url('support-chat'); ?>">Hỗ trợ khách hàng</a></li>
			<li class="breadcrumb-item active">Hội thoại #<?php echo $conv_id; ?></li>
		</ol>
	</nav>
</div>

<div class="row g-3">
	<div class="col-lg-8">
		<div class="admin-card support-admin-chat">
			<div class="admin-card-body p-0 d-flex flex-column" style="min-height: 520px;">
				<div class="support-admin-chat__header px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
					<div>
						<strong>Hội thoại #<?php echo $conv_id; ?></strong>
						<span class="badge bg-secondary ms-2" id="support-admin-status"><?php echo htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?></span>
					</div>
					<div class="btn-group btn-group-sm">
						<button type="button" class="btn btn-success" id="support-admin-take" <?php
							$conv_staff_id = isset($conversation->staff_id) ? (int) $conversation->staff_id : 0;
							echo ($status === 'staff_joined' && $conv_staff_id === $staff_id) ? 'disabled' : '';
						?>>
							Nhận cuộc trò chuyện
						</button>
						<button type="button" class="btn btn-outline-secondary" id="support-admin-return-ai">Trả lại AI</button>
						<button type="button" class="btn btn-outline-danger" id="support-admin-end">Kết thúc</button>
					</div>
				</div>

				<div id="support-admin-messages" class="support-admin-chat__messages flex-grow-1 p-3">
					<?php foreach ($messages as $m) {
						$sender = $svc->normalizeSender($m->sender);
						if ($sender === 'system') { continue; }
						$meta = json_decode((string) $m->meta, true);
						$sname = is_array($meta) && isset($meta['staff_name']) ? $meta['staff_name'] : '';
					?>
					<div class="support-admin-msg support-admin-msg--<?php echo htmlspecialchars($sender, ENT_QUOTES, 'UTF-8'); ?>" data-msg-id="<?php echo (int) $m->id; ?>">
						<div class="support-admin-msg__bubble"><?php echo nl2br(htmlspecialchars($m->content, ENT_QUOTES, 'UTF-8')); ?></div>
						<div class="support-admin-msg__meta small text-muted">
							<?php echo htmlspecialchars($sender === 'staff' ? $sname : $sender, ENT_QUOTES, 'UTF-8'); ?>
							· <?php echo date('H:i d/m', (int) $m->created); ?>
						</div>
					</div>
					<?php } ?>
				</div>

				<form id="support-admin-form" class="support-admin-chat__form border-top p-3">
					<input type="hidden" id="support-admin-conversation-id" value="<?php echo $conv_id; ?>">
					<div class="input-group">
						<input type="text" id="support-admin-input" class="form-control" placeholder="<?php echo ($status === 'waiting_staff') ? 'Gõ tin và bấm Gửi — hệ thống tự nhận cuộc trò chuyện' : 'Nhập tin nhắn...'; ?>" maxlength="1000" autocomplete="off">
						<button type="submit" class="btn btn-primary">Gửi</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="col-lg-4">
		<div class="admin-card">
			<div class="admin-card-body">
				<h2 class="h6 fw-bold">Thông tin khách</h2>
				<?php if (!empty($customer)) { ?>
					<p class="mb-1"><strong><?php echo htmlspecialchars($customer->name, ENT_QUOTES, 'UTF-8'); ?></strong></p>
					<p class="small text-muted mb-0"><?php echo htmlspecialchars($customer->email, ENT_QUOTES, 'UTF-8'); ?></p>
				<?php } else { ?>
					<p class="small text-muted mb-0">Khách chưa đăng nhập (guest token).</p>
				<?php } ?>
				<hr>
				<p class="small mb-0">Nhân viên phụ trách: <strong><?php echo htmlspecialchars(isset($conversation->staff_name) ? (string) $conversation->staff_name : '—', ENT_QUOTES, 'UTF-8'); ?></strong></p>
			</div>
		</div>
	</div>
</div>

<link rel="stylesheet" href="<?php echo base_url('assets/admin/css/support-chat.css'); ?>">
<script>
window.__SUPPORT_ADMIN_CHAT__ = {
	conversationId: <?php echo $conv_id; ?>,
	staffId: <?php echo $staff_id; ?>,
	status: <?php echo json_encode($status, JSON_UNESCAPED_UNICODE); ?>,
	lastMessageId: <?php
		$lastId = 0;
		foreach ($messages as $m) { if ((int) $m->id > $lastId) { $lastId = (int) $m->id; } }
		echo $lastId;
	?>,
	pollUrl: <?php echo json_encode(admin_url('support-chat/poll'), JSON_UNESCAPED_UNICODE); ?>,
	sendUrl: <?php echo json_encode(admin_url('support-chat/send'), JSON_UNESCAPED_UNICODE); ?>,
	takeUrl: <?php echo json_encode(admin_url('support-chat/take/' . $conv_id), JSON_UNESCAPED_UNICODE); ?>,
	endUrl: <?php echo json_encode(admin_url('support-chat/end/' . $conv_id), JSON_UNESCAPED_UNICODE); ?>,
	returnAiUrl: <?php echo json_encode(admin_url('support-chat/return_ai/' . $conv_id), JSON_UNESCAPED_UNICODE); ?>,
	pollIntervalMs: 1500
};
</script>
<script src="<?php echo base_url('assets/admin/js/support-chat.js'); ?>"></script>
