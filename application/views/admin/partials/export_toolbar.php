<?php
$export_module = isset($export_module) ? $export_module : '';
$export_query = isset($export_query) ? $export_query : '';
$base = admin_url('export');
$q = $export_query !== '' ? '?' . $export_query : '';
$qAll = $export_query !== '' ? '?' . $export_query . '&scope=all' : '?scope=all';
$qPage = $export_query !== '' ? '?' . $export_query . '&scope=page' : '?scope=page';
$is_dashboard = ($export_module === 'dashboard');
?>
<div class="admin-export-toolbar d-flex flex-wrap align-items-center gap-2 mb-3">
	<span class="text-muted small me-1"><i class="fa-solid fa-file-export me-1"></i><?php echo $is_dashboard ? 'Báo cáo PDF:' : 'Xuất báo cáo:'; ?></span>
	<?php if (!$is_dashboard) { ?>
	<a class="btn btn-sm btn-success" href="<?php echo $base . '/excel/' . rawurlencode($export_module) . $qAll; ?>"><i class="fa-solid fa-file-excel me-1"></i>Excel (toàn bộ)</a>
	<a class="btn btn-sm btn-outline-success" href="<?php echo $base . '/excel/' . rawurlencode($export_module) . $qPage; ?>">Excel (trang này)</a>
	<?php } ?>
	<a class="btn btn-sm btn-danger" href="<?php echo $base . '/pdf/' . rawurlencode($export_module) . $qAll; ?>"><i class="fa-solid fa-file-pdf me-1"></i>PDF<?php echo $is_dashboard ? ' báo cáo tổng quan' : ''; ?></a>
	<a class="btn btn-sm btn-outline-secondary" href="<?php echo $base . '/print_report/' . rawurlencode($export_module) . $qAll; ?>" target="_blank" rel="noopener"><i class="fa-solid fa-print me-1"></i>In báo cáo</a>
</div>
