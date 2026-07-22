<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('seo_slugify')) {
	function seo_slugify($str)
	{
		if ($str === null || $str === '') {
			return '';
		}
		if (!function_exists('covert_vi_to_en')) {
			$CI =& get_instance();
			$CI->load->helper('common');
		}
		$str = covert_vi_to_en($str);
		$str = strtolower($str);
		$str = preg_replace('/[^a-z0-9-]+/', '-', $str);
		$str = preg_replace('/-+/', '-', $str);
		return trim($str, '-');
	}
}

if (!function_exists('seo_price_slug_map')) {
	function seo_price_slug_map()
	{
		return array(
			'duoi-200k'  => '0-200000',
			'200k-500k'  => '200000-500000',
			'500k-1tr'   => '500000-1000000',
			'tren-1tr'   => '1000000-999999999',
		);
	}
}

if (!function_exists('seo_price_range_to_slug')) {
	function seo_price_range_to_slug($price_range)
	{
		if ($price_range === null || $price_range === '') {
			return '';
		}
		$map = seo_price_slug_map();
		$flip = array_flip($map);
		return isset($flip[$price_range]) ? $flip[$price_range] : '';
	}
}

if (!function_exists('seo_price_slug_to_range')) {
	function seo_price_slug_to_range($slug)
	{
		$map = seo_price_slug_map();
		return isset($map[$slug]) ? $map[$slug] : '';
	}
}

if (!function_exists('seo_catalog_slug')) {
	function seo_catalog_slug($catalog)
	{
		if (is_object($catalog) && isset($catalog->name)) {
			return seo_slugify($catalog->name);
		}
		if (is_array($catalog) && isset($catalog['name'])) {
			return seo_slugify($catalog['name']);
		}
		return '';
	}
}

if (!function_exists('seo_resolve_catalog_id_by_slug')) {
	function seo_resolve_catalog_id_by_slug($slug)
	{
		static $cache = null;
		$slug = seo_slugify($slug);
		if ($slug === '') {
			return 0;
		}
		if ($cache === null) {
			$cache = array();
			$CI =& get_instance();
			$CI->load->model('catalog_model');
			$all = $CI->catalog_model->get_list();
			foreach ($all as $row) {
				$key = seo_catalog_slug($row);
				if ($key !== '' && !isset($cache[$key])) {
					$cache[$key] = (int) $row->id;
				}
			}
		}
		return isset($cache[$slug]) ? (int) $cache[$slug] : 0;
	}
}

if (!function_exists('build_category_url')) {
	function build_category_url($catalog, $price_range = null, $page = null)
	{
		$CI =& get_instance();
		if (is_numeric($catalog)) {
			$CI->load->model('catalog_model');
			$catalog = $CI->catalog_model->get_info((int) $catalog);
		}
		if (empty($catalog) || !is_object($catalog)) {
			return site_url();
		}
		$parts = array(seo_catalog_slug($catalog));
		if ($parts[0] === '') {
			return site_url('product/catalog/' . (int) $catalog->id);
		}
		if ($price_range !== null && $price_range !== '') {
			$ps = seo_price_range_to_slug($price_range);
			if ($ps === '' && isset(seo_price_slug_map()[$price_range])) {
				$ps = $price_range;
			}
			if ($ps !== '') {
				$parts[] = $ps;
			}
		}
		if ($page !== null && (int) $page > 1) {
			$parts[] = (int) $page;
		}
		return site_url(implode('/', $parts));
	}
}

if (!function_exists('build_product_url')) {
	function build_product_url($product)
	{
		$CI =& get_instance();
		if (is_numeric($product)) {
			$CI->load->model('product_model');
			$product = $CI->product_model->get_info((int) $product);
		}
		if (empty($product) || !is_object($product)) {
			return site_url();
		}
		$slug = seo_slugify($product->name);
		if ($slug === '') {
			$slug = 'san-pham';
		}
		return site_url($slug . '-p' . (int) $product->id);
	}
}

if (!function_exists('build_price_filter_url')) {
	function build_price_filter_url($catalog, $price_range)
	{
		return build_category_url($catalog, $price_range);
	}
}

if (!function_exists('seo_search_keyword_from_slug')) {
	function seo_search_keyword_from_slug($slug)
	{
		$slug = rawurldecode((string) $slug);
		$slug = str_replace('-', ' ', $slug);
		return trim($slug);
	}
}

if (!function_exists('build_search_url')) {
	function build_search_url($keyword, $page = null)
	{
		$keyword = trim((string) $keyword);
		if ($keyword === '') {
			return site_url('tim-kiem');
		}
		$slug = seo_slugify($keyword);
		if ($slug === '') {
			$slug = 'tim-kiem';
		}
		$path = 'tim-kiem/' . $slug;
		if ($page !== null && (int) $page > 1) {
			$path .= '/' . (int) $page;
		}
		return site_url($path);
	}
}

if (!function_exists('build_cart_url')) {
	function build_cart_url()
	{
		return site_url('gio-hang');
	}
}

if (!function_exists('build_checkout_url')) {
	function build_checkout_url()
	{
		return site_url('thanh-toan');
	}
}

if (!function_exists('build_checkout_qr_url')) {
	function build_checkout_qr_url($transaction_id)
	{
		return site_url('thanh-toan/checkout_qr/' . (int) $transaction_id);
	}
}

if (!function_exists('build_login_url')) {
	function build_login_url()
	{
		return site_url('dang-nhap');
	}
}

if (!function_exists('build_register_url')) {
	function build_register_url()
	{
		return site_url('dang-ky');
	}
}

if (!function_exists('build_news_index_url')) {
	function build_news_index_url($page = null)
	{
		if ($page !== null && (int) $page > 1) {
			return site_url('tin-tuc/' . (int) $page);
		}
		return site_url('tin-tuc');
	}
}

if (!function_exists('build_news_post_url')) {
	function build_news_post_url($post)
	{
		$CI =& get_instance();
		if (is_numeric($post)) {
			$CI->load->model('news_model');
			$post = $CI->news_model->get_info((int) $post);
		}
		if (empty($post) || !is_object($post)) {
			return site_url('tin-tuc');
		}
		$title = isset($post->title) ? $post->title : (isset($post->name) ? $post->name : '');
		$slug = seo_slugify($title);
		if ($slug === '') {
			$slug = 'bai-viet';
		}
		return site_url('tin-tuc/' . $slug . '-n' . (int) $post->id);
	}
}

if (!function_exists('seo_current_url')) {
	function seo_current_url()
	{
		$CI =& get_instance();
		$qs = $_SERVER['QUERY_STRING'] ?? '';
		$path = $CI->uri->uri_string();
		$url = site_url($path);
		if ($qs !== '') {
			$url .= '?' . $qs;
		}
		return $url;
	}
}

if (!function_exists('seo_redirect')) {
	function seo_redirect($url, $code = 301)
	{
		redirect($url, 'location', (int) $code);
		exit;
	}
}
