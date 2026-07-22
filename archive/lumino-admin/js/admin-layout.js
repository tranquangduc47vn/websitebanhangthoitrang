(function () {
	'use strict';

	document.addEventListener('DOMContentLoaded', function () {
		var logout = document.getElementById('logout');
		if (logout) {
		logout.addEventListener('click', function (e) {
			var target = logout.getAttribute('href');
			if (!target || target === '#' || target.indexOf('javascript:') === 0) {
				return;
			}
			e.preventDefault();
			window.location.href = target;
		});
		}

		var sidebar = document.getElementById('adminSidebar');
		if (sidebar && typeof bootstrap !== 'undefined') {
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

		document.querySelectorAll('[data-admin-dismiss="alert"]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var alert = btn.closest('.alert');
				if (alert) {
					alert.remove();
				}
			});
		});

		if (window.jQuery && jQuery.fn.datepicker) {
			jQuery('#calendar').datepicker();
		}
	});
})();
