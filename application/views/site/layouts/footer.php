<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$wf_vnpay_fallback = site_asset_url('img/vnpay-mark.svg');
?>
<footer class="wf-chrome-2026 wf-chrome-footer" role="contentinfo">
	<div class="wf-chrome-footer__top">
		<div class="wf-chrome-footer__grid">
			<div class="wf-chrome-footer__col">
				<h4 class="wf-chrome-footer__heading">JM DRESS DESIGN</h4>
				<ul class="wf-chrome-footer__list">
					<li><a href="<?php echo base_url('gioi-thieu'); ?>">Giới thiệu</a></li>
					<li><a href="<?php echo build_news_index_url(); ?>">Tin tức</a></li>
					<li><a href="<?php echo base_url('hethongcuahang'); ?>">Hệ thống cửa hàng</a></li>
					<li><a href="<?php echo base_url('ThongTinTuyenDung'); ?>">Thông Tin Tuyển Dụng</a></li>
					<li><a href="<?php echo base_url('LienHeHopTacKinhDoanh'); ?>">Liên hệ hợp tác kinh doanh</a></li>
				</ul>
			</div>

			<div class="wf-chrome-footer__col">
				<h4 class="wf-chrome-footer__heading">LIÊN KẾT VỚI CHÚNG TÔI</h4>
				<ul class="wf-chrome-footer__list wf-chrome-footer__list--social">
					<li>
						<a href="https://facebook.com" target="_blank" rel="noopener noreferrer">
							<i class="fa-brands fa-facebook-f" aria-hidden="true"></i> Facebook
						</a>
					</li>
					<li>
						<a href="https://instagram.com" target="_blank" rel="noopener noreferrer">
							<i class="fa-brands fa-instagram" aria-hidden="true"></i> Instagram
						</a>
					</li>
					<li>
						<a href="https://youtube.com" target="_blank" rel="noopener noreferrer">
							<i class="fa-brands fa-youtube" aria-hidden="true"></i> Youtube
						</a>
					</li>
					<li class="wf-chrome-footer__email">
						<i class="fa-regular fa-envelope" aria-hidden="true"></i> Email: marketing@jm.com.vn
					</li>
				</ul>
			</div>

			<div class="wf-chrome-footer__col">
				<h4 class="wf-chrome-footer__heading">CHĂM SÓC KHÁCH HÀNG</h4>
				<ul class="wf-chrome-footer__list">
					<li><a href="<?php echo base_url('VanChuyen'); ?>">Chính sách vận chuyển</a></li>
					<li><a href="<?php echo base_url('tich-diem'); ?>">Chính sách tích điểm</a></li>
					<li><a href="#">Chính sách đổi trả hàng</a></li>
					<li><a href="#">Chính sách bảo mật</a></li>
					<li><a href="#">Chế độ bảo hành trọn đời</a></li>
				</ul>
			</div>

			<div class="wf-chrome-footer__col">
				<h4 class="wf-chrome-footer__heading">VỊ TRÍ CỬA HÀNG</h4>
				<div class="wf-chrome-footer__map-wrap">
					<iframe
						class="wf-chrome-footer__map"
						src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3918.8758107946264!2d106.57329537415924!3d10.820814789330674!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752b5254ad2b21%3A0x90cbf42aa07ac0e9!2zSOG7kyBCxqFpIE1JTkggTEFO!5e0!3m2!1svi!2s!4v1781060366454!5m2!1svi!2s"
						title="Bản đồ cửa hàng"
						allowfullscreen=""
						loading="lazy"
						referrerpolicy="no-referrer-when-downgrade"></iframe>
				</div>
			</div>
		</div>
	</div>

	<div class="wf-chrome-footer__bottom">
		<div class="wf-chrome-footer__bottom-bar">
			<p class="wf-chrome-footer__copy">©2009-2021 JM All Rights Reserved</p>
			<p class="wf-chrome-footer__legal-inline">
				<strong>CÔNG TY CỔ PHẦN THƯƠNG MẠI JDD VIỆT NAM</strong>
				<span class="wf-chrome-footer__legal-sep" aria-hidden="true">·</span>
				<span>Trụ sở chính: Ô số 02-NV26, Khu đô thị mới Bắc quốc lộ 32, Thị trấn Trạm Trôi, Huyện Hoài Đức, Hà Nội</span>
			</p>
			<p class="wf-chrome-footer__pay">
				Chúng tôi nhận thanh toán qua:
				<img
					src="<?php echo htmlspecialchars($wf_vnpay_fallback, ENT_QUOTES, 'UTF-8'); ?>"
					alt="VNPay"
					class="wf-chrome-footer__vnpay"
					onerror="this.style.display='none'">
			</p>
		</div>
	</div>
</footer>

<?php
$this->load->helper('ai');
ai_render_widget();
?>
