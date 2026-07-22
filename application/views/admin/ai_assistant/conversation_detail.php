<div class="admin-breadcrumb">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo admin_url('home'); ?>"><i class="fa-solid fa-house me-1"></i> Trang chủ</a></li>
			<li class="breadcrumb-item"><a href="<?php echo admin_url('ai-assistant/conversations'); ?>">Hội thoại trợ lý AI</a></li>
			<li class="breadcrumb-item active" aria-current="page">#<?php echo (int) $conversation->id; ?></li>
		</ol>
	</nav>
</div>

<div class="admin-card mb-3">
	<div class="admin-card-body">
		<p class="mb-1"><strong>Khách hàng:</strong>
			<?php if (!empty($customer)) { ?>
				<?php echo htmlspecialchars($customer->name, ENT_QUOTES, 'UTF-8'); ?> (<?php echo htmlspecialchars($customer->email, ENT_QUOTES, 'UTF-8'); ?>)
			<?php } elseif ((int) $conversation->user_id > 0) { ?>
				Khách #<?php echo (int) $conversation->user_id; ?>
			<?php } else { ?>
				Khách vãng lai
			<?php } ?>
		</p>
		<p class="mb-1"><strong>Trạng thái:</strong> <?php echo htmlspecialchars($conversation->status, ENT_QUOTES, 'UTF-8'); ?></p>
		<p class="mb-0"><strong>Bắt đầu:</strong> <?php echo date('d/m/Y H:i', (int) $conversation->started); ?> — <strong>Cập nhật cuối:</strong> <?php echo date('d/m/Y H:i', (int) $conversation->last_message); ?></p>
	</div>
</div>

<div class="admin-card">
	<div class="admin-card-body">
		<?php if (!empty($messages)) { ?>
			<?php foreach ($messages as $m) { ?>
				<div class="mb-3 pb-2 border-bottom">
					<div class="d-flex justify-content-between">
						<strong><?php echo $m->sender === 'user' ? 'Khách hàng' : ($m->sender === 'ai' ? 'Trợ lý AI' : 'Hệ thống'); ?></strong>
						<span class="text-muted small"><?php echo date('d/m/Y H:i:s', (int) $m->created); ?></span>
					</div>
					<div><?php echo nl2br(htmlspecialchars($m->content, ENT_QUOTES, 'UTF-8')); ?></div>
				</div>
			<?php } ?>
		<?php } else { ?>
			<p class="admin-empty">Chưa có tin nhắn nào.</p>
		<?php } ?>
	</div>
</div>
