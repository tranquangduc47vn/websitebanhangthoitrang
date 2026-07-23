<?php
$product = isset($product) ? $product : null;
$variants = isset($variants) ? $variants : array();
if (!$product) {
	echo '<p class="text-danger">Không có dữ liệu sản phẩm.</p>';
	return;
}
$this->load->view('admin/product/edit', array(
	'product' => $product,
	'variants' => $variants,
	'catalog' => isset($catalog) ? $catalog : array(),
));
