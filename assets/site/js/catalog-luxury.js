/**
 * Catalog — auto-submit filter + AJAX pagination (no full-page flash)
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
		var $results = $('#jm-catalog-results');
		var submitLock = false;
		var pageLock = false;

		function beginLoading() {
			$root.addClass('is-filter-loading');
		}

		function beginPageLoading() {
			$root.addClass('is-page-loading');
		}

		function endPageLoading() {
			$root.removeClass('is-page-loading');
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

		function scrollToResults() {
			var el = $root.find('.jm-product-content')[0];
			if (!el) {
				return;
			}
			var stickyTop = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--wf-chrome-offset'), 10) || 120;
			var top = el.getBoundingClientRect().top + window.pageYOffset - stickyTop - 12;
			window.scrollTo({ top: Math.max(0, top), behavior: 'smooth' });
		}

		function loadCatalogPage(url, pushHistory) {
			if (pageLock || !$results.length) {
				return;
			}

			pageLock = true;
			beginPageLoading();

			fetch(url, {
				method: 'GET',
				credentials: 'same-origin',
				headers: {
					'X-Requested-With': 'XMLHttpRequest',
					Accept: 'text/html'
				}
			})
				.then(function (response) {
					if (!response.ok) {
						throw new Error('HTTP ' + response.status);
					}
					return response.text();
				})
				.then(function (html) {
					var doc = new DOMParser().parseFromString(html, 'text/html');
					var fresh = doc.getElementById('jm-catalog-results');
					if (!fresh) {
						window.location.href = url;
						return;
					}

					$results.css('opacity', 0);
					window.requestAnimationFrame(function () {
						$results.html(fresh.innerHTML);
						if (pushHistory !== false) {
							window.history.pushState({ catalogPage: url }, '', url);
						}
						$results.css('opacity', 1);
						scrollToResults();
					});
				})
				.catch(function () {
					window.location.href = url;
				})
				.finally(function () {
					endPageLoading();
					pageLock = false;
				});
		}

		if (formEl) {
			$root.find('.jm-size-label').each(function () {
				var input = this.querySelector('input[type="checkbox"]');
				if (input) {
					$(this).toggleClass('active', input.checked);
				}
			});

			$root.find('.jm-color-label').each(function () {
				var input = this.querySelector('input[type="checkbox"]');
				if (input) {
					$(this).toggleClass('active', input.checked);
				}
			});

			$(formEl).on('change', 'input[name="size[]"]', function () {
				$(this).closest('.jm-size-label').toggleClass('active', this.checked);
				submitFilterOnce();
			});

			$(formEl).on('change', 'input[name="color[]"]', function () {
				$(this).closest('.jm-color-label').toggleClass('active', this.checked);
				submitFilterOnce();
			});

			$(formEl).on('change', 'input[name="category[]"]', function () {
				submitFilterOnce();
			});

			$(formEl).on('change', 'input[name="price_range"]', function () {
				submitFilterOnce();
			});
		}

		$root.find('.jm-sort-box form').on('submit', function () {
			beginLoading();
		});

		$root.on('click', '.jm-catalog-pagination a[href]', function (event) {
			var href = this.getAttribute('href');
			if (!href || href.charAt(0) === '#') {
				return;
			}

			try {
				var target = new URL(href, window.location.origin);
				if (target.origin !== window.location.origin) {
					return;
				}
			} catch (e) {
				return;
			}

			event.preventDefault();
			loadCatalogPage(href, true);
		});

		window.addEventListener('popstate', function () {
			loadCatalogPage(window.location.href, false);
		});
	});
})(window.jQuery);
