(function () {
	'use strict';

	var cfg = window.jmCheckoutVoucher;
	if (!cfg || !cfg.applyUrl) {
		return;
	}

	var btn = document.getElementById('jmCheckoutVoucherBtn');
	var input = document.getElementById('jm-checkout-voucher');
	var msgEl = document.getElementById('jmCheckoutVoucherMsg');
	var discountRow = document.getElementById('jmCheckoutDiscountRow');
	var discountAmt = document.getElementById('jmCheckoutDiscountAmt');
	var oldTotal = document.getElementById('jmCheckoutOldTotal');
	var newTotal = document.getElementById('jmCheckoutNewTotal');

	if (!btn || !input || !msgEl || !newTotal) {
		return;
	}

	var lastAppliedCode = cfg.applied && cfg.applied.discount > 0 ? (input.value || '').trim().toUpperCase() : '';

	function showMsg(text, type) {
		msgEl.hidden = false;
		msgEl.textContent = text;
		msgEl.className = 'jm-checkout-voucher-msg jm-checkout-voucher-msg--' + (type || 'info');
	}

	function hideMsg() {
		msgEl.hidden = true;
		msgEl.textContent = '';
		msgEl.className = 'jm-checkout-voucher-msg';
	}

	function resetTotals() {
		if (discountRow) {
			discountRow.hidden = true;
			discountRow.setAttribute('hidden', 'hidden');
		}
		if (discountAmt) {
			discountAmt.textContent = '';
		}
		if (oldTotal) {
			oldTotal.hidden = true;
			oldTotal.setAttribute('hidden', 'hidden');
			oldTotal.textContent = '';
		}
		newTotal.textContent = cfg.subtotalFmt;
		lastAppliedCode = '';
	}

	function applyDiscount(data) {
		if (data.discount > 0) {
			if (discountRow) {
				discountRow.hidden = false;
				discountRow.removeAttribute('hidden');
			}
			if (discountAmt) {
				var df = data.discount_fmt || '';
				discountAmt.textContent = df.indexOf('−') === 0 || df.indexOf('-') === 0 ? df : '−' + df;
			}
			if (oldTotal) {
				oldTotal.hidden = false;
				oldTotal.removeAttribute('hidden');
				oldTotal.textContent = data.subtotal_fmt || cfg.subtotalFmt;
			}
			newTotal.textContent = data.final_fmt || data.finalFmt || cfg.subtotalFmt;
			lastAppliedCode = (input.value || '').trim().toUpperCase();
		} else {
			resetTotals();
		}
	}

	function parseJsonResponse(res) {
		return res.text().then(function (text) {
			try {
				return JSON.parse(text);
			} catch (e) {
				return { ok: false, message: 'Phản hồi không hợp lệ từ máy chủ.' };
			}
		});
	}

	btn.addEventListener('click', function () {
		if (!cfg.loggedIn) {
			showMsg('Vui lòng đăng nhập để dùng voucher.', 'err');
			return;
		}

		btn.disabled = true;
		btn.classList.add('is-loading');

		var body = new URLSearchParams();
		body.set('voucher_code', input.value.trim());

		fetch(cfg.applyUrl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
				'X-Requested-With': 'XMLHttpRequest',
			},
			body: body.toString(),
		})
			.then(parseJsonResponse)
			.then(function (data) {
				if (!data || data.ok !== true) {
					resetTotals();
					showMsg((data && data.message) ? data.message : 'Không áp dụng được mã.', 'err');
					return;
				}
				if (data.cleared) {
					resetTotals();
					showMsg(data.message, 'info');
					return;
				}
				applyDiscount(data);
				showMsg(data.message, 'ok');
			})
			.catch(function () {
				resetTotals();
				showMsg('Không kết nối được máy chủ. Thử lại sau.', 'err');
			})
			.finally(function () {
				btn.disabled = !cfg.loggedIn;
				btn.classList.remove('is-loading');
			});
	});

	input.addEventListener('input', function () {
		var current = (input.value || '').trim().toUpperCase();
		if (lastAppliedCode && current !== lastAppliedCode) {
			resetTotals();
			hideMsg();
		}
	});
})();
