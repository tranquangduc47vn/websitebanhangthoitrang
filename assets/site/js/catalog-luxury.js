/**
 * Catalog page — filter UX; single submit + lighter collapse
 */
(function ($) {
	'use strict';

	if (!$) {
		return;
	}

	$(function () {
		var $root = $('.jm-catalog-lux');
		if (!$root.length) {
			return;
		}

		var formEl = document.getElementById('filter-form');
		if (!formEl) {
			return;
		}

		var submitLock = false;

		function beginLoading() {
			$root.addClass('is-filter-loading');
		}

		function submitFilterOnce() {
			if (submitLock) {
				return;
			}
			submitLock = true;
			beginLoading();
			window.requestAnimationFrame(function () {
				formEl.submit();
			});
		}

		$root.find('.jm-size-label').on('click', function (e) {
			e.preventDefault();
			var input = this.querySelector('input[type="checkbox"]');
			if (!input) {
				return;
			}
			input.checked = !input.checked;
			submitFilterOnce();
		});

		$root.find('.jm-color-label').on('click', function (e) {
			e.preventDefault();
			var input = this.querySelector('input[type="checkbox"]');
			if (!input) {
				return;
			}
			input.checked = !input.checked;
			submitFilterOnce();
		});

		$(formEl).on('change', 'input[name="category[]"]', function () {
			submitFilterOnce();
		});

		$(formEl).on('change', 'input[name="price_range"]', function () {
			submitFilterOnce();
		});

		$root.find('.jm-sort-box form').on('submit', function () {
			beginLoading();
		});

		function toggleFilterPanel($toggle) {
			var selector = $toggle.attr('data-target');
			if (!selector) {
				return;
			}
			var panel = document.querySelector(selector);
			if (!panel) {
				return;
			}
			var willOpen = !panel.classList.contains('in');
			panel.classList.toggle('in', willOpen);
			$toggle.toggleClass('collapsed', !willOpen);
			$toggle.attr('aria-expanded', willOpen ? 'true' : 'false');
		}

		$root.find('.jm-filter-toggle').on('click', function (e) {
			e.preventDefault();
			toggleFilterPanel($(this));
		});

		$root.find('.jm-filter-toggle').on('keydown', function (e) {
			if (e.key === 'Enter' || e.key === ' ') {
				e.preventDefault();
				toggleFilterPanel($(this));
			}
		});
	});
})(window.jQuery);
