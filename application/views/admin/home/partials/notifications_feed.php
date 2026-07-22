<?php
if (empty($notify_groups) || !is_array($notify_groups)) {
	return;
}
?>
<ul class="adm-notify-rows">
	<?php foreach ($notify_groups as $group) {
		$tone = isset($group['tone']) ? $group['tone'] : 'info';
		$lines = isset($group['lines']) ? $group['lines'] : array();
		$action_label = isset($group['action_label']) ? $group['action_label'] : 'Xem chi tiết';
		$action_url = isset($group['action_url']) ? $group['action_url'] : admin_url('home');
		$btn_class = ($action_label === 'Xử lý') ? 'btn-primary' : 'btn-outline-primary';
		foreach ($lines as $line) {
	?>
	<li class="adm-notify-row adm-notify-row--<?php echo htmlspecialchars($tone, ENT_QUOTES, 'UTF-8'); ?>">
		<span class="adm-notify-row__icon" aria-hidden="true"><i class="<?php echo htmlspecialchars($group['icon'], ENT_QUOTES, 'UTF-8'); ?>"></i></span>
		<span class="adm-notify-row__group"><?php echo htmlspecialchars($group['title'], ENT_QUOTES, 'UTF-8'); ?></span>
		<span class="adm-notify-row__text"><?php echo htmlspecialchars($line, ENT_QUOTES, 'UTF-8'); ?></span>
		<a class="btn btn-sm <?php echo $btn_class; ?> adm-notify-row__action" href="<?php echo htmlspecialchars($action_url, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($action_label, ENT_QUOTES, 'UTF-8'); ?></a>
	</li>
	<?php }
	} ?>
</ul>
