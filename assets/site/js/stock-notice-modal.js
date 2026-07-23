(function (window) {
	'use strict';

	function showStockNotice(opts) {
		opts = opts || {};
		var modal = document.getElementById('qdStockNoticeModal');
		if (!modal) {
			return false;
		}

		var name = opts.product_name || opts.productName || 'Sản phẩm';
		var size = opts.size || '—';
		var color = opts.color || '—';
		var stock = typeof opts.stock === 'number' ? opts.stock : parseInt(opts.stock, 10);
		if (isNaN(stock)) {
			stock = 0;
		}

		var nameEl = document.getElementById('qdStockNoticeProductName');
		var colorEl = document.getElementById('qdStockNoticeColor');
		var sizeEl = document.getElementById('qdStockNoticeSize');
		var stockEl = document.getElementById('qdStockNoticeStockLine');

		if (nameEl) nameEl.textContent = name;
		if (colorEl) colorEl.textContent = color;
		if (sizeEl) sizeEl.textContent = size;
		if (stockEl) {
			stockEl.textContent = 'Hiện chỉ còn ' + stock + ' sản phẩm trong kho.';
		}

		if (window.jQuery && window.jQuery.fn.modal) {
			window.jQuery(modal).modal('show');
		}

		return true;
	}

	window.QD = window.QD || {};
	window.QD.showStockNotice = showStockNotice;

	document.addEventListener('DOMContentLoaded', function () {
		if (window.JM_STOCK_NOTICE) {
			showStockNotice(window.JM_STOCK_NOTICE);
		}
	});
})(window);
