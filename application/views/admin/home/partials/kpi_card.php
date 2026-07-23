<?php
defined('BASEPATH') OR exit('No direct script access allowed');
if (empty($kpi) || empty($login)) {
	return;
}
if (!admin_dashboard_kpi_allowed($kpi['key'], $login)) {
	return;
}
$tone = !empty($kpi['tone']) ? $kpi['tone'] : '';
$card_class = 'adm-dash-kpi' . ($tone !== '' ? ' adm-dash-kpi--' . $tone : '');
?>
<div class="col-6 col-xl-3">
	<div class="<?php echo htmlspecialchars($card_class, ENT_QUOTES, 'UTF-8'); ?>">
		<div class="adm-dash-kpi__label"><?php echo htmlspecialchars($kpi['label'], ENT_QUOTES, 'UTF-8'); ?></div>
		<div class="adm-dash-kpi__value"><?php echo admin_dashboard_format_kpi($kpi['value'], $kpi['format']); ?></div>
		<?php if (!empty($kpi['caption'])) { ?>
			<div class="adm-dash-kpi__caption"><?php echo htmlspecialchars($kpi['caption'], ENT_QUOTES, 'UTF-8'); ?></div>
		<?php } ?>
		<?php if (!empty($kpi['hint'])) { ?>
			<div class="adm-dash-kpi__hint"><?php echo htmlspecialchars($kpi['hint'], ENT_QUOTES, 'UTF-8'); ?></div>
		<?php } ?>
		<?php if (!empty($kpi['static_delta'])) { ?>
			<span class="adm-dash-kpi__delta adm-dash-kpi__delta--flat"><?php echo $kpi['key'] === 'low_stock' ? 'Theo tồn hiện tại' : 'Không theo kỳ lọc'; ?></span>
		<?php } else {
			echo admin_dashboard_kpi_delta_html($kpi['delta']);
		} ?>
	</div>
</div>
