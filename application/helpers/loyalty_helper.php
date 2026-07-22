<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Tích điểm & hạng thành viên — quy tắc cố định.

if (!function_exists('loyalty_tier_order')) {
	function loyalty_tier_order()
	{
		return array('member' => 0, 'silver' => 1, 'gold' => 2, 'vip' => 3);
	}
}

if (!function_exists('loyalty_tier_label')) {
	function loyalty_tier_label($tier)
	{
		$labels = array(
			'member' => 'Thành viên',
			'silver' => 'Bạc',
			'gold' => 'Vàng',
			'vip' => 'VIP',
		);
		$tier = strtolower((string) $tier);
		return isset($labels[$tier]) ? $labels[$tier] : 'Thành viên';
	}
}

if (!function_exists('loyalty_compute_tier')) {
	function loyalty_compute_tier($completed_orders, $lifetime_spend)
	{
		$completed_orders = (int) $completed_orders;
		$lifetime_spend = (int) $lifetime_spend;
		if ($completed_orders >= 25 || $lifetime_spend >= 50000000) {
			return 'vip';
		}
		if ($completed_orders >= 10 || $lifetime_spend >= 20000000) {
			return 'gold';
		}
		if ($completed_orders >= 3 || $lifetime_spend >= 5000000) {
			return 'silver';
		}
		return 'member';
	}
}

if (!function_exists('loyalty_tier_meets')) {
	function loyalty_tier_meets($user_tier, $required_tier)
	{
		$order = loyalty_tier_order();
		$user_tier = isset($order[$user_tier]) ? $user_tier : 'member';
		$required_tier = isset($order[$required_tier]) ? $required_tier : 'member';
		return $order[$user_tier] >= $order[$required_tier];
	}
}

if (!function_exists('loyalty_points_per_vnd')) {
	/** 1 điểm / 10.000đ thanh toán (sau giảm giá) */
	function loyalty_points_per_vnd()
	{
		return 10000;
	}
}

if (!function_exists('loyalty_tier_point_multiplier')) {
	function loyalty_tier_point_multiplier($tier)
	{
		switch (strtolower((string) $tier)) {
			case 'silver':
				return 1.1;
			case 'gold':
				return 1.25;
			case 'vip':
				return 1.5;
			default:
				return 1.0;
		}
	}
}

if (!function_exists('loyalty_calc_points_for_amount')) {
	function loyalty_calc_points_for_amount($final_amount, $tier)
	{
		$final_amount = (int) $final_amount;
		if ($final_amount <= 0) {
			return 0;
		}
		$base = (int) floor($final_amount / loyalty_points_per_vnd());
		$mult = loyalty_tier_point_multiplier($tier);
		return (int) max(0, floor($base * $mult));
	}
}

if (!function_exists('loyalty_normalize_voucher_code')) {
	function loyalty_normalize_voucher_code($code)
	{
		$code = strtoupper(trim((string) $code));
		$code = preg_replace('/\s+/', '', $code);
		return $code;
	}
}

if (!function_exists('loyalty_calc_voucher_discount')) {
	function loyalty_calc_voucher_discount($voucher, $cart_total)
	{
		$cart_total = (int) $cart_total;
		if ($cart_total <= 0 || empty($voucher)) {
			return 0;
		}
		$type = isset($voucher->discount_type) ? $voucher->discount_type : 'fixed';
		$value = (int) $voucher->discount_value;
		if ($type === 'percent') {
			$discount = (int) round($cart_total * $value / 100);
			$cap = (int) $voucher->max_discount;
			if ($cap > 0 && $discount > $cap) {
				$discount = $cap;
			}
		} else {
			$discount = $value;
		}
		if ($discount > $cart_total) {
			$discount = $cart_total;
		}
		return max(0, $discount);
	}
}
