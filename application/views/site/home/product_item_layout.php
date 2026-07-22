<div class="col-xs-12 col-sm-6 col-md-4 col-lg-2" style="margin-bottom: 20px;">
    <div class="thumbnail text-center" style="position: relative; padding: 10px; border-radius: 4px; min-height: 320px;">
        
        <a href="<?php echo build_product_url($product_data); ?>">
            <img src="<?php echo base_url('upload/product/'.$product_data->image_link); ?>" 
                 alt="<?php echo htmlspecialchars(product_display_name($product_data->name), ENT_QUOTES, 'UTF-8'); ?>" 
                 style="max-height: 150px; object-fit: contain; margin-bottom: 10px;">
        </a>
        
        <div class="caption" style="padding: 0;">
            <h4 style="font-size: 14px; height: 36px; overflow: hidden; font-weight: bold; margin: 5px 0;">
                <a href="<?php echo build_product_url($product_data); ?>" style="color: #333; text-decoration: none;">
                    <?php echo product_display_name($product_data->name); ?>
                </a>
            </h4>
            
            <p style="margin-bottom: 5px;">
                <?php if($product_data->discount > 0): ?>
                    <?php $price_new = $product_data->price - $product_data->discount; ?>
                    <strong style="color: #e51f28; font-size: 15px;"><?php echo number_format($price_new); ?> đ</strong>
                    <br>
                    <del style="color: #999; font-size: 12px;"><?php echo number_format($product_data->price); ?> đ</del>
                <?php else: ?>
                    <strong style="color: #333; font-size: 15px;"><?php echo number_format($product_data->price); ?> đ</strong>
                    <br>&nbsp;
                <?php endif; ?>
            </p>
            
            <div style="font-size: 11px; color: #777; margin-bottom: 10px;">
                <span>👁️ <?php echo $product_data->view; ?> xem</span> | 
                <span>🛒 Đã bán: <?php echo $product_data->buyed; ?></span>
            </div>

            <a href="<?php echo site_url('gio-hang/them/' . (int) $product_data->id); ?>" class="btn btn-sm btn-danger btn-block">
                <span class="glyphicon glyphicon-shopping-cart"></span> Mua ngay
            </a>
        </div>
    </div>
</div>