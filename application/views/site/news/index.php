<div class="col-xs-12 col-sm-9 col-md-9 col-lg-9">
	<div class="panel panel-info">
		<div class="panel-heading">
			<h1 class="panel-title">Tin tức</h1>
		</div>
		<div class="panel-body">
			<?php if (!empty($product_list)) { ?>
				<div class="list-group">
					<?php foreach ($product_list as $item) { ?>
						<a class="list-group-item" href="<?php echo build_news_post_url($item); ?>">
							<?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?>
						</a>
					<?php } ?>
				</div>
				<?php echo $this->pagination->create_links(); ?>
			<?php } else { ?>
				<p class="text-muted mb-0">Hiện chưa có tin tức.</p>
			<?php } ?>
		</div>
	</div>
</div>
