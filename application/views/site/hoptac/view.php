<div class="container jm-content-page" style="padding: 30px 15px; max-width: 1200px; margin: 0 auto;">
    <div style="color: #999; font-size: 14px; margin-bottom: 20px;">
        <a href="<?php echo base_url(); ?>" style="color: #666; text-decoration: none;">Trang chủ</a> / 
        <a href="<?php echo base_url('hoptac'); ?>" style="color: #666; text-decoration: none;">Hợp tác kinh doanh</a> / 
        <span style="color: #333;"><?php echo $info->title; ?></span>
    </div>

    <div style="display: flex; flex-wrap: wrap; gap: 30px;">
        <div style="flex: 1; min-width: 300px; background: #fff; padding: 20px; border-radius: 4px;">
            <div style="color: #888; font-size: 13px; margin-bottom: 10px;">
                📅 Ngày đăng: <?php echo date('d/m/Y H:i', strtotime($info->created_at)); ?>
            </div>

            <h1 style="font-size: 26px; font-weight: bold; color: #333; margin: 0 0 20px 0; text-transform: uppercase; line-height: 1.4;">
                <?php echo $info->title; ?>
            </h1>

            <?php if(!empty($info->intro)): ?>
                <div style="font-style: italic; color: #555; background: #f9f9f9; padding: 15px; border-left: 4px solid #333; margin-bottom: 25px; line-height: 1.6; font-size: 15px;">
                    <?php echo $info->intro; ?>
                </div>
            <?php endif; ?>

            <div class="hoptac-content" style="font-size: 15px; color: #333; line-height: 1.8; white-space: pre-line;">
                <?php echo $info->content; ?>
            </div>

      <!--       <div style="margin-top: 40px; padding: 20px; background: #f5f5f5; border: 1px solid #ddd; border-radius: 4px;">
                <h3 style="margin: 0 0 10px 0; color: #222; font-size: 16px; font-weight: bold;">Liên hệ hợp tác phát triển:</h3>
                <p style="margin: 0; color: #555; font-size: 14px;">
                    Quý đối tác có nhu cầu liên kết, mở đại lý nhượng quyền vui lòng gửi đề xuất dự án qua Hotline ban quản trị hoặc Email phòng kinh doanh của thương hiệu <strong>Thời trang Ngọc Lan</strong>.
                </p>
            </div> -->
            
            <div style="margin-top: 30px;">
                <a href="<?php echo base_url('hoptac'); ?>" style="color: #222; text-decoration: none; font-weight: bold; font-size: 14px;">
                    &larr; Quay lại danh sách hợp tác
                </a>
            </div>
        </div>

        <div style="width: 380px; min-width: 300px;">
            <div style="position: sticky; top: 20px;">
                <img src="<?php echo base_url('upload/'.$info->image); ?>" alt="<?php echo $info->title; ?>" style="width: 100%; height: auto; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);" onerror="this.src='<?php echo base_url('upload/no-image.jpg'); ?>';">
            </div>
        </div>
    </div>
</div>