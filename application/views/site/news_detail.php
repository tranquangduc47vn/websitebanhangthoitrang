<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<div class="gt-wrapper-fix">

    <div class="gt-main-content">
        
<div class="gt-col-left">
    <h4>Có thể bạn quan tâm</h4>
    <div class="gt-banner">
        <a href="<?php echo base_url(); ?>" style="display: block;">
            <img src="<?php echo base_url('upload/banner_sidebar.jpg'); ?>" alt="New Arrival" style="cursor: pointer;">
        </a>
    </div>
</div>
        <div class="gt-col-right">
            <h1><?php echo $post->title; ?></h1>
            <p class="date"><?php echo isset($post->created) ? date('d/m/Y H:i', strtotime($post->created)) : '11/06/2026'; ?></p>
            
            <ul class="gt-share-box" style="padding-left: 0; margin-top: 0;">
                <span class="label">Chia sẻ trên:</span>
                <li><a href="#" class="tw"><i class="fa fa-twitter"></i></a></li>
                <li><a href="#" class="fb"><i class="fa fa-facebook"></i></a></li>
                <li><a href="#" class="gp"><i class="fa fa-google-plus"></i></a></li>
                <li><a href="#" class="in"><i class="fa fa-instagram"></i></a></li>
                <li><a href="#" class="yt"><i class="fa fa-youtube-play"></i></a></li>
            </ul>
            
            <hr class="gt-divider">

            <div class="gt-detail-text" style="line-height: 1.6; font-size: 15px; color: #333;">
                <?php if(!empty($post->image_link)): ?>
                    <div style="text-align: center; margin-bottom: 20px;">
                        <img src="<?php echo base_url('upload/news/'.$post->image_link); ?>" alt="<?php echo $post->title; ?>" style="max-width: 100%; height: auto; border-radius: 4px;">
                    </div>
                <?php endif; ?>
                
                <p style="font-weight: bold; font-size: 16px; margin-bottom: 20px; color: #555;"><?php echo $post->intro; ?></p>
                
                <div><?php echo $post->content; ?></div>
            </div>
        </div>
        
    </div>

    <div class="gt-other-news-section" style="margin-top: 60px; border-top: 1px solid #eee; padding-top: 40px; clear: both;">
        <h2 style="font-size: 22px; font-weight: bold; margin-bottom: 25px; color: #111;">Các tin tức khác</h2>
        
        <div class="gt-news-grid" style="display: flex; gap: 3%; flex-wrap: wrap; width: 100%;">
            
            <?php if(!empty($other_news)): ?>
                <?php foreach($other_news as $row): ?>
                    <?php $url_news = build_news_post_url($row); ?>
                    
                    <div class="gt-news-card" style="flex: 0 0 31.3%; min-width: 280px; max-width: 31.3%; box-sizing: border-box; margin-bottom: 35px;">
                        <a href="<?php echo $url_news; ?>" style="display: block; width: 100%; height: 200px; overflow: hidden; border-radius: 4px; border: 1px solid #f0f0f0;">
                            <img src="<?php echo base_url('upload/news/'.$row->image_link); ?>" 
                                 alt="<?php echo $row->title; ?>" 
                                 style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;"
                                 onmouseover="this.style.transform='scale(1.04)'"
                                 onmouseout="this.style.transform='scale(1)'"
                                 onerror="this.src='<?php echo base_url('upload/no-image.jpg'); ?>';">
                        </a>
                        <p style="color: #999; font-size: 12px; margin: 12px 0 6px 0;">
                            <i class="fa fa-calendar-o" style="margin-right: 4px;"></i>
                            <?php echo isset($row->created) ? date('d/m/Y H:i', strtotime($row->created)) : '11/06/2026'; ?>
                        </p>
                        <h4 style="margin: 0; font-size: 15px; line-height: 1.4; font-weight: bold;">
                            <a href="<?php echo $url_news; ?>" style="color: #222; text-decoration: none; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                <?php echo $row->title; ?>
                            </a>
                        </h4>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: #888; font-style: italic;">Chưa có dữ liệu tin tức nào khác để hiển thị.</p>
            <?php endif; ?>

        </div>
    </div>

</div>