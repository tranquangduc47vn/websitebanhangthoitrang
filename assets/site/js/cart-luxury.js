(function () {
	'use strict';

	var cfg = window.JM_CART_AJAX || {};
	var busy = false;

	function formatMoney(n) {
		return (Number(n) || 0).toLocaleString('vi-VN') + ' ₫';
	}

	function postForm(url, data) {
		return fetch(url, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
				'X-Requested-With': 'XMLHttpRequest',
			},
			credentials: 'same-origin',
			body: new URLSearchParams(data).toString(),
		}).then(function (res) {
			return res.json();
		});
	}

	function getJson(url) {
		return fetch(url, {
			headers: { 'X-Requested-With': 'XMLHttpRequest' },
			credentials: 'same-origin',
		}).then(function (res) {
			return res.json();
		});
	}

	function setBusy(on) {
		busy = on;
		document.body.classList.toggle('jm-cart-is-busy', on);
	}

	function updateHeaderBadge(totalItems) {
		var badge = document.querySelector('.wf-chrome-cart-badge');
		if (!badge) return;
		if (totalItems > 0) {
			badge.textContent = totalItems > 99 ? '99+' : String(totalItems);
			badge.style.display = '';
		} else {
			badge.style.display = 'none';
		}
	}

	function updateTotals(totalItems, totalPrice) {
		var countEl = document.querySelector('.jm-cart-panel__count');
		var amountEl = document.querySelector('.jm-cart-summary__amount');
		if (countEl) {
			countEl.textContent = totalItems + ' sản phẩm';
		}
		if (amountEl) {
			amountEl.textContent = formatMoney(totalPrice);
		}
		updateHeaderBadge(totalItems);
	}

	function showFlash(message, isError) {
		var flash = document.querySelector('.jm-cart-flash');
		if (!flash) {
			flash = document.createElement('div');
			flash.className = 'jm-cart-flash';
			flash.setAttribute('role', 'alert');
			var inner = document.querySelector('.jm-cart-lux__inner');
			if (inner) {
				inner.insertBefore(flash, inner.firstChild);
			}
		}
		flash.textContent = message;
		flash.classList.toggle('jm-cart-flash--error', !!isError);
		flash.hidden = !message;
	}

	function showStockNotice(data) {
		if (window.QD && window.QD.showStockNotice && window.QD.showStockNotice(data)) {
			return;
		}
		showFlash(data.message || 'Không đủ tồn kho.', true);
	}

	function findRow(rowid) {
		return document.querySelector('.jm-cart-row[data-rowid="' + CSS.escape(rowid) + '"]');
	}

	function updateRowNumbers() {
		document.querySelectorAll('.jm-cart-row').forEach(function (row, idx) {
			var cell = row.querySelector('.jm-cart-col--idx');
			if (cell) cell.textContent = String(idx + 1);
		});
	}

	function applyQtyUpdate(data) {
		if (data.removed) {
			var row = findRow(data.rowid);
			if (row) row.remove();
			updateRowNumbers();
			if (document.querySelectorAll('.jm-cart-row').length === 0) {
				window.location.reload();
			}
			return;
		}
		var row = findRow(data.rowid);
		if (!row) return;
		var qtyEl = row.querySelector('.jm-cart-qty__val');
		var priceEl = row.querySelector('.jm-cart-line-price');
		if (qtyEl) qtyEl.textContent = String(data.qty);
		if (priceEl) priceEl.textContent = formatMoney(data.subtotal);
	}

	function applyOptionsUpdate(data) {
		var row = findRow(data.old_rowid || data.rowid);
		if (!row) return;

		if (data.old_rowid && data.rowid && data.old_rowid !== data.rowid) {
			row.setAttribute('data-rowid', data.rowid);
			var hidden = row.querySelector('input[name="rowid"]');
			if (hidden) hidden.value = data.rowid;
		}

		var sizeSelect = row.querySelector('select[name="size"]');
		var colorSelect = row.querySelector('select[name="color"]');
		if (sizeSelect && data.size) sizeSelect.value = data.size;
		if (colorSelect && data.color) colorSelect.value = data.color;

		var qtyEl = row.querySelector('.jm-cart-qty__val');
		var priceEl = row.querySelector('.jm-cart-line-price');
		if (qtyEl) qtyEl.textContent = String(data.qty);
		if (priceEl) priceEl.textContent = formatMoney(data.subtotal);
	}

	function handleResponse(data, rollback) {
		if (data.login_required) {
			window.location.href = cfg.loginUrl || '/dang-nhap';
			return;
		}
		if (!data.ok) {
			if (rollback) rollback();
			if (data.stock !== undefined || (data.message && data.message.indexOf('chỉ còn') !== -1)) {
				showStockNotice(data);
			} else {
				showFlash(data.message || 'Không thể cập nhật giỏ hàng.', true);
			}
			return;
		}
		updateTotals(data.total_items, data.total_price);
		if (data.action === 'qty') {
			applyQtyUpdate(data);
		} else if (data.action === 'options') {
			applyOptionsUpdate(data);
		} else if (data.action === 'delete') {
			if (data.empty) {
				window.location.reload();
				return;
			}
			if (data.rowid) {
				var row = findRow(data.rowid);
				if (row) row.remove();
				updateRowNumbers();
			}
		}
		if (data.message) {
			showFlash(data.message, false);
		}
	}

	function onQtyClick(e) {
		var btn = e.target.closest('.jm-cart-qty__btn');
		if (!btn || busy) return;
		e.preventDefault();

		var row = btn.closest('.jm-cart-row');
		if (!row) return;
		var rowid = row.getAttribute('data-rowid');
		var action = btn.getAttribute('data-action');
		if (!rowid || !action) return;

		var qtyEl = row.querySelector('.jm-cart-qty__val');
		var prevQty = parseInt(qtyEl.textContent, 10) || 1;
		var url = (cfg.updateUrl || '/gio-hang/update/') + encodeURIComponent(rowid) + '/' + action;

		setBusy(true);
		getJson(url)
			.then(function (data) {
				handleResponse(data, function () {
					if (qtyEl) qtyEl.textContent = String(prevQty);
				});
			})
			.catch(function () {
				showFlash('Không thể cập nhật số lượng.', true);
			})
			.finally(function () {
				setBusy(false);
			});
	}

	function onOptionsChange(e) {
		var select = e.target.closest('.jm-cart-opt__select');
		if (!select || busy) return;

		var form = select.closest('.jm-cart-opts-form');
		if (!form) return;

		var row = form.closest('.jm-cart-row');
		if (!row) return;

		var rowidInput = form.querySelector('input[name="rowid"]');
		var sizeSelect = form.querySelector('select[name="size"]');
		var colorSelect = form.querySelector('select[name="color"]');
		var prevSize = sizeSelect ? sizeSelect.getAttribute('data-prev') || sizeSelect.value : '';
		var prevColor = colorSelect ? colorSelect.getAttribute('data-prev') || colorSelect.value : '';

		var payload = {
			rowid: rowidInput ? rowidInput.value : row.getAttribute('data-rowid'),
			size: sizeSelect ? sizeSelect.value : (form.querySelector('input[name="size"]') || {}).value || '',
			color: colorSelect ? colorSelect.value : (form.querySelector('input[name="color"]') || {}).value || '',
		};

		setBusy(true);
		postForm(cfg.optionsUrl || '/gio-hang/cap-nhat-thuoc-tinh', payload)
			.then(function (data) {
				handleResponse(data, function () {
					if (sizeSelect) sizeSelect.value = prevSize;
					if (colorSelect) colorSelect.value = prevColor;
				});
				if (data.ok) {
					if (sizeSelect) sizeSelect.setAttribute('data-prev', sizeSelect.value);
					if (colorSelect) colorSelect.setAttribute('data-prev', colorSelect.value);
				}
			})
			.catch(function () {
				if (sizeSelect) sizeSelect.value = prevSize;
				if (colorSelect) colorSelect.value = prevColor;
				showFlash('Không thể đổi size/màu.', true);
			})
			.finally(function () {
				setBusy(false);
			});
	}

	function onRemoveClick(e) {
		var link = e.target.closest('.jm-cart-remove');
		if (!link || busy) return;
		if (!window.confirm('Xóa sản phẩm này khỏi giỏ hàng?')) {
			e.preventDefault();
			return;
		}
		e.preventDefault();
		setBusy(true);
		getJson(link.getAttribute('href'))
			.then(function (data) {
				handleResponse(data);
			})
			.catch(function () {
				showFlash('Không thể xóa sản phẩm.', true);
			})
			.finally(function () {
				setBusy(false);
			});
	}

	function onClearClick(e) {
		var link = e.target.closest('.jm-cart-clear');
		if (!link || busy) return;
		if (!window.confirm('Xóa toàn bộ giỏ hàng?')) {
			e.preventDefault();
			return;
		}
		e.preventDefault();
		setBusy(true);
		getJson(link.getAttribute('href'))
			.then(function (data) {
				handleResponse(data);
			})
			.catch(function () {
				showFlash('Không thể xóa giỏ hàng.', true);
			})
			.finally(function () {
				setBusy(false);
			});
	}

	function initSelectPrev() {
		document.querySelectorAll('.jm-cart-opt__select').forEach(function (sel) {
			sel.setAttribute('data-prev', sel.value);
		});
	}

	document.addEventListener('DOMContentLoaded', function () {
		var root = document.querySelector('.jm-cart-lux');
		if (!root) return;

		initSelectPrev();
		root.addEventListener('click', onQtyClick);
		root.addEventListener('change', onOptionsChange);
		root.addEventListener('click', onRemoveClick);
		root.addEventListener('click', onClearClick);
	});
})();
