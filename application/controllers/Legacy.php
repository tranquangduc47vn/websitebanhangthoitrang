<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Redirect URL storefront cũ sang SEO URL
class Legacy extends CI_Controller
{
	public function posts($path = '')
	{
		$segments = func_get_args();
		$path = implode('/', $segments);
		$target = 'tin-tuc' . ($path !== '' ? '/' . ltrim($path, '/') : '');
		seo_redirect(site_url($target));
	}

	public function posts_page($page = 0)
	{
		seo_redirect(build_news_index_url((int) $page > 0 ? (int) $page : null));
	}

	public function post_view($id = 0)
	{
		$this->load->model('news_model');
		$post = $this->news_model->get_info((int) $id);
		if ($post) {
			seo_redirect(build_news_post_url($post));
		}
		seo_redirect(build_news_index_url());
	}

	public function admin_posts($path = '')
	{
		$segments = func_get_args();
		$path = implode('/', $segments);
		$target = 'admin/posts' . ($path !== '' ? '/' . ltrim($path, '/') : '');
		seo_redirect(site_url($target));
	}

	public function catalog($id = 0, $offset = 0)
	{
		$id = (int) $id;
		$offset = (int) $offset;
		$price = $this->input->get('price_range');
		$url = build_category_url($id, $price ?: null);
		if ($offset > 0) {
			$url = rtrim($url, '/') . '/' . $offset;
		}
		$qs = $_SERVER['QUERY_STRING'] ?? '';
		if ($qs !== '') {
			$keep = array();
			parse_str($qs, $parsed);
			unset($parsed['price_range']);
			if (!empty($parsed)) {
				$url .= '?' . http_build_query($parsed);
			}
		}
		seo_redirect($url);
	}

	public function catalog_c($id = 0, $offset = 0)
	{
		$this->catalog($id, $offset);
	}

	public function product($id = 0)
	{
		seo_redirect(build_product_url((int) $id));
	}

	public function product_search()
	{
		$keyword = $this->input->get('key-search');
		if ($keyword === null || trim($keyword) === '') {
			seo_redirect(site_url('tim-kiem'));
		}
		$page = (int) $this->input->get('per_page');
		$url = build_search_url($keyword, $page > 0 ? $page : null);
		seo_redirect($url);
	}

	public function cart()
	{
		seo_redirect(build_cart_url());
	}

	public function cart_path($path = '')
	{
		$segments = func_get_args();
		$path = implode('/', $segments);
		seo_redirect(site_url('gio-hang' . ($path !== '' ? '/' . $path : '')));
	}

	public function order()
	{
		seo_redirect(build_checkout_url());
	}

	public function order_path($path = '')
	{
		$segments = func_get_args();
		$path = implode('/', $segments);
		seo_redirect(site_url('thanh-toan' . ($path !== '' ? '/' . $path : '')));
	}
}
