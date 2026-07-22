<div class="admin-breadcrumb">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo admin_url('home'); ?>"><i class="fa-solid fa-house me-1"></i> Trang chủ</a></li>
			<li class="breadcrumb-item active" aria-current="page">Slider</li>
		</ol>
	</nav>
</div>

<div class="d-flex flex-wrap justify-content-end gap-2 mb-3">
	<a href="<?php echo admin_url('slider/add'); ?>" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i> Thêm slider</a>
</div>

<div class="admin-card">
	<div class="admin-card-header">
		<span>Danh sách slider</span>
		<div class="admin-search">
			<i class="fa-solid fa-search"></i>
			<input type="search" class="form-control" placeholder="Tìm slider..." data-admin-table-search="sliderListTable">
		</div>
	</div>
	<div class="admin-card-body">
		<?php if (!empty($message_success)) { ?>
			<div class="alert alert-success"><strong>Thành công!</strong> <?php echo $message_success; ?></div>
		<?php } ?>
		<?php if (!empty($message_fail)) { ?>
			<div class="alert alert-danger"><strong>Thất bại!</strong> <?php echo $message_fail; ?></div>
		<?php } ?>
		<div class="table-responsive">
			<table class="table table-hover admin-table" id="sliderListTable">
				<thead>
					<tr>
						<th>Tên slider</th>
						<th>Hình ảnh</th>
						<th>Liên kết</th>
						<th>Thứ tự</th>
						<th class="text-center">Hành động</th>
					</tr>
				</thead>
				<tbody>
					<?php if (!empty($slider)) { ?>
						<?php foreach ($slider as $value) { ?>
							<tr>
								<td><strong><?php echo htmlspecialchars($value->name, ENT_QUOTES, 'UTF-8'); ?></strong></td>
								<td><img src="<?php echo base_url('upload/slider/' . $value->image_link); ?>" alt="" style="width:180px;height:70px;object-fit:cover;border-radius:8px;border:1px solid var(--admin-border);"></td>
								<td><a href="<?php echo $value->link; ?>" target="_blank" rel="noopener"><?php echo htmlspecialchars($value->link, ENT_QUOTES, 'UTF-8'); ?></a></td>
								<td><span class="badge bg-primary"><?php echo $value->sort_order; ?></span></td>
								<td class="text-center">
									<a class="btn btn-sm btn-outline-primary me-1" href="<?php echo admin_url('slider/edit/' . $value->id); ?>"><i class="fa-solid fa-pen"></i></a>
									<a class="btn btn-sm btn-outline-danger" href="<?php echo admin_url('slider/del/' . $value->id); ?>" onclick="return confirm('Bạn chắc chắn muốn xóa slider này?')"><i class="fa-solid fa-trash"></i></a>
								</td>
							</tr>
						<?php } ?>
					<?php } else { ?>
						<tr><td colspan="5" class="admin-empty">Không tìm thấy dữ liệu slider.</td></tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
