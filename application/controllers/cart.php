<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cart extends MY_Frontend_Controller {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('product_model');
    }

    private function _check_login()
    {
        if (!$this->session->userdata('user')) {
            $this->session->set_userdata('redirect_back', current_url());
            $this->session->set_flashdata('message', 'Bạn cần đăng nhập tài khoản để thực hiện chức năng này!');
            redirect(base_url('user/login'));
        }
    }

    public function index()
    {
        $this->_check_login();

        $this->data['message'] = $this->session->flashdata('message');
        
        $carts = $this->session->userdata('custom_cart');
        if (!is_array($carts)) { 
            $carts = array(); 
        }
        
        $this->data['carts'] = $carts;

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
            $qty = 1;
            $size = 'L'; 
            $color = 'Trắng phối Đen'; 
        }

        $id = intval($id);
        $qty = intval($qty);
        if ($qty <= 0) { $qty = 1; }

        $product = $this->product_model->get_info($id);
        if (empty($product)) {
            $this->session->set_flashdata('message', 'Sản phẩm không tồn tại trong hệ thống!');
            redirect(base_url());
        }

        // Chặn thêm nếu hết hàng
        $ton_kho = isset($product->quantity) ? intval($product->quantity) : 0;
        if ($ton_kho <= 0) {
            $this->session->set_flashdata('message', 'Xin lỗi, sản phẩm [ ' . $product->name . ' ] đã hết hàng!');
            redirect(build_cart_url());
        }

        // Chặn nếu qty vượt tồn kho
        if ($qty > $ton_kho) {
            $this->session->set_flashdata('message', 'Xin lỗi, sản phẩm [ ' . $product->name . ' ] trong kho hiện chỉ còn ' . $ton_kho . ' sản phẩm!');
            redirect(build_cart_url());
        }

        $price = floatval($product->price);
        if ($product->discount > 0) {
            $price = floatval($product->price - $product->discount);
        }
        if ($price <= 0) { $price = 1000; } 

        // RowID = product id + size + color
        $rowid = $id . '_' . md5($size . '_' . $color);

        $item = array(
            'rowid'    => $rowid,
            'id'       => $id,
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

        $current_cart = $this->session->userdata('custom_cart');
        if (!is_array($current_cart)) {
            $current_cart = array();
        }

        if (isset($current_cart[$rowid])) {
            // Tổng qty trong giỏ không vượt tồn kho
            $tong_sau_them = $current_cart[$rowid]['qty'] + $qty;
            if ($tong_sau_them > $ton_kho) {
                $this->session->set_flashdata('message', 'Sản phẩm [ ' . $product->name . ' ] đã có trong giỏ hàng. Bạn không thể thêm vì tổng số lượng vượt quá mức tồn kho còn lại (' . $ton_kho . ' sản phẩm)!');
                redirect(build_cart_url());
            }
            
            $current_cart[$rowid]['qty'] += $qty;
            $current_cart[$rowid]['subtotal'] = $current_cart[$rowid]['qty'] * $price;
        } else {
            $current_cart[$rowid] = $item;
        }

        $this->session->set_userdata('custom_cart', $current_cart);
        $this->session->set_flashdata('message', 'Thêm vào giỏ hàng thành công');
        
        redirect(build_cart_url());
    }

    public function update()
    {
        $this->_check_login();

        $rowid = $this->uri->segment(3);
        $action = $this->uri->segment(4);
        
        $current_cart = $this->session->userdata('custom_cart');
        if (!is_array($current_cart)) { 
            $current_cart = array(); 
        }

        if (isset($current_cart[$rowid])) {
            if ($action == 'sum') {
                $product = $this->product_model->get_info($current_cart[$rowid]['id']);
                $ton_kho = isset($product->quantity) ? intval($product->quantity) : 0;

                // Tăng qty không vượt tồn kho
                if ($current_cart[$rowid]['qty'] + 1 > $ton_kho) {
                    $this->session->set_flashdata('message', 'Không thể tăng! Số lượng sản phẩm [ ' . $current_cart[$rowid]['name'] . ' ] trong kho chỉ còn tối đa ' . $ton_kho . ' sản phẩm!');
                    redirect(build_cart_url());
                }

                $current_cart[$rowid]['qty'] += 1;
                $current_cart[$rowid]['subtotal'] = $current_cart[$rowid]['qty'] * $current_cart[$rowid]['price'];
            } elseif ($action == 'sub') {
                if ($current_cart[$rowid]['qty'] > 1) {
                    $current_cart[$rowid]['qty'] -= 1;
                    $current_cart[$rowid]['subtotal'] = $current_cart[$rowid]['qty'] * $current_cart[$rowid]['price'];
                } else {
                    unset($current_cart[$rowid]);
                }
            }
            $this->session->set_userdata('custom_cart', $current_cart);
        }
        redirect(build_cart_url());
    }

    public function del()
    {
        $this->_check_login();

        $rowid = $this->uri->segment(3);
        $current_cart = $this->session->userdata('custom_cart');
        if (!is_array($current_cart)) { 
            $current_cart = array(); 
        }

        if (!empty($rowid) && $rowid !== '0' && $rowid !== 'del') {
            if (isset($current_cart[$rowid])) {
                unset($current_cart[$rowid]);
                $this->session->set_userdata('custom_cart', $current_cart);
                $this->session->set_flashdata('message', 'Xóa sản phẩm thành công');
            }
        } else {
            $this->session->unset_userdata('custom_cart');
            $this->session->set_flashdata('message', 'Xóa giỏ hàng thành công');
        }
        redirect(build_cart_url());
    }
}