/**
 * Product detail gallery: thumbnails + smooth CSS zoom (no jqzoom).
 */
(function ($) {
	'use strict';

	var ZOOM_LEVEL = 4.0;

	function getThumbUrls($link) {
		var small = $link.attr('data-small');
		if (!small) {
			return null;
		}
		return small;
	}

	function scrollThumbIntoView($link) {
		var $container = $('.jm-pdp-lux .jm-thumb-vertical-box');
		var $li = $link.parent();
		if (!$container.length || !$li.length) {
			return;
		}
		var containerTop = $container.scrollTop();
		var containerBottom = containerTop + $container.height();
		var elemTop = $li.position().top + containerTop;
		var elemBottom = elemTop + $li.outerHeight();
		if (elemTop < containerTop) {
			$container.animate({ scrollTop: elemTop - 10 }, 160);
		} else if (elemBottom > containerBottom) {
			$container.animate({ scrollTop: elemBottom - $container.height() + 10 }, 160);
		}
	}

	function resetMainZoom($stage) {
		var $img = $stage.find('.jm-pdp-main-img');
		$img.css({
			transform: 'scale(1)',
			transformOrigin: '50% 50%'
		});
		$stage.removeClass('is-zoom-active');
	}

	function selectThumbnail($link) {
		var url = getThumbUrls($link);
		if (!url) {
			return;
		}

		$('#thumblist li a').removeClass('zoomThumbActive');
		$link.addClass('zoomThumbActive');

		var $img = $('#jm-pdp-main-img');
		var $stage = $('#jm-pdp-main-stage');
		resetMainZoom($stage);

		$img.one('load.jmPdp', function () {
			resetMainZoom($stage);
		});
		$img.attr('src', url);

		scrollThumbIntoView($link);
	}

	function initSmoothZoom() {
		var $stage = $('#jm-pdp-main-stage');
		var $img = $('#jm-pdp-main-img');
		if (!$stage.length || !$img.length) {
			return;
		}

		var pending = false;
		var lastX = 0.5;
		var lastY = 0.5;

		function applyZoom() {
			pending = false;
			if (!$stage.hasClass('is-zoom-active')) {
				return;
			}
			var px = Math.round(lastX * 1000) / 10;
			var py = Math.round(lastY * 1000) / 10;
			$img.css({
				transform: 'scale(' + ZOOM_LEVEL + ')',
				transformOrigin: px + '% ' + py + '%'
			});
		}

		$stage.on('mouseenter', function () {
			$stage.addClass('is-zoom-active');
		});

		$stage.on('mouseleave', function () {
			resetMainZoom($stage);
		});

		$stage.on('mousemove', function (e) {
			var rect = $stage[0].getBoundingClientRect();
			if (!rect.width || !rect.height) {
				return;
			}
			lastX = (e.clientX - rect.left) / rect.width;
			lastY = (e.clientY - rect.top) / rect.height;
			lastX = Math.max(0, Math.min(1, lastX));
			lastY = Math.max(0, Math.min(1, lastY));
			if (!pending) {
				pending = true;
				window.requestAnimationFrame(applyZoom);
			}
		});

		// Wheel on thumbnail column only scrolls thumbs — stop propagation from bubbling weirdly
		$('.jm-pdp-lux .jm-thumb-vertical-box').on('wheel', function (e) {
			e.stopPropagation();
		});
	}

	function initThumbnails() {
		$(document).off('click.jmPdpThumb', '#thumblist li a');
		$(document).on('click.jmPdpThumb', '#thumblist li a', function (e) {
			e.preventDefault();
			selectThumbnail($(this));
			return false;
		});
	}

	$(function () {
		if (!$('.jm-pdp-lux').length) {
			return;
		}
		initThumbnails();
		initSmoothZoom();
	});
})(jQuery);
