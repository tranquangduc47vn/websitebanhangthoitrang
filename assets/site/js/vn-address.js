/**
 * Cascading Tỉnh / Quận-huyện / Phường-xã (Việt Nam) for auth & forms.
 */
(function (window, $) {
	'use strict';

	function VnAddressForm(options) {
		this.$province = $(options.province);
		this.$district = $(options.district);
		this.$ward = $(options.ward);
		this.dataUrl = options.dataUrl;
		this.provinces = null;
		this.initial = options.initial || {};
	}

	VnAddressForm.prototype.init = function () {
		var self = this;
		this.$province.prop('disabled', true);
		this.$district.prop('disabled', true);
		this.$ward.prop('disabled', true);

		this.$province.on('change', function () {
			self.fillDistricts($(this).val(), '');
			self.$ward.html(self.placeholderOption('— Chọn phường / xã —')).prop('disabled', true);
		});
		this.$district.on('change', function () {
			self.fillWards(self.$province.val(), $(this).val(), '');
		});

		return $.getJSON(this.dataUrl).then(function (data) {
			self.provinces = data;
			self.fillProvinces(self.initial.province_id || '');
			if (self.initial.province_id) {
				self.fillDistricts(self.initial.province_id, self.initial.district_id || '');
			}
			if (self.initial.province_id && self.initial.district_id) {
				self.fillWards(self.initial.province_id, self.initial.district_id, self.initial.ward_id || '');
			}
		});
	};

	VnAddressForm.prototype.placeholderOption = function (label) {
		return $('<option>').val('').text(label);
	};

	VnAddressForm.prototype.findProvince = function (id) {
		if (!this.provinces || !id) return null;
		id = String(id);
		for (var i = 0; i < this.provinces.length; i++) {
			if (String(this.provinces[i].Id) === id) {
				return this.provinces[i];
			}
		}
		return null;
	};

	VnAddressForm.prototype.fillProvinces = function (selectedId) {
		var $sel = this.$province;
		$sel.empty().append(this.placeholderOption('— Chọn tỉnh / thành phố —'));
		if (!this.provinces) {
			return;
		}
		$.each(this.provinces, function (_, p) {
			$sel.append($('<option>').val(p.Id).text(p.Name));
		});
		if (selectedId) {
			$sel.val(String(selectedId));
		}
		$sel.prop('disabled', false);
	};

	VnAddressForm.prototype.fillDistricts = function (provinceId, selectedId) {
		var $sel = this.$district;
		$sel.empty().append(this.placeholderOption('— Chọn quận / huyện —'));
		var province = this.findProvince(provinceId);
		if (!province || !province.Districts) {
			$sel.prop('disabled', true);
			return;
		}
		$.each(province.Districts, function (_, d) {
			$sel.append($('<option>').val(d.Id).text(d.Name));
		});
		if (selectedId) {
			$sel.val(String(selectedId));
		}
		$sel.prop('disabled', false);
	};

	VnAddressForm.prototype.fillWards = function (provinceId, districtId, selectedId) {
		var $sel = this.$ward;
		$sel.empty().append(this.placeholderOption('— Chọn phường / xã —'));
		var province = this.findProvince(provinceId);
		if (!province || !province.Districts) {
			$sel.prop('disabled', true);
			return;
		}
		var district = null;
		districtId = String(districtId);
		for (var i = 0; i < province.Districts.length; i++) {
			if (String(province.Districts[i].Id) === districtId) {
				district = province.Districts[i];
				break;
			}
		}
		if (!district || !district.Wards) {
			$sel.prop('disabled', true);
			return;
		}
		$.each(district.Wards, function (_, w) {
			$sel.append($('<option>').val(w.Id).text(w.Name));
		});
		if (selectedId) {
			$sel.val(String(selectedId));
		}
		$sel.prop('disabled', false);
	};

	window.VnAddressForm = VnAddressForm;
})(window, jQuery);
