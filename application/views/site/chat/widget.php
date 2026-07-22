<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php if (!empty($ai_enabled) || !empty($staff_support_enabled)): ?>
<div id="support-chat-widget" class="support-chat-widget">
	<button type="button" id="support-chat-toggle" class="support-chat-toggle" aria-label="Mở hỗ trợ khách hàng" aria-expanded="false">
		<i class="fa-solid fa-comments" aria-hidden="true"></i>
	</button>

	<div id="support-chat-panel" class="support-chat-panel" hidden>
		<div class="support-chat-panel__header">
			<div class="support-chat-panel__avatar support-chat-panel__avatar--status" aria-hidden="true">
				<i class="fa-solid fa-robot"></i>
			</div>
			<div class="support-chat-panel__title">
				<strong>Hỗ trợ khách hàng</strong>
				<span id="support-chat-status" class="support-chat-panel__status">
					<span class="support-chat-panel__dot" aria-hidden="true"></span>
					<span id="support-chat-status-text">Trợ lý AI đang trực tuyến</span>
				</span>
			</div>
			<button type="button" id="support-chat-close" class="support-chat-panel__close" aria-label="Đóng hỗ trợ khách hàng">
				<i class="fa-solid fa-xmark" aria-hidden="true"></i>
			</button>
		</div>

		<div id="support-chat-messages" class="support-chat-panel__messages" role="log" aria-live="polite"></div>

		<div id="support-chat-quick-replies" class="support-chat-panel__quick-replies">
			<?php
			$quick_replies = isset($quick_replies) ? $quick_replies : array();
			foreach ($quick_replies as $reply) {
				$reply = (string) $reply;
				$cls = (mb_stripos($reply, 'nhân viên') !== false) ? ' support-chat-quick-reply--handoff' : '';
			?>
			<button type="button" class="support-chat-quick-reply<?php echo $cls; ?>" data-text="<?php echo htmlspecialchars($reply, ENT_QUOTES, 'UTF-8'); ?>">
				<?php echo htmlspecialchars($reply, ENT_QUOTES, 'UTF-8'); ?>
			</button>
			<?php } ?>
		</div>

		<form id="support-chat-form" class="support-chat-panel__form">
			<input type="text" id="support-chat-input" class="support-chat-panel__input" placeholder="Nhập tin nhắn..." autocomplete="off" maxlength="500">
			<button type="submit" class="support-chat-panel__send" aria-label="Gửi">
				<i class="fa-solid fa-paper-plane" aria-hidden="true"></i>
			</button>
		</form>
	</div>
</div>

<?php
	$json_flags = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE;
?>
<script>
window.__SUPPORT_CHAT_CONFIG__ = {
	sendUrl: <?php echo json_encode(base_url('ai-assistant/send'), JSON_UNESCAPED_UNICODE); ?>,
	historyUrl: <?php echo json_encode(base_url('ai-assistant/history'), JSON_UNESCAPED_UNICODE); ?>,
	pollUrl: <?php echo json_encode(base_url('ai-assistant/poll'), JSON_UNESCAPED_UNICODE); ?>,
	welcomeMessage: <?php echo json_encode((string) $welcome_message, $json_flags); ?>,
	welcomeIntro: <?php echo json_encode((string) $welcome_intro, $json_flags); ?>,
	pollIntervalMs: <?php echo (int) (isset($poll_interval_ms) ? $poll_interval_ms : 1500); ?>
};
</script>
<link rel="stylesheet" href="<?php echo site_asset_url('css/ai-chat.css'); ?>">
<script src="<?php echo site_asset_url('js/ai-chat.js'); ?>" defer></script>
<?php endif; ?>
