<?php
$site_asset_url = site_asset_url('');
?>
	<meta charset="UTF-8">
	<title>Shop quần áo Ngọc Lan</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<script src="<?php echo public_url(); ?>js/jquery-3.1.1.js" type="text/javascript"></script>
	<script src="<?php echo public_url('js/jqzoom_ev'); ?>js/jquery.jcarousel.pack.js" type="text/javascript"></script>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="stylesheet" type="text/css" href="<?php echo $site_asset_url; ?>css/typography-luxury.css?v=1">
	<link rel="stylesheet" type="text/css" href="<?php echo $site_asset_url; ?>bootstrap/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $site_asset_url; ?>css/style.css?v=20260722c">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
	<link rel="stylesheet" type="text/css" href="<?php echo $site_asset_url; ?>css/layout-chrome-2026.css?v=10">
	<?php if (!empty($canonical_url)) { ?>
	<link rel="canonical" href="<?php echo htmlspecialchars($canonical_url, ENT_QUOTES, 'UTF-8'); ?>">
	<?php } ?>
	<script type="text/javascript" src="<?php echo public_url('js/raty/jquery.raty.min.js') ?>"></script>
	<script type="text/javascript">
      $(function() {
         $.fn.raty.defaults.path = "<?php echo public_url('js/raty/img'); ?>";
         $('.raty').raty({
          	  score: function() {
          	    return $(this).attr('data-score');
          	  },
              readOnly  : true,
          });
      });
     </script>
     <style>.raty img{width:16px !important;height:16px; !important;}</style>
