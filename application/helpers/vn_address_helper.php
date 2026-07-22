<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// VN admin divisions: assets/site/data/vn-hanh-chinh.json

if (!function_exists('vn_address_data_path')) {
	function vn_address_data_path()
	{
		return FCPATH . 'assets/site/data/vn-hanh-chinh.json';
	}
}

if (!function_exists('vn_address_get_provinces')) {
	function vn_address_get_provinces()
	{
		static $provinces = null;
		if ($provinces !== null) {
			return $provinces;
		}
		$path = vn_address_data_path();
		if (!is_readable($path)) {
			$provinces = array();
			return $provinces;
		}
		$raw = json_decode(file_get_contents($path), true);
		if (!is_array($raw)) {
			$provinces = array();
			return $provinces;
		}
		$provinces = $raw;
		return $provinces;
	}
}

if (!function_exists('vn_address_lookup')) {
	function vn_address_lookup($province_id, $district_id, $ward_id)
	{
		$province_id = trim((string) $province_id);
		$district_id = trim((string) $district_id);
		$ward_id = trim((string) $ward_id);
		if ($province_id === '' || $district_id === '' || $ward_id === '') {
			return false;
		}

		foreach (vn_address_get_provinces() as $province) {
			if (!isset($province['Id']) || (string) $province['Id'] !== $province_id) {
				continue;
			}
			if (empty($province['Districts']) || !is_array($province['Districts'])) {
				return false;
			}
			foreach ($province['Districts'] as $district) {
				if (!isset($district['Id']) || (string) $district['Id'] !== $district_id) {
					continue;
				}
				if (empty($district['Wards']) || !is_array($district['Wards'])) {
					return false;
				}
				foreach ($district['Wards'] as $ward) {
					if (isset($ward['Id']) && (string) $ward['Id'] === $ward_id) {
						return array(
							'province_name' => $province['Name'],
							'district_name' => $district['Name'],
							'ward_name' => $ward['Name'],
						);
					}
				}
				return false;
			}
			return false;
		}
		return false;
	}
}

if (!function_exists('vn_address_format_line')) {
	function vn_address_format_line($province_id, $district_id, $ward_id, $address_note)
	{
		$lookup = vn_address_lookup($province_id, $district_id, $ward_id);
		if (!$lookup) {
			return '';
		}
		$note = trim(preg_replace('/\s+/u', ' ', (string) $address_note));
		$parts = array();
		if ($note !== '') {
			$parts[] = $note;
		}
		$parts[] = $lookup['ward_name'];
		$parts[] = $lookup['district_name'];
		$parts[] = $lookup['province_name'];
		return implode(', ', $parts);
	}
}

if (!function_exists('vn_address_json_url')) {
	function vn_address_json_url()
	{
		return site_asset_url('data/vn-hanh-chinh.json');
	}
}
