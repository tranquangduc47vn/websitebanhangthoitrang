<?php
$is_edit = !empty($faq);
$admin_form_title = $is_edit ? 'Sửa FAQ' : 'Thêm FAQ';
$admin_form_breadcrumb = 'FAQ trợ lý AI';
$admin_form_back_url = admin_url('ai-assistant/faq');
$f = $faq;
$this->load->view('admin/partials/form_open');
?>
<?php if (!empty($message_fail)) { ?>
	<div class="alert alert-danger"><?php echo htmlspecialchars($message_fail, ENT_QUOTES, 'UTF-8'); ?></div>
<?php } ?>
<form class="admin-form" action="" method="post">
	<div class="mb-3">
		<label class="form-label">Câu hỏi <span class="text-danger">*</span></label>
		<input type="text" name="question" class="form-control" required
			value="<?php echo $is_edit ? htmlspecialchars($f->question, ENT_QUOTES, 'UTF-8') : set_value('question'); ?>">
	</div>
	<div class="mb-3">
		<label class="form-label">Câu trả lời <span class="text-danger">*</span></label>
		<textarea name="answer" class="form-control" rows="4" required><?php echo $is_edit ? htmlspecialchars($f->answer, ENT_QUOTES, 'UTF-8') : set_value('answer'); ?></textarea>
	</div>
	<div class="mb-3">
		<label class="form-label">Từ khóa liên quan (cách nhau bởi dấu phẩy)</label>
		<input type="text" name="keywords" class="form-control" placeholder="đổi trả, hoàn trả, trả hàng"
			value="<?php echo $is_edit ? htmlspecialchars($f->keywords, ENT_QUOTES, 'UTF-8') : set_value('keywords'); ?>">
		<div class="form-text">Giúp AI khớp câu hỏi của khách với FAQ này chính xác hơn.</div>
	</div>
	<div class="row">
		<div class="col-md-4 mb-3">
			<label class="form-label">Danh mục</label>
			<input type="text" name="category" class="form-control" placeholder="policy, order, general..."
				value="<?php echo $is_edit ? htmlspecialchars($f->category, ENT_QUOTES, 'UTF-8') : set_value('category'); ?>">
		</div>
		<div class="col-md-4 mb-3">
			<label class="form-label">Thứ tự hiển thị</label>
			<input type="number" name="sort_order" class="form-control" min="0"
				value="<?php echo $is_edit ? (int) $f->sort_order : set_value('sort_order', 0); ?>">
		</div>
		<div class="col-md-4 mb-3 d-flex align-items-end">
			<label class="form-check mb-2">
				<input type="checkbox" name="is_active" value="1" class="form-check-input" <?php echo (!$is_edit || (int) $f->is_active === 1) ? 'checked' : ''; ?>>
				<span class="form-check-label">Đang kích hoạt</span>
			</label>
		</div>
	</div>
	<div class="admin-form-actions">
		<button type="submit" name="submit" value="1" class="btn btn-primary">Lưu</button>
		<a href="<?php echo admin_url('ai-assistant/faq'); ?>" class="btn btn-default">Hủy</a>
	</div>
</form>
<?php $this->load->view('admin/partials/form_close'); ?>
