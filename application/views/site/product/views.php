<div class="col-xs-12 col-sm-9 col-md-9 col-lg-9 clearpaddingr">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 clearpadding">

		<div class="panel panel-info">
		  <div class="panel-heading">
		    <h3 class="panel-title">Sản phẩm xem nhiều</h3>
		  </div>
		  <div class="panel-body">
		  	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 clearpadding">
		  		<?php foreach ($product_list as $value) { ?>
					<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 re-padding">
			  			<div class="product_item">
			  				<p class="product_name" ><a href="<?php echo build_product_url($value); ?>" ><?php echo product_display_name($value->name); ?></a></p>
			  				<div class="product-image">
			  					<a href="<?php echo build_product_url($value); ?>"><img src="<?php echo base_url(); ?>upload/product/<?php echo $value->image_link; ?>" alt="" class=""></a>
			  				</div>
			  				<?php if ($value->discount>0) { 
			  					$new_price = $value->price - $value->discount; ?>
			  					<p><span class='price text-right'><?php echo number_format($new_price); ?> VNĐ</span> <del class="product-discount"><?php echo number_format($value->price); ?> VNĐ</del></p>
			  				<?php }else{ ?>
								<p><span class='price text-right'><?php echo number_format($value->price); ?> VNĐ</span></p>
			  				<?php	} ?>
							<p><span class="glyphicon glyphicon-eye-open" aria-hidden="true" title="Số lượt xem"></span> <?php echo $value->view; ?> <span class="glyphicon glyphicon-star-empty" aria-hidden="true" title="Số lượng đặt mua"><?php echo $value->buyed; ?></p>
							<a href="<?php echo site_url('gio-hang/them/' . (int) $value->id); ?>"><button class='btn btn-info'><span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> Thêm giỏ hàng</button></a>
			  			</div>
					</div>
				<?php } ?>	
		  	</div>
			 <?php echo $this->pagination->create_links(); ?>
		  </div>
		</div>
		
	</div>
</div>