<div class="col-xs-12 col-sm-9 col-md-9 col-lg-9 clearpaddingr">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 clearpadding">

        <?php if(!empty($message_success)): ?>
            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <strong>Thành công!</strong> <?php echo $message_success; ?>
            </div>
        <?php endif; ?>

        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title" style="font-weight: bold; font-size: 16px;">
                    <?php echo !empty($hop_tac) ? $hop_tac->title : $title; ?>
                </h3>
            </div>
            <div class="panel-body" style="padding: 20px;">
                
                <div class="content-intro" style="margin-bottom: 20px; line-height: 1.8;">
                    <?php if(!empty($hop_tac)): ?>
                        <?php echo $hop_tac->content; ?>
                    <?php else: ?>
                        <p class="lead" style="font-size: 15px;">Mọi yêu cầu đề xuất hợp tác, phát triển đại lý xin vui lòng điền thông tin vào form dưới đây.</p>
                    <?php endif; ?>
                </div>
                
                <hr>

                <form action="<?php echo base_url('LienHeHopTacKinhDoanh'); ?>" method="POST" role="form" style="margin-top: 20px;">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label style="font-weight: bold;">Họ và tên người liên hệ: <span style="color:red;">*</span></label>
                            <input type="text" name="contact_name" required class="form-control" placeholder="Nguyễn Văn A...">
                        </div>
                        <div class="col-md-6 form-group">
                            <label style="font-weight: bold;">Số điện thoại: <span style="color:red;">*</span></label>
                            <input type="text" name="contact_phone" required class="form-control" placeholder="09xxxxxxx...">
                        </div>
                    </div>

                    <div class="form-group">
                        <label style="font-weight: bold;">Tên công ty / Đơn vị hợp tác:</label>
                        <input type="text" name="company_name" class="form-control" placeholder="Công ty TNHH Thương Mại...">
                    </div>

                    <div class="form-group">
                        <label style="font-weight: bold;">Nội dung đề xuất hợp tác chi tiết: <span style="color:red;">*</span></label>
                        <textarea name="contact_content" required class="form-control" rows="5" placeholder="Vui lòng mô tả rõ sản phẩm hoặc hình thức muốn liên kết hợp tác..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="font-weight: bold; padding: 8px 25px;">
                        <span class="glyphicon glyphicon-send"></span> Gửi thông tin liên hệ
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>