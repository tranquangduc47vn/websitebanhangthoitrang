<h1 class="h4 mb-3">Thêm nhà cung cấp</h1>
<form method="post" class="admin-card"><div class="admin-card-body row g-3">
	<input type="hidden" name="submit" value="1">
	<div class="col-md-4"><label class="form-label">Mã</label><input name="code" class="form-control" required></div>
	<div class="col-md-8"><label class="form-label">Tên</label><input name="name" class="form-control" required></div>
	<div class="col-md-4"><label class="form-label">Người liên hệ</label><input name="contact_name" class="form-control"></div>
	<div class="col-md-4"><label class="form-label">Điện thoại</label><input name="phone" class="form-control"></div>
	<div class="col-md-4"><label class="form-label">Email</label><input name="email" type="email" class="form-control"></div>
	<div class="col-12"><label class="form-label">Địa chỉ</label><input name="address" class="form-control"></div>
	<div class="col-12"><label class="form-label">Ghi chú</label><textarea name="note" class="form-control" rows="2"></textarea></div>
	<div class="col-12"><button class="btn btn-primary">Lưu</button> <a href="<?php echo admin_url('suppliers'); ?>" class="btn btn-outline-secondary">Hủy</a></div>
</div></form>
