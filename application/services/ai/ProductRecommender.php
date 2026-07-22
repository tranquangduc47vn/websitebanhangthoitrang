<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProductRecommender {

	public function recommend(array $filters, $limit = 5)
	{
		$CI = get_instance();
		$db = $CI->db;

		// Resolve catalog IDs first — avoid CI query builder state leaking between queries.
		$catalogIds = $this->resolveCatalogIds($db, $filters);

		$db->select('id, catalog_id, name, price, discount, image_link, buyed, quantity, created, color')
			->from('product');

		if (!empty($catalogIds)) {
			$db->where_in('catalog_id', $catalogIds);
		}

		if (!empty($filters['max_price'])) {
			$db->where('(price - discount) <=', (float) $filters['max_price'], false);
		}

		if (!empty($filters['color'])) {
			$safeColor = $db->escape_like_str(mb_strtolower((string) $filters['color'], 'UTF-8'));
			$db->where("REPLACE(LOWER(color), ' ', '') LIKE '%" . $safeColor . "%'", null, false);
		}

		if (!empty($filters['name_keyword'])) {
			$db->like('name', (string) $filters['name_keyword']);
		}

		$sort = isset($filters['sort']) ? $filters['sort'] : '';
		if ($sort === 'discount') {
			$db->where('discount >', 0);
		}

		switch ($sort) {
			case 'bestseller':
				$db->order_by('buyed', 'DESC');
				break;
			case 'newest':
				$db->order_by('id', 'DESC');
				break;
			case 'discount':
				$db->order_by('discount', 'DESC');
				break;
			default:
				$db->order_by('buyed', 'DESC');
		}

		$db->limit(max(1, min(5, (int) $limit)));
		$rows = $db->get()->result();

		$out = array();
		foreach ($rows as $row) {
			$finalPrice = max(0, (float) $row->price - (float) $row->discount);
			$out[] = array(
				'id' => (int) $row->id,
				'name' => $row->name,
				'price' => $finalPrice,
				'price_fmt' => number_format($finalPrice, 0, ',', '.') . ' đ',
				'url' => build_product_url($row),
				'image' => base_url('upload/product/' . $row->image_link),
				'color' => trim((string) $row->color),
			);
		}
		return $out;
	}

	protected function resolveCatalogIds($db, array $filters)
	{
		$ids = array();

		if (!empty($filters['gender'])) {
			$genderName = $filters['gender'] === 'nam' ? 'nam' : 'nữ';
			$roots = $db->select('id')->from('catalog')->where('parent_id', 1)->like('name', $genderName)->get()->result();
			foreach ($roots as $r) {
				$ids = array_merge($ids, $this->collectSubtree($db, $r->id));
			}
		}

		if (!empty($filters['category_keyword'])) {
			$cats = $db->select('id')->from('catalog')->like('name', $filters['category_keyword'])->get()->result();
			$catIds = array();
			foreach ($cats as $c) {
				$catIds = array_merge($catIds, $this->collectSubtree($db, $c->id));
			}
			if (!empty($ids) && !empty($catIds)) {
				$intersect = array_values(array_intersect($ids, $catIds));
				$ids = !empty($intersect) ? $intersect : $catIds;
			} elseif (!empty($catIds)) {
				$ids = $catIds;
			}
		}

		return array_values(array_unique($ids));
	}

	protected function collectSubtree($db, $rootId)
	{
		$ids = array((int) $rootId);
		$level1 = $db->select('id')->from('catalog')->where('parent_id', $rootId)->get()->result();
		foreach ($level1 as $l1) {
			$ids[] = (int) $l1->id;
			$level2 = $db->select('id')->from('catalog')->where('parent_id', $l1->id)->get()->result();
			foreach ($level2 as $l2) {
				$ids[] = (int) $l2->id;
			}
		}
		return $ids;
	}
}
