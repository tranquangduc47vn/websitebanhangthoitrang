<div class="container jm-content-page" style="max-width: 1200px; margin: 0 auto; padding: 40px 15px; background-color: #fbfbfb;">
    
    <h1 style="font-size: 24px; font-weight: bold; margin-bottom: 40px; text-transform: uppercase; text-align: center; letter-spacing: 1px; color: #222;">
        Hợp tác kinh doanh - Thời trang Ngọc Lan
    </h1>

    <div class="hoptac-list">
        <?php if(!empty($list)): 
            $featured = array_shift($list); 
        ?>
            
            <div class="hoptac-featured" style="display: flex; background: #fff; border: 1px solid #eee; margin-bottom: 50px; border-radius: 4px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.02); min-height: 380px; flex-wrap: wrap;">
                
                <div style="flex: 1; min-width: 300px; padding: 40px; display: flex; flex-direction: column; justify-content: center;">
                    <div class="date" style="color: #999; font-size: 13px; margin-bottom: 12px;">
                        📅 <?php echo date('d/m/Y H:i', strtotime($featured->created_at)); ?>
                    </div>
                    
                    <h2 style="font-size: 24px; font-weight: 600; color: #222; margin: 0 0 15px 0; text-transform: uppercase; line-height: 1.4;">
                        <a href="<?php echo base_url('hoptac/view/'.$featured->slug); ?>" style="color: #222; text-decoration: none;">
                            <?php echo $featured->title; ?>
                        </a>
                    </h2>
                    
                    <p style="color: #666; font-size: 14px; line-height: 1.6; margin: 0 0 25px 0;">
                        <?php echo $featured->intro; ?>
                    </p>
                    
                    <a href="<?php echo base_url('hoptac/view/'.$featured->slug); ?>" style="color: #000; font-weight: bold; text-decoration: none; font-size: 13px; text-transform: uppercase; border-bottom: 2px solid #000; width: max-content; padding-bottom: 3px; display: inline-flex; align-items: center; gap: 5px;">
                        Xem chi tiết &rarr;
                    </a>
                </div>

                <div class="thumb" style="flex: 1.2; min-width: 350px; background: #f5f5f5;">
                    <a href="<?php echo base_url('hoptac/view/'.$featured->slug); ?>" style="display: block; width: 100%; height: 100%;">
                        <img src="<?php echo base_url('upload/'.$featured->image); ?>" alt="<?php echo $featured->title; ?>" style="width: 100%; height: 100%; object-fit: cover; display: block; min-height: 300px;" onerror="this.src='<?php echo base_url('upload/no-image.jpg'); ?>';">
                    </a>
                </div>
            </div>

            <?php if(!empty($list)): ?>
                <div class="hoptac-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; margin-bottom: 40px;">
                    
                    <?php foreach($list as $row): ?>
                        <div class="hoptac-item" style="background: #fff; border: 1px solid #eee; border-radius: 4px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.02); display: flex; flex-direction: column;">
                            
                            <div class="thumb" style="height: 220px; overflow: hidden; background: #f9f9f9;">
                                <a href="<?php echo base_url('hoptac/view/'.$row->slug); ?>" style="display: block; width: 100%; height: 100%;">
                                    <img src="<?php echo base_url('upload/'.$row->image); ?>" alt="<?php echo $row->title; ?>" style="width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.3s ease;" onmouseover="this.style.transform='scale(1.04)'" onmouseout="this.style.transform='scale(1)'" onerror="this.src='<?php echo base_url('upload/no-image.jpg'); ?>';">
                                </a>
                            </div>

                            <div style="padding: 25px; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between;">
                                <div>
                                    <div class="date" style="color: #aaa; font-size: 12px; margin-bottom: 10px;">
                                        📅 <?php echo date('d/m/Y H:i', strtotime($row->created_at)); ?>
                                    </div>
                                    
                                    <h2 style="font-size: 16px; font-weight: 600; color: #222; margin: 0 0 12px 0; text-transform: uppercase; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 44px;">
                                        <a href="<?php echo base_url('hoptac/view/'.$row->slug); ?>" style="color: #222; text-decoration: none;">
                                            <?php echo $row->title; ?>
                                        </a>
                                    </h2>
                                    
                                    <p style="color: #666; font-size: 13.5px; line-height: 1.6; margin: 0 0 20px 0; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; height: 65px;">
                                        <?php echo $row->intro; ?>
                                    </p>
                                </div>

                                <a href="<?php echo base_url('hoptac/view/'.$row->slug); ?>" style="color: #000; font-weight: bold; text-decoration: none; font-size: 12px; text-transform: uppercase; border-bottom: 1px solid #000; width: max-content; padding-bottom: 2px; display: inline-flex; align-items: center; gap: 5px;">
                                    Xem chi tiết &rarr;
                                </a>
                            </div>

                        </div>
                    <?php endforeach; ?>

                </div>
            <?php endif; ?>

        <?php else: ?>
            <p style="font-style: italic; color: #999; text-align: center; padding: 40px 0;">Hiện tại chưa có thông tin hợp tác kinh doanh nào mới.</p>
        <?php endif; ?>
    </div>
</div>