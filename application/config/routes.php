<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/

$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Admin
$route['admin'] = 'admin/home/index';

$route['admin/export/excel/(:any)'] = 'admin/export/excel/$1';
$route['admin/export/pdf/(:any)'] = 'admin/export/pdf/$1';
$route['admin/export/print_report/(:any)'] = 'admin/export/print_report/$1';

// Alias plural URL → controller cũ
$route['admin/products'] = 'admin/product/index';
$route['admin/products/(.+)'] = 'admin/product/$1';
$route['admin/inventory'] = 'admin/inventory/index';
$route['admin/inventory/low-stock'] = 'admin/inventory/low_stock';
$route['admin/inventory/(.+)'] = 'admin/inventory/$1';
$route['admin/receipts'] = 'admin/stock_receipt/index';
$route['admin/receipts/create'] = 'admin/stock_receipt/add';
$route['admin/receipts/view/(:num)'] = 'admin/stock_receipt/view/$1';
$route['admin/receipts/confirm/(:num)'] = 'admin/stock_receipt/confirm/$1';
$route['admin/receipts/cancel/(:num)'] = 'admin/stock_receipt/cancel/$1';
$route['admin/receipts/(.+)'] = 'admin/stock_receipt/$1';
$route['admin/stock-movements'] = 'admin/stock_movements/index';
$route['admin/stock-movements/(.+)'] = 'admin/stock_movements/$1';
$route['admin/suppliers'] = 'admin/suppliers/index';
$route['admin/suppliers/(.+)'] = 'admin/suppliers/$1';
$route['admin/stock-receipts'] = 'admin/stock_receipt/index';
$route['admin/stock-receipts/(.+)'] = 'admin/stock_receipt/$1';
$route['admin/stock-inventory'] = 'admin/inventory/index';
$route['admin/stock-inventory/low-stock'] = 'admin/inventory/low_stock';
$route['admin/stock-inventory/(.+)'] = 'admin/inventory/$1';
$route['admin/users'] = 'admin/user/index';
$route['admin/users/(.+)'] = 'admin/user/$1';
$route['admin/orders'] = 'admin/transaction/index';
$route['admin/orders/(:num)'] = 'admin/transaction/index/$1';
$route['admin/orders/(.+)'] = 'admin/transaction/$1';
$route['admin/posts'] = 'admin/news/index';
$route['admin/posts/(.+)'] = 'admin/news/$1';

// 301 admin/news cũ
$route['admin/news'] = 'legacy/admin_posts';
$route['admin/news/(.+)'] = 'legacy/admin_posts/$1';

// Admin pages — hỗ trợ /admin/page và /admin/pages
$route['admin/page'] = 'admin/pages';
$route['admin/page/edit/(:num)'] = 'admin/pages/edit/$1';
$route['admin/pages'] = 'admin/pages';
$route['admin/pages/edit/(:num)'] = 'admin/pages/edit/$1';

$route['admin/store'] = 'admin/store/index';
$route['admin/store/add'] = 'admin/store/add';
$route['admin/store/edit/(:num)'] = 'admin/store/edit/$1';
$route['admin/store/delete/(:num)'] = 'admin/store/delete/$1';

$route['admin/tuyendung'] = 'admin/tuyendung/index';
$route['admin/tuyendung/add'] = 'admin/tuyendung/add';
$route['admin/tuyendung/edit/(:num)'] = 'admin/tuyendung/edit/$1';
$route['admin/tuyendung/delete/(:num)'] = 'admin/tuyendung/delete/$1';

// Storefront (URL SEO)
$route['gioi-thieu'] = 'gioithieu';
$route['dang-nhap'] = 'user/login';
$route['dang-ky'] = 'user/register';
$route['quen-mat-khau'] = 'user/forgot_password';
$route['dat-lai-mat-khau/(:any)'] = 'user/reset_password/$1';
$route['dat-lai-mat-khau'] = 'user/reset_password';

// —— SEO storefront (canonical) ——
$route['gio-hang'] = 'cart/index';
$route['gio-hang/them/(:num)'] = 'cart/add/$1';
$route['gio-hang/add/(:num)'] = 'cart/add/$1';
$route['gio-hang/cap-nhat-thuoc-tinh'] = 'cart/update_options';
$route['gio-hang/update/(.+)'] = 'cart/update/$1';
$route['gio-hang/del/(.+)'] = 'cart/del/$1';
$route['gio-hang/del'] = 'cart/del';
$route['gio-hang/(.+)'] = 'cart/$1';
$route['thanh-toan'] = 'order/index';
$route['thanh-toan/apply_voucher'] = 'order/apply_voucher';
$route['thanh-toan/checkout_qr/(:num)'] = 'order/checkout_qr/$1';
$route['thanh-toan/cancel/(:num)'] = 'order/cancel/$1';
$route['thanh-toan/(.+)'] = 'order/$1';

$route['tim-kiem/(:any)/(:num)'] = 'product/search_seo/$1/$2';
$route['tim-kiem/(:any)'] = 'product/search_seo/$1';
$route['tim-kiem'] = 'product/search_seo';

$route['tin-tuc/([a-z0-9-]+)-n(:num)'] = 'news/view/$2';
$route['tin-tuc/(:num)'] = 'news/index/$1';
$route['tin-tuc'] = 'news/index';

// Legacy → 301 (storefront)
$route['product/catalog/(:num)/(:num)'] = 'legacy/catalog/$1/$2';
$route['product/catalog/(:num)'] = 'legacy/catalog/$1';
$route['product/view/(:num)'] = 'legacy/product/$1';
$route['product/detail/(:num)'] = 'legacy/product/$1';
$route['product/search'] = 'legacy/product_search';
$route['cart'] = 'legacy/cart';
$route['cart/(.+)'] = 'legacy/cart_path/$1';
$route['order'] = 'legacy/order';
$route['order/(.+)'] = 'legacy/order_path/$1';
$route['posts'] = 'legacy/posts';
$route['posts/view/(:num)'] = 'legacy/post_view/$1';
$route['posts/(:num)'] = 'legacy/posts_page/$1';
$route['news'] = 'legacy/posts';
$route['news/(.+)'] = 'legacy/posts/$1';

// Alias có phân trang (:num)
$route['ban-chay'] = 'product/hot';
$route['ban-chay/(:num)'] = 'product/hot/$1';

$route['moi'] = 'product/news';
$route['moi/(:num)'] = 'product/news/$1';

$route['khuyen-mai'] = 'product/discount';
$route['khuyen-mai/(:num)'] = 'product/discount/$1';

// Catch-all danh mục/sản phẩm — phải đặt cuối file
$route['ThongTinTuyenDung'] = 'tuyendung/index';
$route['ThongTinTuyenDung/(:any)'] = 'tuyendung/detail/$1';
$route['LienHeHopTacKinhDoanh'] = 'hoptac';

// Product detail (must precede single-segment category catch-all)
$route['([a-zA-Z0-9-]+)-p(:num)'] = 'product/view/$2';

// Legacy category slug-id (301 via legacy)
$route['([a-zA-Z0-9-]+)-c(:num)/(:num)'] = 'legacy/catalog_c/$2/$3';
$route['([a-zA-Z0-9-]+)-c(:num)'] = 'legacy/catalog_c/$2';

// Reserved storefront paths (must not be resolved as category slugs)
$route['hethongcuahang'] = 'hethongcuahang/index';
$route['hethongcuahang/(.+)'] = 'hethongcuahang/$1';
$route['VanChuyen'] = 'vanchuyen/index';
$route['vanchuyen'] = 'vanchuyen/index';
$route['vanchuyen/(.+)'] = 'vanchuyen/$1';
$route['tich-diem'] = 'loyalty/index';
$route['TichDiem'] = 'loyalty/index';
$route['user'] = 'user/index';
$route['user/(.+)'] = 'user/$1';
$route['product/index'] = 'product/index';
$route['product/views'] = 'product/views';
$route['product/news'] = 'product/news';
$route['product/raty'] = 'product/raty';
$route['product/raty-undo'] = 'product/raty_undo';

// Category SEO + price filters + pagination offset (same offset semantics as before)
$route['([a-z0-9-]+)/duoi-200k/(:num)'] = 'product/catalog_seo/$1/duoi-200k/$2';
$route['([a-z0-9-]+)/200k-500k/(:num)'] = 'product/catalog_seo/$1/200k-500k/$2';
$route['([a-z0-9-]+)/500k-1tr/(:num)'] = 'product/catalog_seo/$1/500k-1tr/$2';
$route['([a-z0-9-]+)/tren-1tr/(:num)'] = 'product/catalog_seo/$1/tren-1tr/$2';
$route['([a-z0-9-]+)/duoi-200k'] = 'product/catalog_seo/$1/duoi-200k';
$route['([a-z0-9-]+)/200k-500k'] = 'product/catalog_seo/$1/200k-500k';
$route['([a-z0-9-]+)/500k-1tr'] = 'product/catalog_seo/$1/500k-1tr';
$route['([a-z0-9-]+)/tren-1tr'] = 'product/catalog_seo/$1/tren-1tr';
$route['([a-z0-9-]+)/(:num)'] = 'product/catalog_seo/$1/_/$2';
$route['([a-z0-9-]+)'] = 'product/catalog_seo/$1';

// AI + support chat
$route['ai-assistant/send'] = 'aiassistant/send';
$route['ai-assistant/history'] = 'aiassistant/history';
$route['ai-assistant/poll'] = 'aiassistant/poll';
$route['admin/ai-assistant'] = 'admin/aiassistant/settings';
$route['admin/ai-assistant/(.+)'] = 'admin/aiassistant/$1';
$route['admin/support-chat'] = 'admin/supportchat/index';
$route['admin/support-chat/(.+)'] = 'admin/supportchat/$1';
