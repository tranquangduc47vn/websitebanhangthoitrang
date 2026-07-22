(function ($) {
	'use strict';

	if (!$ || !$.fn) {
		return;
	}

	var $root = $('.jm-header-icons');
	if (!$root.length) {
		return;
	}

	$root.find('.jm-icon-slot').each(function () {
		var $slot = $(this);
		var $panel = $slot.find('.jm-icon-popover');
		var closeTimer = null;
		var $toggle = $slot.find('.jm-icon-toggle');

		function open() {
			clearTimeout(closeTimer);
			$root.find('.jm-icon-slot.is-open').not($slot).removeClass('is-open')
				.find('.jm-icon-toggle').attr('aria-expanded', 'false');
			$slot.addClass('is-open');
			$toggle.attr('aria-expanded', 'true');
		}

		function scheduleClose() {
			clearTimeout(closeTimer);
			closeTimer = setTimeout(function () {
				$slot.removeClass('is-open');
				$toggle.attr('aria-expanded', 'false');
			}, 380);
		}

		$slot.on('mouseenter', open);
		$slot.on('mouseleave', scheduleClose);
		if ($panel.length) {
			$panel.on('mouseenter', open);
			$panel.on('mouseleave', scheduleClose);
		}
	});
})(window.jQuery);
