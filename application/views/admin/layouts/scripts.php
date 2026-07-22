<?php
$admin_asset_url = base_url('assets/admin/');
$admin_footer_minimal = !empty($admin_footer_minimal);
?>
<?php if (!$admin_footer_minimal) { ?>
<script src="<?php echo $admin_asset_url; ?>js/jquery-3.1.1.js"></script>
<?php if (!empty($admin_load_chart)) { ?>
<script src="<?php echo $admin_asset_url; ?>js/chart.min.js"></script>
<?php } ?>
<script src="<?php echo $admin_asset_url; ?>js/bootstrap-datepicker.js"></script>
<?php } ?>
<script src="<?php echo $admin_asset_url; ?>js/bootstrap5.bundle.min.js"></script>
<script src="<?php echo $admin_asset_url; ?>js/admin-layout.js?v=5"></script>
