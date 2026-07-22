<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<div class="gt-wrapper-fix" style="padding: 20px 0;">

    <div class="gt-main-content" style="display: flex; gap: 30px;">
        
        <div class="gt-col-right" style="flex: 1;">
            <h1 style="font-size: 26px; font-weight: bold; margin-bottom: 30px; color: #111; text-transform: uppercase; border-bottom: 2px solid #222; padding-bottom: 10px;">
                <?php echo $page_title; ?>
            </h1>
            
            <div class="gt-news-grid" style="display: flex; gap: 3%; flex-wrap: wrap; width: 100%;">
                
                <?php if(!empty($list_news)): ?>
                    <?php foreach($list_news as $row): ?>
                        
                        <?php $url_news = build_news_post_url(is_array($row) ? (int) $row['id'] : $row); ?>
                        
                        <div class="gt-news-card" style="flex: 0 0 31.3%; min-width: 280px; max-width: 31.3%; box-sizing: border-box; margin-bottom: 40px;">
                            
                            <a href="<?php echo $url_news; ?>" style="display: block; width: 100%; height: 210px; overflow: hidden; border-radius: 4px; border: 1px solid #f0f0f0; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                                <img src="<?php echo base_url('upload/news/'.($row['image_link'] ?? ($row['image'] ?? ''))); ?>" 
                                     alt="<?php echo $row['title']; ?>" 
                                     style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;"
                                     onmouseover="this.style.transform='scale(1.04)'"
                                     onmouseout="this.style.transform='scale(1)'"
                                     onerror="this.src='<?php echo base_url('upload/no-image.jpg'); ?>';">
                            </a>
                            
                            <p style="color: #999; font-size: 12px; margin: 12px 0 6px 0;">
                                <i class="fa fa-calendar-o" style="margin-right: 4px;"></i>
                                <?php echo isset($row['created']) ? date('d/m/Y', strtotime($row['created'])) : '11/06/2026'; ?>
                            </p>
                            
                            <h3 style="margin: 0 0 8px 0; font-size: 16px; line-height: 1.4; font-weight: bold;">
                                <a href="<?php echo $url_news; ?>" style="color: #222; text-decoration: none; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    <?php echo $row['title']; ?>
                                </a>
                            </h3>
                            
                            <p style="font-size: 13px; color: #666; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin: 0;">
                                <?php echo isset($row['intro']) ? strip_tags($row['intro']) : (isset($row['content']) ? strip_tags($row['content']) : ''); ?>
                            </p>
                            
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: #888; font-style: italic; width: 100%;">Hiện tại hệ thống chưa cập nhật bài viết nào.</p>
                <?php endif; ?>

            </div>
        </div>

        <div class="gt-col-left" style="width: 250px; flex-shrink: 0;">
    <h4 style="font-size: 16px; font-weight: bold; margin-bottom: 15px; text-transform: uppercase; color: #333;">Có thể bạn quan tâm</h4>
    <div class="gt-banner" style="width: 100%; border: 1px solid #eee; border-radius: 4px; overflow: hidden;">
        
        <a href="<?php echo base_url(); ?>" style="display: block; width: 100%; text-decoration: none;">
            <img src="<?php echo base_url('upload/banner_sidebar.jpg'); ?>" 
                 alt="New Arrival" 
                 style="width: 100%; height: auto; display: block; cursor: pointer; transition: opacity 0.2s ease;"
                 onmouseover="this.style.opacity='0.9'"
                 onmouseout="this.style.opacity='1'">
        </a>
        
    </div>
</div>

    </div>
</div>