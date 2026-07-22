<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<div class="jm-recruitment-container jm-content-page" style="padding: 40px 0; max-width: 1200px; margin: 0 auto; background-color: #fff;">

    <div class="jm-recruitment-wrapper" style="width: 100%;">
        
        <?php if(!empty($list_tuyendung)): ?>
            
            <?php 
                $first_job = array_shift($list_tuyendung); 
                $url_first_job = base_url('tuyendung/view/'.$first_job['slug']);
            ?>
            <div class="jm-featured-job" style="display: flex; align-items: center; justify-content: space-between; gap: 5%; margin-bottom: 60px; padding-bottom: 60px; border-bottom: 1px solid #eee;">
                
                <div class="jm-featured-info" style="flex: 0 0 45%; display: flex; flex-direction: column; justify-content: center;">
                    <span style="color: #888; font-size: 13px; margin-bottom: 15px; letter-spacing: 0.5px;">
                        <?php echo isset($first_job['created_at']) ? date('d/m/Y H:i', strtotime($first_job['created_at'])) : '16/06/2026 12:00'; ?>
                    </span>
                    <h2 style="margin: 0 0 20px 0; font-size: 26px; font-weight: 500; line-height: 1.3; text-transform: uppercase; letter-spacing: 0.5px;">
                        <a href="<?php echo $url_first_job; ?>" style="color: #222; text-decoration: none;">
                            <?php echo $first_job['title']; ?>
                        </a>
                    </h2>
                    <p style="font-size: 14px; color: #555; line-height: 1.7; margin: 0 0 30px 0; text-align: justify;">
                        <?php echo isset($first_job['intro']) ? strip_tags($first_job['intro']) : ''; ?>
                    </p>
                    <a href="<?php echo $url_first_job; ?>" style="color: #111; font-size: 13px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; text-transform: uppercase; letter-spacing: 1px; width: fit-content;">
                        Xem chi tiết <span style="font-size: 16px; transform: translateY(-1px);">&rarr;</span>
                    </a>
                </div>
                
                <div class="jm-featured-image" style="flex: 0 0 50%;">
                    <a href="<?php echo $url_first_job; ?>" style="display: block; width: 100%; height: 380px; overflow: hidden; border-radius: 4px;">
                        <img src="<?php echo base_url('upload/recruitment/'.$first_job['image']); ?>" 
                             alt="<?php echo $first_job['title']; ?>" 
                             style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;"
                             onmouseover="this.style.transform='scale(1.02)'"
                             onmouseout="this.style.transform='scale(1)'"
                             onerror="this.src='<?php echo base_url('upload/no-image.jpg'); ?>';">
                    </a>
                </div>
            </div>


            <?php if(!empty($list_tuyendung)): ?>
                <div class="jm-sub-grid" style="display: flex; flex-wrap: wrap; gap: 50px 3%; width: 100%;">
                    
                    <?php foreach($list_tuyendung as $row): ?>
                        <?php $url_sub_job = base_url('tuyendung/view/'.$row['slug']); ?>
                        
                        <div class="jm-sub-card" style="flex: 0 0 31.3%; max-width: 31.3%; box-sizing: border-box; display: flex; flex-direction: column;">
                            
                            <a href="<?php echo $url_sub_job; ?>" style="display: block; width: 100%; height: 230px; overflow: hidden; border-radius: 4px; background-color: #f9f9f9;">
                                <img src="<?php echo base_url('upload/recruitment/'.$row['image']); ?>" 
                                     alt="<?php echo $row['title']; ?>" 
                                     style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;"
                                     onmouseover="this.style.transform='scale(1.03)'"
                                     onmouseout="this.style.transform='scale(1)'"
                                     onerror="this.src='<?php echo base_url('upload/no-image.jpg'); ?>';">
                            </a>
                            
                            <span style="color: #888; font-size: 12px; margin: 18px 0 8px 0; letter-spacing: 0.3px;">
                                <?php echo isset($row['created_at']) ? date('d/m/Y H:i', strtotime($row['created_at'])) : '16/06/2026 12:00'; ?>
                            </span>
                            
                            <h3 style="margin: 0 0 10px 0; font-size: 15px; font-weight: 500; line-height: 1.4; text-transform: uppercase; letter-spacing: 0.3px;">
                                <a href="<?php echo $url_sub_job; ?>" style="color: #222; text-decoration: none; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 42px;">
                                    <?php echo $row['title']; ?>
                                </a>
                            </h3>
                            
                            <p style="font-size: 13px; color: #666; line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin: 0 0 15px 0; height: 42px; text-align: justify;">
                                <?php echo isset($row['intro']) ? strip_tags($row['intro']) : ''; ?>
                            </p>
                            
                            <a href="<?php echo $url_sub_job; ?>" style="color: #111; font-size: 12px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; margin-top: auto; padding-top: 15px; text-transform: uppercase; letter-spacing: 0.5px; width: fit-content;">
                                Xem chi tiết <span style="font-size: 14px; transform: translateY(-1px);">&rarr;</span>
                            </a>
                            
                        </div>
                    <?php endforeach; ?>

                </div>
            <?php endif; ?>

        <?php else: ?>
            <div style="width: 100%; text-align: center; padding: 80px 0; color: #888; font-style: italic; font-size: 14px;">
                Hiện tại hệ thống đang cập nhật danh sách tuyển dụng mới. Vui lòng quay lại sau!
            </div>
        <?php endif; ?>

    </div>
</div>

<style>
    @media (max-width: 991px) {
        .jm-featured-job { flex-direction: column-reverse; gap: 25px; margin-bottom: 40px; padding-bottom: 40px; }
        .jm-featured-info, .jm-featured-image { flex: 0 0 100% !important; width: 100% !important; }
        .jm-featured-image a { height: 280px !important; }
        .jm-sub-card { flex: 0 0 48.5% !important; max-width: 48.5% !important; }
        .jm-sub-card h3 a, .jm-sub-card p { height: auto !important; }
    }
    @media (max-width: 576px) {
        .jm-sub-card { flex: 0 0 100% !important; max-width: 100% !important; }
        .jm-recruitment-container { padding: 20px 15px; }
    }
</style>