(function () {
	'use strict';

	var cfg = window.STOCK_RECEIPT_FORM || {};
	var urls = cfg.urls || {};
	var state = {
		page: 1,
		pages: 1,
		total: 0,
		loading: false,
		lines: {},
	};

	var els = {};

	function $(id) {
		return document.getElementById(id);
	}

	function escHtml(str) {
		return String(str == null ? '' : str)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;');
	}

	function formatMoney(n) {
		return (Number(n) || 0).toLocaleString('vi-VN');
	}

	function debounce(fn, ms) {
		var t;
		return function () {
			var args = arguments;
			var ctx = this;
			clearTimeout(t);
			t = setTimeout(function () {
				fn.apply(ctx, args);
			}, ms);
		};
	}

	function currentFilters() {
		return {
			catalog_id: els.catalog.value || '',
			product_id: els.product.value || '',
			color: els.color.value || '',
			size: els.size.value || '',
			q: els.q.value.trim(),
		};
	}

	function buildQuery(params) {
		var parts = [];
		Object.keys(params).forEach(function (key) {
			var val = params[key];
			if (val !== '' && val != null) {
				parts.push(encodeURIComponent(key) + '=' + encodeURIComponent(val));
			}
		});
		return parts.join('&');
	}

	function fetchJson(url) {
		return fetch(url, {
			headers: { 'X-Requested-With': 'XMLHttpRequest' },
			credentials: 'same-origin',
		}).then(function (res) {
			if (!res.ok) {
				throw new Error('HTTP ' + res.status);
			}
			return res.json();
		});
	}

	function pickerSubtotal(qty, cost) {
		return (parseFloat(qty) || 0) * (parseFloat(cost) || 0);
	}

	function updatePickerRowSubtotal(row) {
		var qtyInput = row.querySelector('.picker-qty');
		var costInput = row.querySelector('.picker-cost');
		var subtotalEl = row.querySelector('.picker-subtotal');
		if (!subtotalEl) return;
		subtotalEl.textContent = formatMoney(pickerSubtotal(qtyInput.value, costInput.value));
	}

	function stockCell(item) {
		var cls = 'text-' + (item.stock_class || 'secondary');
		var warn = item.stock <= 0
			? ' <span class="badge bg-danger ms-1 stock-badge-out" title="Tồn kho = 0">!</span>'
			: '';
		return '<span class="' + cls + '">' + formatMoney(item.stock) + '</span>' + warn;
	}

	function renderPickerRows(items) {
		window.__pickerItems = window.__pickerItems || {};
		if (!items.length) {
			els.pickerBody.innerHTML = '<tr class="picker-empty"><td colspan="9" class="text-center text-muted py-4">Không tìm thấy biến thể phù hợp.</td></tr>';
			return;
		}

		items.forEach(function (item) {
			window.__pickerItems[item.id] = item;
		});

		els.pickerBody.innerHTML = items.map(function (item) {
			var cost = item.cost_price > 0 ? item.cost_price : '';
			var qty = 1;
			var sub = pickerSubtotal(qty, cost);
			var inReceipt = !!state.lines[item.id];
			var btnClass = inReceipt ? 'btn-outline-secondary' : 'btn-outline-primary';
			var btnLabel = inReceipt ? 'Đã thêm' : 'Thêm';
			var btnDisabled = inReceipt ? ' disabled' : '';
			return '<tr data-variant-id="' + item.id + '">' +
				'<td><code class="small">' + escHtml(item.sku) + '</code></td>' +
				'<td>' + escHtml(item.product_name) + '</td>' +
				'<td>' + escHtml(item.color || '—') + '</td>' +
				'<td>' + escHtml(item.size || '—') + '</td>' +
				'<td class="text-end">' + stockCell(item) + '</td>' +
				'<td><input type="number" class="form-control form-control-sm picker-cost" min="0" step="1000" value="' + escHtml(cost) + '"></td>' +
				'<td><input type="number" class="form-control form-control-sm picker-qty" min="1" max="999999" value="1"></td>' +
				'<td class="text-end picker-subtotal small">' + formatMoney(sub) + '</td>' +
				'<td><button type="button" class="btn btn-sm ' + btnClass + ' btn-picker-add"' + btnDisabled + '>' + btnLabel + '</button></td>' +
				'</tr>';
		}).join('');

		els.pickerBody.querySelectorAll('tr[data-variant-id]').forEach(function (row) {
			row.querySelectorAll('.picker-qty, .picker-cost').forEach(function (input) {
				input.addEventListener('input', function () {
					updatePickerRowSubtotal(row);
				});
			});
		});
	}

	function updatePickerMeta() {
		if (state.total === 0) {
			els.summary.textContent = 'Không có biến thể nào khớp bộ lọc.';
		} else {
			els.summary.textContent = 'Tìm thấy ' + formatMoney(state.total) + ' biến thể · trang ' + state.page + '/' + state.pages;
		}
		els.pagination.hidden = state.pages <= 1;
		els.pageLabel.textContent = state.page + '/' + state.pages;
		els.prev.disabled = state.page <= 1 || state.loading;
		els.next.disabled = state.page >= state.pages || state.loading;
	}

	function loadVariants(page) {
		if (state.loading) return;
		state.loading = true;
		state.page = page || 1;
		var params = currentFilters();
		params.page = state.page;
		params.per_page = 25;

		els.summary.textContent = 'Đang tải…';
		fetchJson(urls.search_variants + '?' + buildQuery(params))
			.then(function (data) {
				if (!data.ok) throw new Error(data.message || 'Lỗi tìm kiếm');
				state.total = data.total || 0;
				state.pages = data.pages || 1;
				renderPickerRows(data.items || []);
				updatePickerMeta();
			})
			.catch(function () {
				els.pickerBody.innerHTML = '<tr><td colspan="9" class="text-center text-danger py-4">Không tải được danh sách biến thể.</td></tr>';
				els.summary.textContent = 'Lỗi tải dữ liệu.';
			})
			.finally(function () {
				state.loading = false;
				updatePickerMeta();
			});
	}

	function refreshPickerAddButtons() {
		els.pickerBody.querySelectorAll('tr[data-variant-id]').forEach(function (row) {
			var id = parseInt(row.getAttribute('data-variant-id'), 10);
			var btn = row.querySelector('.btn-picker-add');
			if (!btn) return;
			if (state.lines[id]) {
				btn.disabled = true;
				btn.textContent = 'Đã thêm';
				btn.classList.remove('btn-outline-primary');
				btn.classList.add('btn-outline-secondary');
			}
		});
	}

	function recalcRow(row) {
		var qty = parseFloat(row.querySelector('.line-qty').value) || 0;
		var cost = parseFloat(row.querySelector('.line-cost').value) || 0;
		row.querySelector('.line-subtotal').textContent = formatMoney(qty * cost);
	}

	function recalcTotal() {
		var total = 0;
		Object.keys(state.lines).forEach(function (id) {
			var line = state.lines[id];
			total += (line.qty || 0) * (line.unit_cost || 0);
		});
		els.total.textContent = formatMoney(total);
		els.lineCount.textContent = Object.keys(state.lines).length + ' dòng';
	}

	function renderReceiptLines() {
		var ids = Object.keys(state.lines);
		if (!ids.length) {
			els.linesBody.innerHTML = '<tr class="receipt-empty" id="receipt-empty-row"><td colspan="8" class="text-center text-muted py-4">Chưa có dòng nhập. Thêm biến thể từ bảng phía trên.</td></tr>';
			recalcTotal();
			return;
		}

		els.linesBody.innerHTML = ids.map(function (id) {
			var line = state.lines[id];
			var sub = (line.qty || 0) * (line.unit_cost || 0);
			return '<tr class="receipt-line" data-variant-id="' + id + '">' +
				'<td><code class="small">' + escHtml(line.sku) + '</code></td>' +
				'<td>' + escHtml(line.product_name) + '</td>' +
				'<td>' + escHtml(line.color || '—') + '</td>' +
				'<td>' + escHtml(line.size || '—') + '</td>' +
				'<td><input type="number" name="qty[]" class="form-control form-control-sm line-qty" min="1" max="999999" value="' + line.qty + '" required></td>' +
				'<td class="line-subtotal text-end small">' + formatMoney(sub) + '</td>' +
				'<td><input type="number" name="unit_cost[]" class="form-control form-control-sm line-cost" min="0" step="1000" value="' + line.unit_cost + '"></td>' +
				'<td>' +
					'<input type="hidden" name="variant_id[]" value="' + id + '">' +
					'<button type="button" class="btn btn-sm btn-outline-danger btn-remove-line" title="Xóa">&times;</button>' +
				'</td>' +
				'</tr>';
		}).join('');

		els.linesBody.querySelectorAll('.receipt-line').forEach(bindReceiptRow);
		recalcTotal();
	}

	function bindReceiptRow(row) {
		var id = parseInt(row.getAttribute('data-variant-id'), 10);
		row.querySelector('.line-qty').addEventListener('input', function () {
			state.lines[id].qty = parseInt(this.value, 10) || 0;
			recalcRow(row);
			recalcTotal();
		});
		row.querySelector('.line-cost').addEventListener('input', function () {
			state.lines[id].unit_cost = parseFloat(this.value) || 0;
			recalcRow(row);
			recalcTotal();
		});
		row.querySelector('.btn-remove-line').addEventListener('click', function () {
			delete state.lines[id];
			renderReceiptLines();
			refreshPickerAddButtons();
		});
		recalcRow(row);
	}

	function addLineFromItem(item, qty, unitCost) {
		var id = item.id;
		if (state.lines[id]) {
			return;
		}
		state.lines[id] = {
			variant_id: id,
			sku: item.sku,
			product_name: item.product_name,
			color: item.color,
			size: item.size,
			qty: Math.max(1, parseInt(qty, 10) || 1),
			unit_cost: unitCost != null && unitCost !== '' ? parseFloat(unitCost) || 0 : (parseFloat(item.cost_price) || 0),
		};
		renderReceiptLines();
		refreshPickerAddButtons();
	}

	function renderRecentVariants(items) {
		if (!items || !items.length) {
			els.recentWrap.hidden = true;
			els.recentList.innerHTML = '';
			return;
		}
		els.recentWrap.hidden = false;
		els.recentList.innerHTML = items.map(function (item) {
			var meta = [item.color || '—', item.size || '—'].join(' / ');
			return '<div class="recent-variant-chip" data-variant-id="' + item.id + '">' +
				'<span><code class="small">' + escHtml(item.sku) + '</code></span>' +
				'<span class="chip-meta">' + escHtml(item.product_name) + ' · ' + escHtml(meta) + '</span>' +
				'<button type="button" class="btn btn-sm btn-outline-primary btn-recent-add">Thêm</button>' +
				'</div>';
		}).join('');
	}

	function loadRecentVariants() {
		if (!urls.recent_variants) return;
		fetchJson(urls.recent_variants)
			.then(function (data) {
				if (!data.ok) return;
				window.__recentVariantCache = data.items || [];
				renderRecentVariants(data.items || []);
			})
			.catch(function () {
				els.recentWrap.hidden = true;
			});
	}

	function refillFilterOptions() {
		var params = {
			catalog_id: els.catalog.value || '',
			product_id: els.product.value || '',
		};
		return fetchJson(urls.filter_options + '?' + buildQuery(params))
			.then(function (data) {
				if (!data.ok) return;
				var colorVal = els.color.value;
				var sizeVal = els.size.value;
				els.color.innerHTML = '<option value="">— Tất cả —</option>' +
					(data.colors || []).map(function (c) {
						return '<option value="' + escHtml(c) + '">' + escHtml(c) + '</option>';
					}).join('');
				els.size.innerHTML = '<option value="">— Tất cả —</option>' +
					(data.sizes || []).map(function (s) {
						return '<option value="' + escHtml(s) + '">' + escHtml(s) + '</option>';
					}).join('');
				if (colorVal && (data.colors || []).indexOf(colorVal) >= 0) els.color.value = colorVal;
				if (sizeVal && (data.sizes || []).indexOf(sizeVal) >= 0) els.size.value = sizeVal;
			});
	}

	function initTomSelect() {
		els.productTom = new TomSelect('#filter-product', {
			valueField: 'id',
			labelField: 'text',
			searchField: ['text'],
			placeholder: 'Tìm sản phẩm…',
			maxItems: 1,
			loadThrottle: 300,
			load: function (query, callback) {
				var params = {
					q: query,
					page: 1,
					catalog_id: els.catalog.value || '',
				};
				fetchJson(urls.filter_products + '?' + buildQuery(params))
					.then(function (data) {
						callback(data.results || []);
					})
					.catch(function () {
						callback();
					});
			},
			render: {
				option: function (data, escape) {
					return '<div>' + escape(data.text) + '</div>';
				},
				item: function (data, escape) {
					return '<div>' + escape(data.text) + '</div>';
				},
			},
		});
	}

	function bindEvents() {
		var triggerSearch = debounce(function () {
			loadVariants(1);
		}, 350);

		els.q.addEventListener('keydown', function (e) {
			if (e.key === 'Enter') {
				e.preventDefault();
				loadVariants(1);
			}
		});
		els.q.addEventListener('input', triggerSearch);

		els.catalog.addEventListener('change', function () {
			els.productTom.clear(true);
			els.productTom.clearOptions();
			refillFilterOptions().then(function () {
				loadVariants(1);
			});
		});

		els.productTom.on('change', function () {
			refillFilterOptions().then(function () {
				loadVariants(1);
			});
		});

		els.color.addEventListener('change', function () {
			loadVariants(1);
		});
		els.size.addEventListener('change', function () {
			loadVariants(1);
		});

		els.prev.addEventListener('click', function () {
			if (state.page > 1) loadVariants(state.page - 1);
		});
		els.next.addEventListener('click', function () {
			if (state.page < state.pages) loadVariants(state.page + 1);
		});

		els.pickerBody.addEventListener('click', function (e) {
			var btn = e.target.closest('.btn-picker-add');
			if (!btn || btn.disabled) return;
			var row = btn.closest('tr[data-variant-id]');
			if (!row) return;
			var id = parseInt(row.getAttribute('data-variant-id'), 10);
			var item = (window.__pickerItems || {})[id];
			if (!item) return;
			var qty = row.querySelector('.picker-qty').value;
			var cost = row.querySelector('.picker-cost').value;
			addLineFromItem(item, qty, cost);
		});

		els.recentList.addEventListener('click', function (e) {
			var btn = e.target.closest('.btn-recent-add');
			if (!btn) return;
			var chip = btn.closest('.recent-variant-chip');
			var id = parseInt(chip.getAttribute('data-variant-id'), 10);
			var items = (window.__recentVariantCache || []).filter(function (x) {
				return x.id === id;
			});
			if (items.length) {
				addLineFromItem(items[0], 1, items[0].cost_price);
			}
		});

		els.form.addEventListener('submit', function (e) {
			if (!Object.keys(state.lines).length) {
				e.preventDefault();
				alert('Vui lòng thêm ít nhất một dòng biến thể vào phiếu nhập.');
			}
		});
	}

	function loadInitialLines() {
		(cfg.initialLines || []).forEach(function (line) {
			state.lines[line.variant_id] = {
				variant_id: line.variant_id,
				sku: line.sku,
				product_name: line.product_name,
				color: line.color,
				size: line.size,
				qty: line.qty,
				unit_cost: line.unit_cost,
			};
		});
		renderReceiptLines();
	}

	function init() {
		els.form = $('stock-receipt-form');
		if (!els.form || !urls.search_variants) return;

		els.catalog = $('filter-catalog');
		els.product = $('filter-product');
		els.color = $('filter-color');
		els.size = $('filter-size');
		els.q = $('filter-q');
		els.pickerBody = $('variant-picker-body');
		els.summary = $('variant-picker-summary');
		els.pagination = $('variant-picker-pagination');
		els.prev = $('picker-prev');
		els.next = $('picker-next');
		els.pageLabel = $('picker-page-label');
		els.linesBody = $('receipt-lines-body');
		els.total = $('receipt-total');
		els.lineCount = $('receipt-line-count');
		els.recentWrap = $('recent-variants-wrap');
		els.recentList = $('recent-variants-list');

		initTomSelect();
		bindEvents();
		loadInitialLines();
		loadRecentVariants();

		refillFilterOptions().then(function () {
			loadVariants(1);
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
