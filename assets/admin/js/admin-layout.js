(function () {
	'use strict';

	function initBackForwardCacheGuard() {
		window.addEventListener('pageshow', function (event) {
			if (!event.persisted) {
				return;
			}
			if (!document.body || !document.body.classList.contains('admin-app')) {
				return;
			}
			window.location.reload();
		});
	}

	initBackForwardCacheGuard();

	function initLogout() {
		var logout = document.getElementById('logout');
		if (!logout) {
			return;
		}
		logout.addEventListener('click', function (e) {
			var target = logout.getAttribute('href');
			if (!target || target === '#' || target.indexOf('javascript:') === 0) {
				return;
			}
			e.preventDefault();
			window.location.href = target;
		});
	}

	function initSidebarAutoClose() {
		var sidebar = document.getElementById('adminSidebar');
		if (!sidebar || typeof bootstrap === 'undefined') {
			return;
		}
		sidebar.querySelectorAll('.nav-link').forEach(function (link) {
			link.addEventListener('click', function () {
				if (window.innerWidth < 992) {
					var instance = bootstrap.Offcanvas.getInstance(sidebar);
					if (instance) {
						instance.hide();
					}
				}
			});
		});
	}

	function dismissAdminAlert(alert) {
		if (!alert || !alert.parentNode || alert.getAttribute('data-admin-dismissed') === '1') {
			return;
		}
		alert.setAttribute('data-admin-dismissed', '1');
		alert.classList.remove('show');
		window.setTimeout(function () {
			if (alert.parentNode) {
				alert.remove();
			}
		}, 350);
	}

	function initFlashAutoDismiss() {
		document.querySelectorAll('.admin-flash-stack .alert').forEach(function (alert) {
			if (alert.getAttribute('data-admin-autodismiss-init') === '1') {
				return;
			}
			alert.setAttribute('data-admin-autodismiss-init', '1');

			var ms = parseInt(alert.getAttribute('data-admin-auto-dismiss'), 10);
			if (isNaN(ms) || ms < 1) {
				ms = 5000;
			}
			window.setTimeout(function () {
				dismissAdminAlert(alert);
			}, ms);
		});
	}

	function initAlertDismiss() {
		document.querySelectorAll('[data-admin-dismiss="alert"]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var alert = btn.closest('.alert');
				if (alert) {
					dismissAdminAlert(alert);
				}
			});
		});
	}

	function initDarkModeToggle() {
		var toggle = document.getElementById('adminThemeToggle');
		var body = document.body;
		if (!toggle || !body) {
			return;
		}

		var THEME_KEY = 'webshop-admin-theme';
		var icon = toggle.querySelector('[data-theme-icon]');

		function getStoredDark() {
			try {
				return localStorage.getItem(THEME_KEY) === 'dark';
			} catch (e) {
				return false;
			}
		}

		function applyAdminTheme(isDark) {
			body.classList.toggle('admin-dark', isDark);
			body.setAttribute('data-bs-theme', isDark ? 'dark' : 'light');
			if (icon) {
				icon.className = isDark ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
			}
			toggle.setAttribute('aria-pressed', isDark ? 'true' : 'false');
			toggle.setAttribute('title', isDark ? 'Bật giao diện sáng' : 'Bật dark mode');
			try {
				localStorage.setItem(THEME_KEY, isDark ? 'dark' : 'light');
			} catch (e) {}
		}

		applyAdminTheme(body.classList.contains('admin-dark') || getStoredDark());

		toggle.addEventListener('click', function () {
			applyAdminTheme(!body.classList.contains('admin-dark'));
		});
	}

	function initTableSearch() {
		document.querySelectorAll('[data-admin-table-search]').forEach(function (input) {
			var targetId = input.getAttribute('data-admin-table-search');
			var table = document.getElementById(targetId);
			if (!table) {
				return;
			}
			var rows = table.querySelectorAll('tbody tr');
			input.addEventListener('input', function () {
				var q = input.value.trim().toLowerCase();
				rows.forEach(function (row) {
					var text = row.textContent.toLowerCase();
					row.style.display = !q || text.indexOf(q) !== -1 ? '' : 'none';
				});
			});
		});
	}

	document.addEventListener('DOMContentLoaded', function () {
		initLogout();
		initSidebarAutoClose();
		initAlertDismiss();
		initFlashAutoDismiss();
		initDarkModeToggle();
		initTableSearch();

		if (window.jQuery && jQuery.fn.datepicker) {
			jQuery('#calendar').datepicker();
		}
	});

	window.addEventListener('load', function () {
		initFlashAutoDismiss();
	});
})();
