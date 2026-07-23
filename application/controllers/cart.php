<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cart extends MY_Frontend_Controller {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('product_model');
        $this->load->model('product_variant_model');
        $this->load->library('product_service');
    }

    private function _check_login()
    {
        if (!$this->session->userdata('user')) {
            $this->session->set_userdata('redirect_back', current_url());
            $this->session->set_flashdata('message', 'Bạn cần đăng nhập tài khoản để thực hiện chức năng này!');
            redirect(base_url('user/login'));
        }
    }

    private function _parse_product_variants($product)
    {
        $colors = array();
        $sizes = array();

        if (!empty($product->color)) {
            foreach (explode(',', $product->color) as $color) {
                $color = trim($color);
                if ($color !== '') {
                    $colors[] = $color;
                }
            }
        }
        if (empty($colors)) {
            $colors[] = 'Mặc định';
        }

        if (!empty($product->size)) {
            foreach (explode(',', $product->size) as $size) {
                $size = trim($size);
                if ($size !== '') {
                    $sizes[] = $size;
                }
            }
        }
        if (empty($sizes)) {
            $sizes[] = 'Freesize';
        }

        return array(
            'colors' => $colors,
            'sizes' => $sizes,
        );
    }

    private function _default_variant($product)
    {
        $variants = $this->_parse_product_variants($product);
        return array(
            'size' => $variants['sizes'][0],
            'color' => $variants['colors'][0],
        );
    }

    private function _normalize_variant($product, $size, $color)
    {
        $variants = $this->_parse_product_variants($product);
        $size = trim((string) $size);
        $color = trim((string) $color);

        if ($size === '' || !in_array($size, $variants['sizes'], true)) {
            $size = $variants['sizes'][0];
        }
        if ($color === '' || !in_array($color, $variants['colors'], true)) {
            $color = $variants['colors'][0];
        }

        return array(
            'size' => $size,
            'color' => $color,
            'variants' => $variants,
        );
    }

    private function _build_rowid($id, $size, $color)
    {
        return $id . '_' . md5($size . '_' . $color);
    }

    private function _enrich_cart_items($carts)
    {
        $items = array();
        foreach ($carts as $rowid => $item) {
            $product = $this->product_model->get_info($item['id']);
            if ($product) {
                $item['variants'] = $this->_parse_product_variants($product);
                $items[$rowid] = $item;
            }
        }
        return $items;
    }

    private function _sanitize_cart_session()
    {
        $carts = $this->session->userdata('custom_cart');
        if (!is_array($carts) || empty($carts)) {
            return;
        }

        $items = $this->_enrich_cart_items($carts);
        if (count($items) === count($carts)) {
            return;
        }

        $removed = count($carts) - count($items);
        $this->session->set_userdata('custom_cart', $items);
        $this->session->set_flashdata(
            'message',
            $removed . ' sản phẩm trong giỏ không còn bán và đã được xóa tự động.'
        );
    }

    private function _wants_json()
    {
        return $this->input->is_ajax_request();
    }

    private function _json_response($payload, $status = 200)
    {
        $this->output
            ->set_status_header((int) $status)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($payload, JSON_UNESCAPED_UNICODE));
    }

    private function _cart_totals($carts)
    {
        $total_items = 0;
        $total_price = 0;
        foreach ((array) $carts as $item) {
            $total_items += (int) $item['qty'];
            $total_price += (float) $item['subtotal'];
        }
        return array(
            'total_items' => $total_items,
            'total_price' => $total_price,
        );
    }

    private function _cart_success_payload($carts, $message = '', $extra = array())
    {
        $totals = $this->_cart_totals($carts);
        return array_merge(array(
            'ok' => true,
            'message' => $message,
            'total_items' => $totals['total_items'],
            'total_price' => $totals['total_price'],
        ), $extra);
    }

    private function _cart_stock_notice_payload($extra)
    {
        if (!isset($extra['stock'])) {
            return null;
        }

        return array(
            'product_name' => isset($extra['product_name']) ? (string) $extra['product_name'] : '',
            'size' => isset($extra['size']) ? (string) $extra['size'] : '',
            'color' => isset($extra['color']) ? (string) $extra['color'] : '',
            'stock' => (int) $extra['stock'],
        );
    }

    private function _cart_fail($message, $extra = array())
    {
        if ($this->_wants_json()) {
            $carts = $this->session->userdata('custom_cart');
            if (!is_array($carts)) {
                $carts = array();
            }
            $this->_json_response(array_merge(array(
                'ok' => false,
                'message' => $message,
            ), $this->_cart_totals($carts), $extra), 200);
            return true;
        }

        $stock_notice = $this->_cart_stock_notice_payload($extra);
        if ($stock_notice !== null) {
            $this->session->set_flashdata('stock_notice', $stock_notice);
        } else {
            $this->session->set_flashdata('message', $message);
        }
        redirect(build_cart_url());
        return true;
    }

    private function _cart_finish($payload)
    {
        if ($this->_wants_json()) {
            $this->_json_response($payload);
            return;
        }
        if (!empty($payload['message'])) {
            $this->session->set_flashdata('message', $payload['message']);
        }
        redirect(build_cart_url());
    }

    private function _require_login_for_cart()
    {
        if ($this->session->userdata('user')) {
            return true;
        }
        if ($this->_wants_json()) {
            $this->_json_response(array(
                'ok' => false,
                'message' => 'Bạn cần đăng nhập tài khoản để thực hiện chức năng này!',
                'login_required' => true,
            ), 401);
            return false;
        }
        $this->session->set_userdata('redirect_back', current_url());
        $this->session->set_flashdata('message', 'Bạn cần đăng nhập tài khoản để thực hiện chức năng này!');
        redirect(base_url('user/login'));
        return false;
    }

    public function index()
    {
        $this->_check_login();

        $this->data['message'] = $this->session->flashdata('message');
        $this->data['stock_notice'] = $this->session->flashdata('stock_notice');
        
        $carts = $this->session->userdata('custom_cart');
        if (!is_array($carts)) { 
            $carts = array(); 
        }

        $this->_sanitize_cart_session();
        $carts = $this->session->userdata('custom_cart');
        if (!is_array($carts)) {
            $carts = array();
        }
        
        $this->data['carts'] = $this->_enrich_cart_items($carts);

        $total_items = 0;
        foreach ($carts as $item) {
            $total_items += $item['qty'];
        }
        $this->data['total_items'] = $total_items;
        
        $this->render_frontend_sub('site/cart/index');
    }

    public function add()
    {
        $this->_check_login();

        $id = $this->input->post('id');
        $qty = $this->input->post('qty');
        $size = $this->input->post('size');
        $color = $this->input->post('color');

        if (empty($id)) {
            $id = $this->uri->rsegment(3);
        }

        $id = intval($id);
        $qty = intval($qty);
        if ($qty <= 0) { $qty = 1; }

        $product = $this->product_model->get_info($id);
        if (empty($product)) {
            $this->session->set_flashdata('message', 'Sản phẩm không tồn tại trong hệ thống!');
            redirect(base_url());
        }

        $variant = $this->_normalize_variant($product, $size, $color);
        $size = $variant['size'];
        $color = $variant['color'];

        $current_cart = $this->session->userdata('custom_cart');
        if (!is_array($current_cart)) {
            $current_cart = array();
        }

        $rowid = $this->_build_rowid($id, $size, $color);
        $existing_qty = isset($current_cart[$rowid]) ? (int) $current_cart[$rowid]['qty'] : 0;

        $check = $this->product_service->validate_cart_quantity($id, $color, $size, $qty, $existing_qty, false);
        if (!$check['ok']) {
            $this->session->set_flashdata('stock_notice', array(
                'product_name' => (string) $product->name,
                'size' => $check['size'],
                'color' => $check['color'],
                'stock' => isset($check['stock']) ? (int) $check['stock'] : 0,
            ));
            redirect(build_cart_url());
        }

        $variant_id = (int) $check['variant_id'];
        $size = $check['size'];
        $color = $check['color'];

        $price = floatval($product->price);
        if ($product->discount > 0) {
            $price = floatval($product->price - $product->discount);
        }
        if ($price <= 0) { $price = 1000; } 

        $rowid = $this->_build_rowid($id, $size, $color);

        $item = array(
            'rowid'    => $rowid,
            'id'       => $id,
            'variant_id' => $variant_id,
            'qty'      => $qty,
            'price'    => $price,
            'name'     => $product->name,
            'subtotal' => $price * $qty,
            'options'  => array(
                'image_link' => !empty($product->image_link) ? $product->image_link : 'no-image.png',
                'size'       => $size,
                'color'      => $color
            )
        );

        if (isset($current_cart[$rowid])) {
            $current_cart[$rowid]['qty'] += $qty;
            $current_cart[$rowid]['variant_id'] = $variant_id;
            $current_cart[$rowid]['subtotal'] = $current_cart[$rowid]['qty'] * $price;
            $current_cart[$rowid]['options']['size'] = $size;
            $current_cart[$rowid]['options']['color'] = $color;
        } else {
            $current_cart[$rowid] = $item;
        }

        $this->session->set_userdata('custom_cart', $current_cart);
        $this->session->set_flashdata('message', 'Thêm vào giỏ hàng thành công');
        
        redirect(build_cart_url());
    }

    public function update_options()
    {
        if (!$this->_require_login_for_cart()) {
            return;
        }

        $rowid = trim((string) $this->input->post('rowid'));
        $size = $this->input->post('size');
        $color = $this->input->post('color');

        if ($rowid === '') {
            $this->_cart_fail('Không tìm thấy sản phẩm trong giỏ hàng');
            return;
        }

        $current_cart = $this->session->userdata('custom_cart');
        if (!is_array($current_cart) || !isset($current_cart[$rowid])) {
            $this->_cart_fail('Không tìm thấy sản phẩm trong giỏ hàng');
            return;
        }

        $item = $current_cart[$rowid];
        $product = $this->product_model->get_info($item['id']);
        if (empty($product)) {
            $this->_cart_fail('Sản phẩm không còn tồn tại');
            return;
        }

        $variant = $this->_normalize_variant($product, $size, $color);
        $size = $variant['size'];
        $color = $variant['color'];
        $new_rowid = $this->_build_rowid($item['id'], $size, $color);

        if ($new_rowid === $rowid) {
            $this->_cart_finish($this->_cart_success_payload($current_cart, '', array(
                'action' => 'options',
                'old_rowid' => $rowid,
                'rowid' => $rowid,
                'qty' => (int) $item['qty'],
                'subtotal' => (float) $item['subtotal'],
                'size' => $size,
                'color' => $color,
                'product_name' => (string) $product->name,
            )));
            return;
        }

        $moving_qty = (int) $item['qty'];
        $existing_target_qty = isset($current_cart[$new_rowid]) ? (int) $current_cart[$new_rowid]['qty'] : 0;
        $check = $this->product_service->validate_cart_quantity($item['id'], $color, $size, $moving_qty, $existing_target_qty, false);
        if (!$check['ok']) {
            $this->_cart_fail($check['message'], array(
                'action' => 'options',
                'rowid' => $rowid,
                'product_name' => (string) $product->name,
                'size' => $check['size'],
                'color' => $check['color'],
                'stock' => isset($check['stock']) ? (int) $check['stock'] : 0,
            ));
            return;
        }

        if (isset($current_cart[$new_rowid])) {
            $merged_qty = $current_cart[$new_rowid]['qty'] + $moving_qty;
            $current_cart[$new_rowid]['qty'] = $merged_qty;
            $current_cart[$new_rowid]['variant_id'] = (int) $check['variant_id'];
            $current_cart[$new_rowid]['subtotal'] = $merged_qty * $current_cart[$new_rowid]['price'];
            $current_cart[$new_rowid]['options']['size'] = $check['size'];
            $current_cart[$new_rowid]['options']['color'] = $check['color'];
            unset($current_cart[$rowid]);
            $subtotal = (float) $current_cart[$new_rowid]['subtotal'];
            $qty = (int) $merged_qty;
        } else {
            unset($current_cart[$rowid]);
            $item['rowid'] = $new_rowid;
            $item['variant_id'] = (int) $check['variant_id'];
            $item['options']['size'] = $check['size'];
            $item['options']['color'] = $check['color'];
            $current_cart[$new_rowid] = $item;
            $subtotal = (float) $item['subtotal'];
            $qty = (int) $item['qty'];
        }

        $this->session->set_userdata('custom_cart', $current_cart);
        $this->_cart_finish($this->_cart_success_payload($current_cart, 'Đã cập nhật size và màu sắc', array(
            'action' => 'options',
            'old_rowid' => $rowid,
            'rowid' => $new_rowid,
            'qty' => $qty,
            'subtotal' => $subtotal,
            'size' => $check['size'],
            'color' => $check['color'],
            'product_name' => (string) $product->name,
        )));
    }

    public function update()
    {
        if (!$this->_require_login_for_cart()) {
            return;
        }

        $rowid = $this->uri->segment(3);
        $action = $this->uri->segment(4);
        
        $current_cart = $this->session->userdata('custom_cart');
        if (!is_array($current_cart)) { 
            $current_cart = array(); 
        }

        if (!isset($current_cart[$rowid])) {
            $this->_cart_fail('Không tìm thấy sản phẩm trong giỏ hàng');
            return;
        }

        $item = $current_cart[$rowid];
        $product = $this->product_model->get_info($item['id']);
        $product_name = $product ? (string) $product->name : (string) $item['name'];
        $removed = false;

        if ($action == 'sum') {
            $color = isset($item['options']['color']) ? $item['options']['color'] : '';
            $size = isset($item['options']['size']) ? $item['options']['size'] : '';
            $check = $this->product_service->validate_cart_quantity(
                $item['id'],
                $color,
                $size,
                1,
                (int) $item['qty'],
                false
            );
            if (!$check['ok']) {
                $this->_cart_fail($check['message'], array(
                    'action' => 'qty',
                    'rowid' => $rowid,
                    'product_name' => $product_name,
                    'size' => $check['size'],
                    'color' => $check['color'],
                    'stock' => isset($check['stock']) ? (int) $check['stock'] : 0,
                ));
                return;
            }

            $current_cart[$rowid]['qty'] += 1;
            $current_cart[$rowid]['variant_id'] = (int) $check['variant_id'];
            $current_cart[$rowid]['subtotal'] = $current_cart[$rowid]['qty'] * $current_cart[$rowid]['price'];
        } elseif ($action == 'sub') {
            if ($current_cart[$rowid]['qty'] > 1) {
                $current_cart[$rowid]['qty'] -= 1;
                $current_cart[$rowid]['subtotal'] = $current_cart[$rowid]['qty'] * $current_cart[$rowid]['price'];
            } else {
                unset($current_cart[$rowid]);
                $removed = true;
            }
        }

        $this->session->set_userdata('custom_cart', $current_cart);

        if ($removed) {
            $this->_cart_finish($this->_cart_success_payload($current_cart, '', array(
                'action' => 'qty',
                'rowid' => $rowid,
                'removed' => true,
            )));
            return;
        }

        $item = $current_cart[$rowid];
        $this->_cart_finish($this->_cart_success_payload($current_cart, '', array(
            'action' => 'qty',
            'rowid' => $rowid,
            'removed' => false,
            'qty' => (int) $item['qty'],
            'subtotal' => (float) $item['subtotal'],
            'product_name' => $product_name,
            'size' => isset($item['options']['size']) ? $item['options']['size'] : '',
            'color' => isset($item['options']['color']) ? $item['options']['color'] : '',
        )));
    }

    public function del()
    {
        if (!$this->_require_login_for_cart()) {
            return;
        }

        $rowid = $this->uri->segment(3);
        $current_cart = $this->session->userdata('custom_cart');
        if (!is_array($current_cart)) { 
            $current_cart = array(); 
        }

        $message = '';
        if (!empty($rowid) && $rowid !== '0' && $rowid !== 'del') {
            if (isset($current_cart[$rowid])) {
                unset($current_cart[$rowid]);
                $this->session->set_userdata('custom_cart', $current_cart);
                $message = 'Xóa sản phẩm thành công';
            }
        } else {
            $this->session->unset_userdata('custom_cart');
            $current_cart = array();
            $message = 'Xóa giỏ hàng thành công';
        }

        $this->_cart_finish($this->_cart_success_payload($current_cart, $message, array(
            'action' => 'delete',
            'rowid' => $rowid,
            'removed' => true,
            'empty' => empty($current_cart),
        )));
    }
}
