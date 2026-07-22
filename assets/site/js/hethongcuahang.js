jQuery(document).ready(function($) {

	$(document).on('click', '.store-item', function() {
		var newMapSrc = $(this).attr('data-maps');

		if (newMapSrc && newMapSrc !== '') {
			$('#store-map').attr('src', newMapSrc);
		}

		$('.store-item').css('background', 'transparent');
		$('.store-item').find('.store-name').css('color', '#111');

		$(this).css('background', '#f0f4f8');
		$(this).find('.store-name').css('color', '#0056b3');
	});

	// Lọc tỉnh/thành — dùng inline !important để không bị CSS theme ghi đè
	$('#select-city').on('change', function() {
		var selectedCity = $(this).val().trim().toLowerCase();

		if (selectedCity === 'all') {
			$('.store-item').setProperty('display', 'block', 'important');
			$('.store-item').css('display', '');
		} else {
			$('.store-item').each(function() {
				var storeCity = $(this).attr('data-city') ? $(this).attr('data-city').trim().toLowerCase() : '';

				if (storeCity.indexOf(selectedCity) !== -1 || selectedCity.indexOf(storeCity) !== -1) {
					this.style.setProperty('display', 'block', 'important');
				} else {
					this.style.setProperty('display', 'none', 'important');
				}
			});
		}

		$('.store-item').filter(function() {
			return $(this).css('display') !== 'none';
		}).first().trigger('click');
	});

	$('.store-item').first().trigger('click');
});
