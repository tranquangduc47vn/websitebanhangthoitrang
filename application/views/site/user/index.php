<?php
$account_asset = site_asset_url('');
?>
<link rel="stylesheet" href="<?php echo $account_asset; ?>css/account-luxury.css?v=3">

<div class="col-xs-12 jm-account-lux">
	<div class="jm-account-lux__inner">

		<?php if (!empty($message_success)) { ?>
			<div class="jm-account-flash jm-account-flash--ok"><?php echo htmlspecialchars($message_success, ENT_QUOTES, 'UTF-8'); ?></div>
		<?php } ?>
		<?php if (!empty($message_fail)) { ?>
			<div class="jm-account-flash jm-account-flash--err"><?php echo htmlspecialchars($message_fail, ENT_QUOTES, 'UTF-8'); ?></div>
		<?php } ?>

		<section class="jm-account-panel">
			<div class="jm-account-panel__head">
				<h1 class="jm-account-panel__title">Thông tin tài khoản</h1>
				<p class="jm-account-panel__sub">JM Dress Design</p>
			</div>
			<div class="jm-account-panel__body">
				<div class="jm-account-profile">
					<div class="jm-account-profile__item">
						<span class="jm-account-profile__label">Họ và tên</span>
						<span class="jm-account-profile__value"><?php echo htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8'); ?></span>
					</div>
					<div class="jm-account-profile__item">
						<span class="jm-account-profile__label">Email</span>
						<span class="jm-account-profile__value"><?php echo htmlspecialchars($user->email, ENT_QUOTES, 'UTF-8'); ?></span>
					</div>
					<div class="jm-account-profile__item">
						<span class="jm-account-profile__label">Số điện thoại</span>
						<span class="jm-account-profile__value"><?php echo htmlspecialchars($user->phone, ENT_QUOTES, 'UTF-8'); ?></span>
					</div>
				</div>
			</div>
		</section>

		<?php if (!empty($loyalty)) { ?>
		<section class="jm-account-panel" id="jm-account-loyalty">
			<div class="jm-account-panel__head">
				<h2 class="jm-account-panel__title">Tích điểm &amp; hạng thành viên</h2>
				<p class="jm-account-panel__sub">
					Hạng <?php echo htmlspecialchars($loyalty->tier_label, ENT_QUOTES, 'UTF-8'); ?>
					· <?php echo number_format((int) $loyalty->points, 0, ',', '.'); ?> điểm
				</p>
			</div>
			<div class="jm-account-panel__body">
				<div class="jm-loyalty-stats">
					<div class="jm-loyalty-stat">
						<span class="jm-loyalty-stat__label">Đơn hoàn thành</span>
						<span class="jm-loyalty-stat__value"><?php echo (int) $loyalty->completed_orders; ?></span>
					</div>
					<div class="jm-loyalty-stat">
						<span class="jm-loyalty-stat__label">Tổng chi tiêu</span>
						<span class="jm-loyalty-stat__value"><?php echo number_format((int) $loyalty->lifetime_spend, 0, ',', '.'); ?> ₫</span>
					</div>
				</div>
				<p class="jm-loyalty-hint">Nhập mã voucher khi thanh toán. <a href="<?php echo base_url('tich-diem'); ?>">Xem chính sách tích điểm</a></p>
				<?php if (!empty($available_vouchers)) { ?>
					<h3 class="jm-loyalty-vouchers__title">Mã bạn có thể dùng</h3>
					<ul class="jm-loyalty-vouchers">
						<?php foreach ($available_vouchers as $vc) {
							$disc = ($vc->discount_type === 'percent')
								? (int) $vc->discount_value . '%'
								: number_format((int) $vc->discount_value, 0, ',', '.') . ' ₫';
						?>
							<li class="jm-loyalty-voucher-item">
								<code><?php echo htmlspecialchars($vc->code, ENT_QUOTES, 'UTF-8'); ?></code>
								<span><?php echo htmlspecialchars($vc->name, ENT_QUOTES, 'UTF-8'); ?> — giảm <?php echo $disc; ?></span>
							</li>
						<?php } ?>
					</ul>
				<?php } ?>
			</div>
		</section>
		<?php } ?>

		<section class="jm-account-panel" id="jm-account-addresses">
			<div class="jm-account-panel__head">
				<h2 class="jm-account-panel__title">Sổ địa chỉ giao hàng</h2>
				<p class="jm-account-panel__sub">Tối đa <?php echo (int) $address_max; ?> địa chỉ · Chọn một địa chỉ mặc định</p>
			</div>
			<div class="jm-account-panel__body">
				<div class="jm-account-addresses">
					<?php if (!empty($user_addresses)) { ?>
						<?php foreach ($user_addresses as $addr) {
							$is_default = ((int) $addr->is_default === 1);
							$card_class = 'jm-account-address-card' . ($is_default ? ' jm-account-address-card--default' : '');
						?>
							<article class="<?php echo $card_class; ?>">
								<?php if ($is_default) { ?>
									<span class="jm-account-address-badge">Mặc định</span>
								<?php } ?>
								<p class="jm-account-address-text"><?php echo htmlspecialchars($addr->address_line, ENT_QUOTES, 'UTF-8'); ?></p>
								<div class="jm-account-address-actions">
									<?php if (!$is_default) { ?>
										<form method="post" action="<?php echo base_url('user/address_default/' . (int) $addr->id); ?>" style="display:inline;">
											<button type="submit" class="jm-account-btn jm-account-btn--gold">Đặt mặc định</button>
										</form>
									<?php } ?>
									<form method="post" action="<?php echo base_url('user/address_delete/' . (int) $addr->id); ?>" style="display:inline;" onsubmit="return confirm('Xóa địa chỉ này?');">
										<button type="submit" class="jm-account-btn jm-account-btn--danger">Xóa</button>
									</form>
								</div>
							</article>
						<?php } ?>
					<?php } else { ?>
						<p class="jm-account-empty">Bạn chưa có địa chỉ giao hàng. Thêm địa chỉ bên dưới (chọn đúng tỉnh, quận, phường trên danh sách Việt Nam).</p>
					<?php } ?>
				</div>

				<?php if ($address_count < $address_max) { ?>
					<div class="jm-account-add">
						<h3 class="jm-account-add__title">Thêm địa chỉ mới</h3>
						<form method="post" action="<?php echo base_url('user/address_add'); ?>">
							<div class="jm-account-field">
								<label>Địa giới hành chính (Việt Nam)</label>
								<p class="jm-auth-hint">Chọn tỉnh, quận/huyện, phường/xã — không nhập tỉnh thành tùy ý.</p>
								<select class="jm-account-select" id="addrAddProvince" name="province_id" required>
									<option value="">— Đang tải tỉnh thành —</option>
								</select>
								<select class="jm-account-select" id="addrAddDistrict" name="district_id" required disabled>
									<option value="">— Chọn quận / huyện —</option>
								</select>
								<select class="jm-account-select" id="addrAddWard" name="ward_id" required disabled>
									<option value="">— Chọn phường / xã —</option>
								</select>
							</div>
							<div class="jm-account-field">
								<label for="addrAddNote">Ghi chú địa chỉ (số nhà, đường…)</label>
								<input type="text" class="jm-account-input" id="addrAddNote" name="address_note" required maxlength="255"
									placeholder="Ví dụ: 123 Nguyễn Trãi, tòa A, căn 502">
							</div>
							<?php if ($address_count > 0) { ?>
								<label class="jm-account-check">
									<input type="checkbox" name="is_default" value="1">
									Đặt làm địa chỉ mặc định
								</label>
							<?php } ?>
							<div style="text-align:center;">
								<button type="submit" class="jm-account-btn jm-account-btn--primary">Lưu địa chỉ</button>
							</div>
						</form>
					</div>
				<?php } else { ?>
					<p class="jm-account-limit">Bạn đã đủ <?php echo (int) $address_max; ?> địa chỉ. Xóa một địa chỉ nếu muốn thêm mới.</p>
				<?php } ?>
			</div>
		</section>

		<section class="jm-account-panel">
			<div class="jm-account-panel__head">
				<h2 class="jm-account-panel__title">Lịch sử đơn hàng</h2>
			</div>
			<div class="jm-account-panel__body">
				<div class="jm-account-orders-wrap">
					<?php if (!empty($list_transaction)) { ?>
						<table class="jm-account-orders">
							<thead>
								<tr>
									<th>STT</th>
									<th>Ngày đặt</th>
									<th>SĐT nhận</th>
									<th>Tổng tiền</th>
									<th>Trạng thái</th>
									<th>Thao tác</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$stt = 0;
								foreach ($list_transaction as $value) {
									$stt++;
									$status_class = 'jm-account-status--wait';
									$status_label = 'Đang chờ';
									switch ($value->status) {
										case '0':
											$status_class = 'jm-account-status--wait';
											$status_label = 'Chờ xử lý';
											break;
										case '1':
											$status_class = 'jm-account-status--ok';
											$status_label = 'Đã xác nhận';
											break;
										case '2':
											$status_class = 'jm-account-status--ship';
											$status_label = 'Đang giao';
											break;
										case '3':
											$status_class = 'jm-account-status--done';
											$status_label = 'Hoàn thành';
											break;
										case '4':
											$status_class = 'jm-account-status--cancel';
											$status_label = 'Đã hủy';
											break;
									}
								?>
									<tr>
										<td><?php echo $stt; ?></td>
										<td><?php echo mdate('%H:%i %d/%m/%Y', $value->created); ?></td>
										<td><?php echo htmlspecialchars($value->user_phone, ENT_QUOTES, 'UTF-8'); ?></td>
										<td class="jm-order-amount"><?php echo number_format($value->amount); ?> ₫</td>
										<td>
											<span class="jm-account-status <?php echo $status_class; ?>"><?php echo $status_label; ?></span>
											<?php if ($value->status == '4' && !empty($value->reason)) { ?>
												<div style="margin-top:6px;font-size:11px;color:#888;max-width:180px;margin-left:auto;margin-right:auto;"><?php echo htmlspecialchars($value->reason, ENT_QUOTES, 'UTF-8'); ?></div>
											<?php } ?>
										</td>
										<td>
											<?php if ($value->status == '0') { ?>
												<button type="button" class="jm-account-btn jm-account-btn--danger" onclick="moModalHuyDon(<?php echo (int) $value->id; ?>)">Hủy đơn</button>
											<?php } elseif ($value->status == '4') { ?>
												<span style="font-size:12px;color:#999;">—</span>
											<?php } elseif ($value->status == '3') { ?>
												<span style="font-size:12px;color:#558b2f;">✓</span>
											<?php } else { ?>
												<span style="font-size:11px;color:#888;">Đang xử lý</span>
											<?php } ?>
										</td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					<?php } else { ?>
						<p class="jm-account-orders-empty">Bạn chưa đặt đơn hàng nào trên hệ thống của chúng tôi.</p>
					<?php } ?>
				</div>
			</div>
		</section>
	</div>
</div>

<div class="modal fade" id="modalHuyDonHang" tabindex="-1" role="dialog" aria-labelledby="modalHuyDonLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="color: white;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 1;">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="modalHuyDonLabel" style="font-weight: bold;">Xác nhận hủy đơn hàng</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" id="id_don_hang_huy" value="">
        <p style="font-weight: bold; margin-bottom: 15px;">Vui lòng chọn lý do hủy đơn hàng của bạn:</p>
        <div class="radio">
          <label>
            <input type="radio" name="ly_do_mac_dinh" value="Tôi muốn thay đổi thông tin nhận hàng (SĐT, Địa chỉ)">
            Tôi muốn thay đổi thông tin nhận hàng (Số điện thoại, Địa chỉ nhận)
          </label>
        </div>
        <div class="radio">
          <label>
            <input type="radio" name="ly_do_mac_dinh" value="Tôi muốn đổi màu sắc, kích thước hoặc thêm/bớt sản phẩm">
            Tôi muốn đổi màu sắc, kích cỡ size hoặc thêm/bớt mặt hàng
          </label>
        </div>
        <div class="radio">
          <label>
            <input type="radio" name="ly_do_mac_dinh" value="Tìm thấy nơi khác bán giá rẻ hơn hoặc thời gian giao lâu quá">
            Tìm thấy cửa hàng khác bán rẻ hơn hoặc thời gian giao hàng quá lâu
          </label>
        </div>
        <div class="radio">
          <label>
            <input type="radio" name="ly_do_mac_dinh" value="Tôi không còn nhu cầu mua sản phẩm này nữa">
            Tôi đổi ý, không còn nhu cầu đặt mua sản phẩm này nữa
          </label>
        </div>
        <div class="radio">
          <label>
            <input type="radio" name="ly_do_mac_dinh" value="khac">
            <strong style="color: #337ab7;">Lý do khác (Vui lòng tự nhập ở dưới)...</strong>
          </label>
        </div>
        <div id="khungLyDoKhac" style="display: none; margin-top: 15px;">
          <label for="text_ly_do_khac">Nhập lý do chi tiết:</label>
          <textarea class="form-control" id="text_ly_do_khac" rows="3" placeholder="Gõ lý do hủy cụ thể..."></textarea>
        </div>
        <div class="alert alert-warning" style="margin-top: 15px; margin-bottom: 0; font-size: 12px;">
          <strong>Cảnh báo:</strong> Thao tác hủy đơn không thể hoàn tác.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
        <button type="button" class="btn btn-danger" onclick="thucHienHuyDon()">Xác nhận hủy</button>
      </div>
    </div>
  </div>
</div>

<?php if ($address_count < $address_max) { ?>
<script src="<?php echo site_asset_url('js/vn-address.js?v=1'); ?>"></script>
<script>
(function ($) {
	var form = new VnAddressForm({
		province: '#addrAddProvince',
		district: '#addrAddDistrict',
		ward: '#addrAddWard',
		dataUrl: <?php echo json_encode($vn_address_json_url); ?>,
		initial: { province_id: '', district_id: '', ward_id: '' }
	});
	form.init().fail(function () {
		$('#addrAddProvince').html('<option value="">Không tải được dữ liệu địa giới</option>');
	});
})(jQuery);
</script>
<?php } ?>

<script type="text/javascript">
function moModalHuyDon(id) {
    $('#id_don_hang_huy').val(id);
    $('input[name="ly_do_mac_dinh"]').prop('checked', false);
    $('#text_ly_do_khac').val('');
    $('#khungLyDoKhac').hide();
    $('#modalHuyDonHang').modal('show');
}
$(document).ready(function() {
    $('input[name="ly_do_mac_dinh"]').change(function() {
        if ($(this).val() === 'khac') {
            $('#khungLyDoKhac').fadeIn(250);
            $('#text_ly_do_khac').focus();
        } else {
            $('#khungLyDoKhac').fadeOut(150);
        }
    });
});
function thucHienHuyDon() {
    var transactionId = $('#id_don_hang_huy').val();
    var luaChonRadio = $('input[name="ly_do_mac_dinh"]:checked').val();
    var chuoiLyDoCuThe = "";
    if (!luaChonRadio) {
        alert("Vui lòng chọn lý do hủy đơn hàng!");
        return;
    }
    if (luaChonRadio === 'khac') {
        chuoiLyDoCuThe = $('#text_ly_do_khac').val().trim();
        if (chuoiLyDoCuThe === "") {
            alert("Vui lòng nhập lý do chi tiết!");
            return;
        }
    } else {
        chuoiLyDoCuThe = luaChonRadio;
    }
    window.location.href = "<?php echo site_url('thanh-toan/cancel'); ?>/" + transactionId + "?reason=" + encodeURIComponent(chuoiLyDoCuThe);
}
</script>
