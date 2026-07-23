<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Master data sản phẩm + sinh biến thể (màu × size).
 */
class Product_service {

	protected $CI;

	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->model('product_model');
		$this->CI->load->model('product_variant_model');
		$this->CI->config->load('stock');
	}

	public function normalize_list($values)
	{
		$out = array();
		if (!is_array($values)) {
			return $out;
		}
		foreach ($values as $v) {
			$v = trim((string) $v);
			if ($v !== '' && !in_array($v, $out, true)) {
				$out[] = $v;
			}
		}
		return $out;
	}

	public function parse_product_attributes($product)
	{
		$colors = array('');
		$sizes = array('');

		if ($product && !empty($product->color)) {
			$colors = $this->normalize_list(explode(',', (string) $product->color));
			if (empty($colors)) {
				$colors = array('');
			}
		}
		if ($product && !empty($product->size)) {
			$sizes = $this->normalize_list(explode(',', (string) $product->size));
			if (empty($sizes)) {
				$sizes = array('');
			}
		}

		return array('colors' => $colors, 'sizes' => $sizes);
	}

	public function generate_sku($product_id, $color, $size)
	{
		$product_id = (int) $product_id;
		$slug = function ($text) {
			$text = trim((string) $text);
			if ($text === '') {
				return 'DF';
			}
			$text = preg_replace('/[^A-Za-z0-9]+/', '-', $text);
			$text = strtoupper(trim($text, '-'));
			return $text !== '' ? substr($text, 0, 12) : 'DF';
		};

		$base = 'SP' . str_pad((string) $product_id, 5, '0', STR_PAD_LEFT)
			. '-' . $slug($color) . '-' . $slug($size);

		$sku = $base;
		$n = 1;
		while ($this->CI->product_variant_model->sku_exists($sku)) {
			$n++;
			$sku = $base . '-' . $n;
		}
		return $sku;
	}

	public function sync_variants($product_id, array $colors, array $sizes, $base_price = null, $cost_price = 0)
	{
		$product_id = (int) $product_id;
		$product = $this->CI->product_model->get_info($product_id);
		if (!$product) {
			return array('ok' => false, 'message' => 'Sản phẩm không tồn tại.');
		}

		$colors = $this->normalize_list($colors);
		$sizes = $this->normalize_list($sizes);
		if (empty($colors)) {
			$colors = array('');
		}
		if (empty($sizes)) {
			$sizes = array('');
		}

		if ($base_price === null) {
			$base_price = (float) $product->price;
		}

		$min_stock = (int) $this->CI->config->item('default_min_stock');
		$now = time();
		$created = 0;

		foreach ($colors as $color) {
			foreach ($sizes as $size) {
				$existing = $this->CI->product_variant_model->find_variant($product_id, $color, $size);
				if ($existing) {
					continue;
				}
				$this->CI->product_variant_model->create(array(
					'product_id' => $product_id,
					'sku' => $this->generate_sku($product_id, $color, $size),
					'color' => $color,
					'size' => $size,
					'price' => (float) $base_price,
					'cost_price' => max(0, (float) $cost_price),
					'stock' => 0,
					'min_stock' => $min_stock,
					'image' => '',
					'status' => 1,
					'created' => $now,
					'updated' => $now,
				));
				$created++;
			}
		}

		$this->CI->product_model->update($product_id, array(
			'color' => implode(',', array_filter($colors, function ($c) { return $c !== ''; })) ?: null,
			'size' => implode(',', array_filter($sizes, function ($s) { return $s !== ''; })) ?: null,
		));

		$this->sync_product_quantity_cache($product_id);

		return array('ok' => true, 'created' => $created);
	}

	public function sync_product_quantity_cache($product_id)
	{
		$total = $this->CI->product_variant_model->sum_stock_by_product((int) $product_id);
		$this->CI->product_model->update((int) $product_id, array('quantity' => $total));
		return $total;
	}

	public function resolve_variant_id($product_id, $color, $size, $auto_create = false)
	{
		$row = $this->resolve_variant_row($product_id, $color, $size, $auto_create);
		return $row ? (int) $row['variant_id'] : 0;
	}

	public function resolve_variant_row($product_id, $color, $size, $auto_create = false)
	{
		$product_id = (int) $product_id;
		$product = $this->CI->product_model->get_info($product_id);
		if (!$product) {
			return null;
		}

		$attrs = $this->parse_product_attributes($product);
		$color = trim((string) $color);
		$size = trim((string) $size);
		if ($color === '' || !in_array($color, $attrs['colors'], true)) {
			$color = $attrs['colors'][0];
		}
		if ($size === '' || !in_array($size, $attrs['sizes'], true)) {
			$size = $attrs['sizes'][0];
		}

		$row = $this->CI->product_variant_model->find_variant($product_id, $color, $size);
		if (!$row && $auto_create) {
			$this->sync_variants($product_id, array($color), array($size), (float) $product->price);
			$row = $this->CI->product_variant_model->find_variant($product_id, $color, $size);
		}
		if (!$row || (int) $row->status !== 1) {
			return null;
		}

		return array(
			'variant_id' => (int) $row->id,
			'stock' => (int) $row->stock,
			'color' => (string) $row->color,
			'size' => (string) $row->size,
			'sku' => (string) $row->sku,
		);
	}

	public function variant_stock_message($product_name, $size, $color, $stock)
	{
		$product_name = trim((string) $product_name);
		$size = trim((string) $size);
		$color = trim((string) $color);
		return $product_name . ' size ' . $size . ' màu ' . $color . ' chỉ còn ' . (int) $stock . ' sản phẩm trong kho.';
	}

	public function validate_cart_quantity($product_id, $color, $size, $qty, $existing_in_cart = 0, $auto_create = false)
	{
		$qty = max(0, (int) $qty);
		$existing_in_cart = max(0, (int) $existing_in_cart);
		$product = $this->CI->product_model->get_info((int) $product_id);
		$product_name = $product ? (string) $product->name : 'Sản phẩm';
		$variant = $this->resolve_variant_row($product_id, $color, $size, $auto_create);
		if (!$variant) {
			return array(
				'ok' => false,
				'message' => 'Biến thể sản phẩm không tồn tại hoặc đã ngừng kinh doanh.',
				'variant_id' => 0,
				'stock' => 0,
				'color' => trim((string) $color),
				'size' => trim((string) $size),
			);
		}

		$total_requested = $existing_in_cart + $qty;
		if ($total_requested > $variant['stock']) {
			return array(
				'ok' => false,
				'message' => $this->variant_stock_message($product_name, $variant['size'], $variant['color'], $variant['stock']),
				'variant_id' => $variant['variant_id'],
				'stock' => $variant['stock'],
				'color' => $variant['color'],
				'size' => $variant['size'],
			);
		}

		return array(
			'ok' => true,
			'message' => '',
			'variant_id' => $variant['variant_id'],
			'stock' => $variant['stock'],
			'color' => $variant['color'],
			'size' => $variant['size'],
		);
	}

	public function get_variant_stock_map($product_id)
	{
		$product_id = (int) $product_id;
		if ($product_id <= 0) {
			return array();
		}

		$rows = $this->CI->product_variant_model->get_by_product($product_id);
		$map = array();
		foreach ($rows as $row) {
			if ((int) $row->status !== 1) {
				continue;
			}
			$key = trim((string) $row->color) . '||' . trim((string) $row->size);
			$map[$key] = array(
				'id' => (int) $row->id,
				'stock' => (int) $row->stock,
				'color' => (string) $row->color,
				'size' => (string) $row->size,
			);
		}
		return $map;
	}
}
