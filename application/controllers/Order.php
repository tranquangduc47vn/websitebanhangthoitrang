<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends MY_Frontend_Controller {
    public function __construct()
    {
        parent::__construct();
        
        $this->load->library('form_validation');
        $this->load->helper('form');
        $this->load->model('product_model');
        $this->load->library('product_service');
        $this->load->library('inventory_service');
    }

    public function index()
    {
        $carts = $this->session->userdata('custom_cart');
        if (!is_array($carts)) {
            $carts = array();
        }

        $sanitize = $this->_sanitize_cart($carts);
        $carts = $sanitize['cart'];
        if (!empty($sanitize['removed'])) {
            $this->session->set_userdata('custom_cart', $carts);
            $this->session->set_flashdata(
                'message',
                'Một số sản phẩm không còn bán (' . implode(', ', $sanitize['removed']) . ') và đã được xóa khỏi giỏ hàng.'
            );
            if (empty($carts)) {
                redirect(build_cart_url());
            }
        }

        $total_amount = 0;
        foreach ($carts as $value) {
            $total_amount = $total_amount + $value['subtotal'];
        }
        $this->data['total_amount'] = $total_amount;

        $user_id = 0;
        if ($this->session->userdata('user')) {
            $user = $this->session->userdata('user');
            $user_id = $user->id;
        }
        $this->_refresh_checkout_voucher($user_id, (int) $total_amount);

        if ($this->input->post()) {
            if (empty($carts)) {
                $this->session->set_flashdata('message', 'Giỏ hàng trống, không thể đặt hàng.');
                redirect(build_cart_url());
            }

            $this->form_validation->set_rules('name','Họ tên','required');
            $this->form_validation->set_rules('email', 'Email đăng nhập', 'required|valid_email');
            $this->form_validation->set_rules('address','Địa chỉ','required');
            $this->form_validation->set_rules('message','Ghi chú','trim');
            $this->form_validation->set_rules('phone','Điện thoại','required');
            
            if ($this->form_validation->run()) {
                $this->load->helper('loyalty');
                $this->load->model('voucher_model');

                $sanitize = $this->_sanitize_cart($carts);
                $carts = $sanitize['cart'];
                if (!empty($sanitize['removed'])) {
                    $this->session->set_userdata('custom_cart', $carts);
                    $this->session->set_flashdata(
                        'message',
                        'Không thể đặt hàng: sản phẩm "' . implode('", "', $sanitize['removed']) . '" không còn tồn tại. Vui lòng kiểm tra lại giỏ hàng.'
                    );
                    redirect(build_cart_url());
                }
                if (empty($carts)) {
                    $this->session->set_flashdata('message', 'Giỏ hàng trống, không thể đặt hàng.');
                    redirect(build_cart_url());
                }

                $total_amount = 0;
                foreach ($carts as $value) {
                    $total_amount = $total_amount + $value['subtotal'];
                }

                $payment_method = $this->input->post('payment');
                if (empty($payment_method)) {
                    $payment_method = 'COD';
                }

                $cart_subtotal = (int) $total_amount;
                $discount_amount = 0;
                $voucher_code = loyalty_normalize_voucher_code($this->input->post('voucher_code'));
                $applied_voucher = null;

                if ($voucher_code !== '') {
                    $check = $this->voucher_model->validate_for_checkout($voucher_code, $user_id, $cart_subtotal);
                    if (!$check['ok']) {
                        $this->session->unset_userdata('checkout_voucher');
                        $this->session->set_flashdata('message', $check['message']);
                        redirect(build_checkout_url());
                    }
                    $discount_amount = (int) $check['discount'];
                    $applied_voucher = $check['voucher'];
                }

                $final_amount = max(0, $cart_subtotal - $discount_amount);

                $this->load->model('transaction_model');
                $this->load->model('order_model');

                $this->db->trans_start();

                $data = array(
                    'user_id'      => $user_id,
                    'user_name'    => $this->input->post('name'),
                    'user_email'   => $this->input->post('email'),
                    'user_address' => $this->input->post('address'),
                    'user_phone'   => $this->input->post('phone'),
                    'message'      => trim((string) $this->input->post('message')),
                    'amount'       => $final_amount,
                    'voucher_code' => $voucher_code,
                    'discount_amount' => $discount_amount,
                    'payment'      => $payment_method,
                    'status'       => '0',
                    'created'      => time()
                );
                
                $this->transaction_model->create($data);
                $transaction_id = $this->db->insert_id();

                if ($applied_voucher) {
                    $this->voucher_model->record_use(
                        (int) $applied_voucher->id,
                        $user_id,
                        $transaction_id,
                        $discount_amount
                    );
                }

                foreach ($carts as $items) {
                    $product_info = $this->product_model->get_info($items['id']);
                    if (empty($product_info)) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata(
                            'message',
                            'Không thể đặt hàng vì sản phẩm không còn tồn tại. Vui lòng cập nhật giỏ hàng.'
                        );
                        redirect(build_cart_url());
                    }

                    $size = isset($items['options']['size']) ? $items['options']['size'] : '';
                    $color = isset($items['options']['color']) ? $items['options']['color'] : '';
                    $variant_id = !empty($items['variant_id']) ? (int) $items['variant_id'] : 0;
                    if ($variant_id <= 0) {
                        $variant_id = $this->product_service->resolve_variant_id($items['id'], $color, $size, true);
                    }

                    $deduct = $this->inventory_service->deduct_for_order(
                        $variant_id,
                        (int) $items['qty'],
                        (int) $transaction_id,
                        $user_id,
                        'Xuất kho đơn #' . (int) $transaction_id,
                        false
                    );
                    if (empty($deduct['ok'])) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('message', !empty($deduct['message']) ? $deduct['message'] : 'Không đủ tồn kho để đặt hàng.');
                        redirect(build_checkout_url());
                    }

                    $order_data = array(
                        'transaction_id' => $transaction_id,
                        'product_id'     => $items['id'],
                        'variant_id'     => $variant_id,
                        'size'           => $size !== '' ? $size : 'Mặc định',
                        'color'          => $color !== '' ? $color : 'Mặc định',
                        'qty'            => $items['qty'],
                        'amount'         => $items['subtotal']
                    );
                    if ($this->db->field_exists('cost_price', 'order') && $variant_id > 0 && $this->db->table_exists('product_variants')) {
                        $vrow = $this->db->select('cost_price')->where('id', $variant_id)->get('product_variants')->row();
                        $order_data['cost_price'] = $vrow ? max(0, (float) $vrow->cost_price) : 0;
                    }
                    $this->order_model->create($order_data);
                }

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('message', 'Đặt hàng thất bại. Vui lòng thử lại sau.');
                    redirect(build_checkout_url());
                }

                $this->_remember_order_access($transaction_id);
                $this->session->unset_userdata('checkout_voucher');

                if ($payment_method == 'Chuyển khoản') {
                    redirect(build_checkout_qr_url($transaction_id));
                } else {
                    $this->session->unset_userdata('custom_cart');
                    $this->session->set_flashdata('message', "Đặt hàng thành công! Đơn hàng của bạn sẽ được thanh toán bằng hình thức COD (Tiền mặt).");
                    redirect(build_cart_url());
                }
            }
        }

        if ($this->session->userdata('user')) {
            $this->data['user'] = $this->session->userdata('user');
        }

        $this->data['checkout_voucher'] = $this->session->userdata('checkout_voucher');
        $this->data['checkout_apply_url'] = site_url('thanh-toan/apply_voucher');

        $this->render_frontend_sub('site/order/index');
    }

    // AJAX áp dụng voucher (session + JSON)
    public function apply_voucher()
    {
        $this->output->set_content_type('application/json', 'utf-8');

        $subtotal = $this->_cart_subtotal();
        if ($subtotal <= 0) {
            echo json_encode(array('ok' => false, 'message' => 'Giỏ hàng trống.'));
            return;
        }

        $user_id = 0;
        if ($this->session->userdata('user')) {
            $user_id = (int) $this->session->userdata('user')->id;
        }
        if ($user_id <= 0) {
            echo json_encode(array('ok' => false, 'message' => 'Vui lòng đăng nhập để dùng voucher.'));
            return;
        }

        $this->load->helper('loyalty');
        $this->load->model('voucher_model');
        $code = loyalty_normalize_voucher_code($this->input->post('voucher_code'));

        if ($code === '') {
            $this->session->unset_userdata('checkout_voucher');
            echo json_encode(array(
                'ok' => true,
                'cleared' => true,
                'message' => 'Đã bỏ mã voucher.',
                'subtotal' => $subtotal,
                'discount' => 0,
                'final' => $subtotal,
                'subtotal_fmt' => $this->_money_fmt($subtotal),
                'final_fmt' => $this->_money_fmt($subtotal),
            ));
            return;
        }

        $check = $this->voucher_model->validate_for_checkout($code, $user_id, $subtotal);
        if (!$check['ok']) {
            $this->session->unset_userdata('checkout_voucher');
            echo json_encode(array('ok' => false, 'message' => $check['message']));
            return;
        }

        $discount = (int) $check['discount'];
        $final = max(0, $subtotal - $discount);
        $payload = array(
            'code' => $code,
            'discount' => $discount,
            'cart_subtotal' => $subtotal,
            'final' => $final,
        );
        $this->session->set_userdata('checkout_voucher', $payload);

        $msg = 'Áp dụng mã thành công';
        if (!empty($check['voucher']->name)) {
            $msg .= ': ' . $check['voucher']->name;
        }
        $msg .= '.';

        echo json_encode(array(
            'ok' => true,
            'message' => $msg,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'final' => $final,
            'subtotal_fmt' => $this->_money_fmt($subtotal),
            'discount_fmt' => $this->_money_fmt($discount),
            'final_fmt' => $this->_money_fmt($final),
        ));
    }

    public function checkout_qr()
    {
        $id = $this->uri->segment(3);
        if (empty($id)) {
            redirect(base_url());
        }

        $this->load->model('transaction_model');
        $transaction = $this->transaction_model->get_info($id);

        if (empty($transaction)) {
            redirect(base_url());
        }

        if ($transaction->payment !== 'Chuyển khoản' || (string) $transaction->status !== '0') {
            $this->session->set_flashdata('message', 'Không thể xem mã QR cho đơn hàng này.');
            redirect(build_cart_url());
        }

        if (!$this->_can_access_order($transaction)) {
            $this->session->set_flashdata('message', 'Bạn không có quyền xem thông tin thanh toán đơn hàng này.');
            redirect(base_url());
        }

        $BANK_ID = "vietcombank";        
        $ACCOUNT_NO = "1234567890";      
        $ACCOUNT_NAME = "NGUYEN VAN A";   
        
        $AMOUNT = $transaction->amount;   
        $DESCRIPTION = "DH" . $transaction->id; 

        $qr_image_url = "https://img.vietqr.io/image/{$BANK_ID}-{$ACCOUNT_NO}-compact2.png?amount={$AMOUNT}&addInfo=" . urlencode($DESCRIPTION) . "&accountName=" . urlencode($ACCOUNT_NAME);

        $this->data['qr_image_url'] = $qr_image_url;
        $this->data['transaction'] = $transaction;

        // Xóa giỏ sau khi hiển thị QR (chuyển khoản)
        $this->session->unset_userdata('custom_cart');
        
        $this->render_frontend_sub('site/order/checkout_qr');
    }
    public function cancel()
    {
        $transaction_id = $this->uri->segment(3);
        if (empty($transaction_id)) {
            $this->session->set_flashdata('message', 'Không tìm thấy mã đơn hàng!');
            redirect(base_url());
        }

        $this->load->model('transaction_model');
        $transaction = $this->transaction_model->get_info($transaction_id);

        if (empty($transaction)) {
            $this->session->set_flashdata('message', 'Đơn hàng không tồn tại trên hệ thống!');
            redirect(base_url());
        }

        // Chủ đơn hoặc phiên order_access_ids
        if (!$this->_can_access_order($transaction)) {
            $this->session->set_flashdata('message', 'Bạn không có quyền xử lý đơn hàng này!');
            redirect(base_url());
        }

        if (!in_array((string) $transaction->status, array('0', '1'), true)) {
            $this->session->set_flashdata('message', 'Đơn hàng không thể hủy ở trạng thái hiện tại.');
            redirect(base_url('user'));
        }

        $reason = $this->input->get('reason');
        $reason = !empty($reason) ? trim(urldecode($reason)) : "Khách hàng tự hủy đơn";

        // Hoàn kho + loyalty; status 4 = đã hủy
        if ($transaction->status != '4') {
            $reverse_buyed = ((string) $transaction->status === '3');
            $this->transaction_model->release_stock_for_transaction($transaction_id, $reverse_buyed);

            $this->load->model('loyalty_model');
            $this->loyalty_model->on_order_cancelled($transaction, $reverse_buyed);

            $this->transaction_model->update($transaction_id, array(
                'status' => '4',
                'reason' => $reason
            ));

            $this->session->set_flashdata('message', 'Hủy đơn hàng thành công! Sản phẩm đã được hoàn lại kho hàng.');
        } else {
            $this->session->set_flashdata('message', 'Đơn hàng này đã được hủy từ trước.');
        }

        redirect(base_url('user')); 
    }

    // Khách không đăng nhập vẫn hủy/xem QR qua order_access_ids
    private function _remember_order_access($transaction_id)
    {
        $ids = $this->session->userdata('order_access_ids');
        if (!is_array($ids)) {
            $ids = array();
        }
        $ids[(int) $transaction_id] = time();
        $this->session->set_userdata('order_access_ids', $ids);
    }

    private function _can_access_order($transaction)
    {
        if (empty($transaction)) {
            return false;
        }

        $order_id = (int) $transaction->id;

        if ($this->session->userdata('user')) {
            $user = $this->session->userdata('user');
            if ((int) $transaction->user_id > 0) {
                return (int) $transaction->user_id === (int) $user->id;
            }
        }

        $ids = $this->session->userdata('order_access_ids');
        return is_array($ids) && !empty($ids[$order_id]);
    }

    private function _cart_subtotal()
    {
        $carts = $this->session->userdata('custom_cart');
        if (!is_array($carts)) {
            return 0;
        }
        $total = 0;
        foreach ($carts as $value) {
            $total += (int) $value['subtotal'];
        }
        return $total;
    }

    private function _refresh_checkout_voucher($user_id, $cart_subtotal)
    {
        $applied = $this->session->userdata('checkout_voucher');
        if (!is_array($applied) || empty($applied['code'])) {
            $this->session->unset_userdata('checkout_voucher');
            return;
        }
        if ((int) $applied['cart_subtotal'] !== (int) $cart_subtotal) {
            $this->session->unset_userdata('checkout_voucher');
            return;
        }
        if ((int) $user_id <= 0) {
            $this->session->unset_userdata('checkout_voucher');
            return;
        }

        $this->load->helper('loyalty');
        $this->load->model('voucher_model');
        $code = loyalty_normalize_voucher_code($applied['code']);
        $check = $this->voucher_model->validate_for_checkout($code, (int) $user_id, (int) $cart_subtotal);
        if (!$check['ok']) {
            $this->session->unset_userdata('checkout_voucher');
            return;
        }

        $discount = (int) $check['discount'];
        $this->session->set_userdata('checkout_voucher', array(
            'code' => $code,
            'discount' => $discount,
            'cart_subtotal' => (int) $cart_subtotal,
            'final' => max(0, (int) $cart_subtotal - $discount),
        ));
    }

    private function _money_fmt($amount)
    {
        return number_format((int) $amount, 0, ',', '.') . ' ₫';
    }

    private function _sanitize_cart(array $carts)
    {
        $clean = array();
        $removed = array();

        foreach ($carts as $rowid => $item) {
            $product_id = isset($item['id']) ? (int) $item['id'] : 0;
            if ($product_id <= 0) {
                continue;
            }

            $product = $this->product_model->get_info($product_id);
            if (empty($product)) {
                $removed[] = !empty($item['name']) ? $item['name'] : ('#' . $product_id);
                continue;
            }

            $clean[$rowid] = $item;
        }

        return array(
            'cart' => $clean,
            'removed' => $removed,
        );
    }
}