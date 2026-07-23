<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends MY_Frontend_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model('product_model');
        $this->load->model('product_review_model');
        $this->load->model('catalog_model');
        
        $this->load->model('slider_model');
        $this->data['slider'] = $this->slider_model->get_list();

        $this->data['catalog'] = $this->_get_catalog_menu();
    }

    private function _get_catalog_menu()
    {
        $input_catalog = array();
        $input_catalog['where'] = array('parent_id' => 1); 
        $input_catalog['order'] = array('id', 'ASC');
        $catalog_list = $this->catalog_model->get_list($input_catalog);
        
        foreach ($catalog_list as $row)
        {
            $input_sub = array();
            $input_sub['where'] = array('parent_id' => $row->id);
            $input_sub['order'] = array('id', 'ASC');
            $subs = $this->catalog_model->get_list($input_sub);

            foreach ($subs as $sub)
            {
                $input_child = array();
                $input_child['where'] = array('parent_id' => $sub->id);
                $childs = $this->catalog_model->get_list($input_child);
                $sub->sub = $childs; 
            }
            
            $row->sub = $subs;   
            $row->subs = $subs;  
        }
        return $catalog_list;
    }

    public function index()
    {
        $this->render_frontend_sub('site/product/index');
    }

    public function view($id = null)
    {
        if ($id === null || $id === '') {
            $id = $this->uri->rsegment(3);
        }
        $id = intval($id);
        $product = $this->product_model->get_info($id);
        if (empty($product)) {
            $this->session->set_flashdata('message_fail', 'Sản phẩm không tồn tại');
            redirect(base_url());
        }

        if ($this->uri->segment(1) === 'product' && in_array($this->uri->segment(2), array('view', 'detail'), true)) {
            seo_redirect(build_product_url($product));
        }

        $uri_segment = $this->uri->segment(1);
        if (preg_match('/^(.+)-p(\d+)$/i', $uri_segment, $m)) {
            $expected = seo_slugify($product->name);
            if ((int) $m[2] === $id && $expected !== '' && $m[1] !== $expected) {
                seo_redirect(build_product_url($product));
            }
        }

        $this->data['product']=$product;
        $catalog_product = $this->catalog_model->get_info($product->catalog_id);
        $this->data['catalog_product']=$catalog_product;

        $view = $this->session->userdata('session_view');
        $view = (!is_array($view)) ? array() : $view;
        if (!isset($view[$id])) {
            $view[$id]=TRUE;
            $this->session->set_userdata('session_view',$view);
            $data = array();
            $data['view'] = $product->view + 1;
            $this->product_model->update($id,$data);
        }
        
        $image_list = json_decode($product->image_list);
        $this->data['image_list'] = $image_list;

        $product_colors = [];
        if (!empty($product->color)) {
            $product_colors = explode(',', $product->color);
        }
        $this->data['product_colors'] = $product_colors;

        $product_sizes = [];
        if (!empty($product->size)) {
            $product_sizes = explode(',', $product->size);
        }
        $this->data['product_sizes'] = $product_sizes;

        $this->load->library('product_service');
        $this->data['variant_stock_map'] = $this->product_service->get_variant_stock_map($id);

        $input = array();
        $input['where'] = array('catalog_id' => $product->catalog_id);
        $input['limit'] = array('4','0');
        $productsub = $this->product_model->get_list($input);
        $this->data['productsub'] = $productsub;
        
        $input = array();
        $input['order'] = array('buyed', 'DESC');
        $input['limit'] = array('4','0');
        $productview = $this->product_model->get_list($input);
        $this->data['productview'] = $productview;

        $this->data['canonical_url'] = build_product_url($product);

        $review_stats = array(
            'rate_count' => (int) $product->rate_count,
            'rate_total' => (int) $product->rate_total,
        );
        $review_breakdown = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0);
        $product_reviews = array();
        $user_already_rated = FALSE;
        $user_review = FALSE;

        if ($this->db->table_exists('product_review')) {
            $product_reviews = $this->product_review_model->get_by_product($id, 50);
            $review_breakdown = $this->product_review_model->get_star_breakdown($id);
            $review_count = array_sum($review_breakdown);
            if ($review_count > 0) {
                $review_total = 0;
                foreach ($review_breakdown as $star => $count) {
                    $review_total += ((int) $star) * (int) $count;
                }
                $review_stats = array(
                    'rate_count' => $review_count,
                    'rate_total' => $review_total,
                );
                $product->rate_count = $review_count;
                $product->rate_total = $review_total;
            }

            $user_login = $this->session->userdata('user');
            $session_token = $this->_get_rating_session_token();
            $user_id = ($user_login && isset($user_login->id)) ? (int) $user_login->id : 0;
            $user_review = $this->product_review_model->get_user_review($id, $user_id, $session_token);
            $user_already_rated = ($user_review !== FALSE);
        }

        $this->data['product_reviews'] = $product_reviews;
        $this->data['review_breakdown'] = $review_breakdown;
        $this->data['user_already_rated'] = $user_already_rated;
        $this->data['user_review'] = $user_review;
        
        $this->render_frontend_sub('site/product/view');
    }

    public function catalog_seo($slug = '', $price_slug = '', $offset = 0)
    {
        if ($price_slug === '_') {
            $price_slug = '';
        }
        $id = seo_resolve_catalog_id_by_slug($slug);
        if ($id <= 0) {
            show_404();
        }
        if ($price_slug !== '') {
            $range = seo_price_slug_to_range($price_slug);
            if ($range === '') {
                show_404();
            }
            $_GET['price_range'] = $range;
        }
        $this->catalog($id, (int) $offset, $price_slug);
    }

    public function catalog($id = 0, $seo_offset = null, $seo_price_slug = null)
    {
        $id = intval($id);
        $catalog = $this->catalog_model->get_info($id);
        
        if (empty($catalog)) {
            redirect(base_url());
        }

        $first_seg = $this->uri->segment(1);
        if ($first_seg !== 'product' && $this->input->get('price_range')) {
            $already_in_path = ($seo_price_slug !== null && $seo_price_slug !== '');
            if (!$already_in_path) {
                $url = build_category_url($catalog, $this->input->get('price_range'));
                $qs = $_SERVER['QUERY_STRING'] ?? '';
                if ($qs !== '') {
                    parse_str($qs, $parsed);
                    unset($parsed['price_range']);
                    if (!empty($parsed)) {
                        $url .= '?' . http_build_query($parsed);
                    }
                }
                seo_redirect($url);
            }
        }

        if ($this->db->table_exists('master_colors')) {
            $this->data['master_colors'] = $this->db->get('master_colors')->result();
        }

        $this->data['catalog_p'] = $catalog;

        $current_root_title = $catalog->name;
        $current_root_id = 0;
        
        if ($catalog->parent_id == 1) {
            $current_root_id = $catalog->id;
        } else {
            $temp_catalog = $catalog;
            while ($temp_catalog && $temp_catalog->parent_id != 1 && $temp_catalog->parent_id != 0) {
                $temp_catalog = $this->catalog_model->get_info($temp_catalog->parent_id);
            }
            if ($temp_catalog && $temp_catalog->parent_id == 1) {
                $current_root_id = $temp_catalog->id;
                $current_root_title = $temp_catalog->name; 
            } else if ($temp_catalog) {
                $current_root_id = $temp_catalog->id;
                $current_root_title = $temp_catalog->name;
            }
        }

        $is_filtering = $this->input->get('category') || $this->input->get('size') || $this->input->get('color') || $this->input->get('price_range');

        if ($is_filtering) {
            $this->data['main_catalog_title'] = $current_root_title;
        } else {
            $this->data['main_catalog_title'] = $catalog->name;
        }

        $catalog_list = array();
        if ($current_root_id > 0) {
            $input_filter_cat = array();
            $input_filter_cat['where'] = array('parent_id' => $current_root_id);
            $input_filter_cat['order'] = array('id', 'ASC');
            $catalog_list = $this->catalog_model->get_list($input_filter_cat);

            foreach ($catalog_list as $parent) {
                $input_sub = array();
                $input_sub['where'] = array('parent_id' => $parent->id);
                $input_sub['order'] = array('id', 'ASC'); 
                $parent->subs = $this->catalog_model->get_list($input_sub);
            }
        }
        $this->data['catalog_list'] = $catalog_list;

        $chosen_cats = $this->input->get('category');
        $final_catalog_ids = array();
        if (!empty($chosen_cats) && is_array($chosen_cats)) {
            foreach ($chosen_cats as $f_id) {
                $final_catalog_ids[] = intval($f_id);
                $sub_1 = $this->catalog_model->get_list(array('where' => array('parent_id' => $f_id)));
                if (!empty($sub_1)) {
                    foreach ($sub_1 as $s1) {
                        $final_catalog_ids[] = $s1->id;
                        $sub_2 = $this->catalog_model->get_list(array('where' => array('parent_id' => $s1->id)));
                        if (!empty($sub_2)) {
                            foreach ($sub_2 as $s2) { $final_catalog_ids[] = $s2->id; }
                        }
                    }
                }
            }
        } else {
            $final_catalog_ids[] = intval($id);
            $current_subs = $this->catalog_model->get_list(array('where' => array('parent_id' => $id)));
            if (!empty($current_subs)) {
                foreach ($current_subs as $c_sub) {
                    $final_catalog_ids[] = $c_sub->id;
                    $current_subs_2 = $this->catalog_model->get_list(array('where' => array('parent_id' => $c_sub->id)));
                    if (!empty($current_subs_2)) {
                        foreach ($current_subs_2 as $c_sub2) { $final_catalog_ids[] = $c_sub2->id; }
                    }
                }
            }
        }
        $final_catalog_ids = array_unique($final_catalog_ids);
        $this->_apply_product_catalog_filters($final_catalog_ids);

        $count_db = clone $this->db;
        $total = $count_db->count_all_results('product');
        $this->data['total'] = $total;

        $this->db->reset_query();
        $this->_apply_product_catalog_filters($final_catalog_ids);

        $this->load->library('pagination');

        $price_range_get = $this->input->get('price_range');
        $price_slug_active = ($seo_price_slug !== null && $seo_price_slug !== '')
            ? $seo_price_slug
            : seo_price_range_to_slug($price_range_get);

        if ($seo_offset !== null) {
            $segment = (int) $seo_offset;
            $pagination_uri = ($price_slug_active !== '') ? 3 : 2;
        } elseif ($this->uri->segment(1) === 'product' && $this->uri->segment(2) === 'catalog') {
            $segment = intval($this->uri->segment(4));
            $pagination_uri = 4;
        } else {
            $pagination_uri = ($price_slug_active !== '') ? 3 : 2;
            $segment = intval($this->uri->segment($pagination_uri));
        }

        $config = array(
            'base_url'           => rtrim(build_category_url($catalog, $price_range_get ?: null), '/') . '/',
            'total_rows'         => $total,
            'per_page'           => 8,
            'uri_segment'        => $pagination_uri,
            'reuse_query_string' => FALSE,
            'full_tag_open'   => '<div class="jm-modern-pagination"><ul>',
            'full_tag_close'  => '</ul></div>',
            'first_link'      => false,
            'last_link'       => false,
            'next_link'       => 'Next <i class="glyphicon glyphicon-menu-right"></i>',
            'prev_link'       => '<i class="glyphicon glyphicon-menu-left"></i> Prev',
            'cur_tag_open'    => '<li class="active"><span>',
            'cur_tag_close'   => '</span></li>',
            'num_tag_open'    => '<li>',
            'num_tag_close'   => '</li>',
            'next_tag_open'  => '<li class="next">',
            'next_tag_close' => '</li>',
            'prev_tag_open'  => '<li class="prev">',
            'prev_tag_close' => '</li>'
        );

        $this->load->helper('seo_url');
        $filter_query = catalog_build_filter_query();
        if ($filter_query !== '') {
            $config['suffix'] = $filter_query;
        }

        $this->pagination->initialize($config);

        $sort = $this->input->get('sort');
        if ($sort == 'price_asc') {
            $this->db->order_by('(price - discount)', 'ASC', FALSE); 
        } elseif ($sort == 'price_desc') {
            $this->db->order_by('(price - discount)', 'DESC', FALSE); 
        } else {
            $this->db->order_by('id', 'DESC');
        }
        
        $this->db->limit($config['per_page'], $segment);
        
        $product_list = $this->db->get('product')->result();
        $this->data['product_list'] = $product_list;

        $canon = build_category_url($catalog, $price_range_get ?: null);
        if ($segment > 0) {
            $canon = rtrim($canon, '/') . '/' . $segment;
        }
        $this->data['canonical_url'] = $canon;

        $this->render_frontend_sub('site/product/catalog');
    }

    public function hot()
    {
        $this->db->where('buyed >', 0);

        $chosen_cats = $this->input->get('category');
        $final_catalog_ids = array();
        if (!empty($chosen_cats) && is_array($chosen_cats)) {
            foreach ($chosen_cats as $f_id) {
                $final_catalog_ids[] = intval($f_id);
                $sub_1 = $this->catalog_model->get_list(array('where' => array('parent_id' => $f_id)));
                if (!empty($sub_1)) {
                    foreach ($sub_1 as $s1) {
                        $final_catalog_ids[] = $s1->id;
                        $sub_2 = $this->catalog_model->get_list(array('where' => array('parent_id' => $s1->id)));
                        if (!empty($sub_2)) {
                            foreach ($sub_2 as $s2) {
                                $final_catalog_ids[] = $s2->id;
                            }
                        }
                    }
                }
            }
        }
        $final_catalog_ids = array_unique($final_catalog_ids);
        if (!empty($final_catalog_ids)) {
            $this->db->where_in('catalog_id', $final_catalog_ids);
        }

        $chosen_sizes = $this->input->get('size');
        if (!empty($chosen_sizes)) {
            if (!is_array($chosen_sizes)) {
                $chosen_sizes = array($chosen_sizes);
            }
            $this->db->group_start();
            foreach ($chosen_sizes as $sz) {
                if (trim($sz) !== '') {
                    $this->db->or_like('size', trim($sz));
                }
            }
            $this->db->group_end();
        }

        $chosen_colors = $this->input->get('color');
        if (!empty($chosen_colors)) {
            if (!is_array($chosen_colors)) {
                $chosen_colors = array($chosen_colors);
            }
            $this->db->group_start();
            foreach ($chosen_colors as $cl) {
                if (trim($cl) !== '') {
                    $clean_search = mb_strtolower($cl, 'UTF-8');
                    $clean_search = str_replace(' ', '', $clean_search);
                    $safe_color = $this->db->escape_like_str($clean_search);
                    $this->db->or_where("REPLACE(LOWER(color), ' ', '') LIKE '%" . $safe_color . "%'", null, false);
                }
            }
            $this->db->group_end();
        }

        $price_range = $this->input->get('price_range');
        if (!empty($price_range)) {
            $arr_p = explode('-', $price_range);
            if (count($arr_p) == 2) {
                $min_p = intval($arr_p[0]);
                $max_p = intval($arr_p[1]);
                $this->db->where('(price - discount) >= ' . $min_p, null, false);
                $this->db->where('(price - discount) <= ' . $max_p, null, false);
            }
        }

        $count_db = clone $this->db;
        $total = $count_db->count_all_results('product');
        $this->data['total'] = $total;

        $this->load->library('pagination');
        $config = array(
            'base_url'             => base_url('ban-chay'),
            'total_rows'           => $total,
            'per_page'             => 8,
            'page_query_string'    => true,
            'query_string_segment' => 'per_page',
            'reuse_query_string'   => true,
            'full_tag_open'        => '<div class="jm-modern-pagination"><ul>',
            'full_tag_close'       => '</ul></div>',
            'first_link'           => false,
            'last_link'            => false,
            'next_link'            => 'Next',
            'prev_link'            => 'Prev',
            'cur_tag_open'         => '<li class="active"><span>',
            'cur_tag_close'        => '</span></li>',
            'num_tag_open'         => '<li>',
            'num_tag_close'        => '</li>',
            'next_tag_open'        => '<li class="next">',
            'next_tag_close'       => '</li>',
            'prev_tag_open'        => '<li class="prev">',
            'prev_tag_close'       => '</li>',
        );
        $this->pagination->initialize($config);

        $segment = (int) $this->input->get('per_page');

        $sort = $this->input->get('sort');
        if ($sort === 'price_asc') {
            $this->db->order_by('(price - discount)', 'ASC', false);
        } elseif ($sort === 'price_desc') {
            $this->db->order_by('(price - discount)', 'DESC', false);
        } elseif ($sort === 'new') {
            $this->db->order_by('id', 'DESC');
        } else {
            $this->db->order_by('buyed', 'DESC');
        }

        $this->db->limit($config['per_page'], $segment);
        $product_list = $this->db->get('product')->result();
        $this->data['product_list'] = $product_list;

        if ($this->db->table_exists('master_colors')) {
            $this->data['master_colors'] = $this->db->get('master_colors')->result();
        }

        $input_filter_cat = array();
        $input_filter_cat['where'] = array('parent_id' => 1);
        $input_filter_cat['order'] = array('id', 'ASC');
        $catalog_list = $this->catalog_model->get_list($input_filter_cat);
        foreach ($catalog_list as $parent) {
            $input_sub = array();
            $input_sub['where'] = array('parent_id' => $parent->id);
            $input_sub['order'] = array('id', 'ASC');
            $parent->subs = $this->catalog_model->get_list($input_sub);
        }
        $this->data['catalog_list'] = $catalog_list;

        $this->data['page_title'] = 'Sản phẩm bán chạy - ' . shop_name();
        $this->data['is_hot'] = true;
        $this->data['is_hot_list'] = true;
        $this->data['main_catalog_title'] = 'Sản phẩm bán chạy';
        $this->data['filter_action'] = base_url('ban-chay');
        $this->data['canonical_url'] = base_url('ban-chay');

        $this->render_frontend_sub('site/product/hot');
    }

    public function views()
    {
        $input = array();
        $input['order'] = array('view','DESC');

        $total = $this->product_model->get_total($input);
        $this->data['total'] = $total;

        $this->load->library('pagination');
        
        $config = array(
            'base_url'    => base_url('product/views'),
            'total_rows'  => $total,
            'per_page'    => 8,
            'uri_segment' => 4, 
            'full_tag_open'   => '<div class="jm-modern-pagination"><ul>',
            'full_tag_close'  => '</ul></div>',
            'first_link'      => false,
            'last_link'       => false,
            'next_link'       => 'Next <i class="glyphicon glyphicon-menu-right"></i>',
            'prev_link'       => '<i class="glyphicon glyphicon-menu-left"></i> Prev',
            'cur_tag_open'    => '<li class="active"><span>',
            'cur_tag_close'   => '</span></li>',
            'num_tag_open'    => '<li>',
            'num_tag_close'   => '</li>',
            'next_tag_open'   => '<li class="next">',
            'next_tag_close'  => '</li>',
            'prev_tag_open'   => '<li class="prev">',
            'prev_tag_close'  => '</li>'
        );
        $this->pagination->initialize($config);

        $segment = $this->uri->segment(4); 
        $segment = intval($segment);

        $input['limit'] = array($config['per_page'], $segment);

        $product_list = $this->product_model->get_list($input);
        $this->data['product_list'] = $product_list;
        $this->render_frontend_sub('site/product/views');
    }

    public function news()
    {
        $chosen_cats = $this->input->get('category');
        $final_catalog_ids = array();
        if (!empty($chosen_cats) && is_array($chosen_cats)) {
            foreach ($chosen_cats as $f_id) {
                $final_catalog_ids[] = intval($f_id);
                $sub_1 = $this->catalog_model->get_list(array('where' => array('parent_id' => $f_id)));
                if (!empty($sub_1)) {
                    foreach ($sub_1 as $s1) {
                        $final_catalog_ids[] = $s1->id;
                        $sub_2 = $this->catalog_model->get_list(array('where' => array('parent_id' => $s1->id)));
                        if (!empty($sub_2)) {
                            foreach ($sub_2 as $s2) { $final_catalog_ids[] = $s2->id; }
                        }
                    }
                }
            }
        }
        
        $final_catalog_ids = array_unique($final_catalog_ids);
        if(!empty($final_catalog_ids)){
            $this->db->where_in('catalog_id', $final_catalog_ids);
        }

        $chosen_sizes = $this->input->get('size');
        if (!empty($chosen_sizes)) {
            if (!is_array($chosen_sizes)) $chosen_sizes = array($chosen_sizes);
            $this->db->group_start();
            foreach ($chosen_sizes as $sz) {
                if (trim($sz) !== '') {
                    $this->db->or_like('size', trim($sz));
                }
            }
            $this->db->group_end();
        }

        $chosen_colors = $this->input->get('color');
        if (!empty($chosen_colors)) {
            if (!is_array($chosen_colors)) $chosen_colors = array($chosen_colors);
            $this->db->group_start();
            foreach ($chosen_colors as $cl) {
                if (trim($cl) !== '') {
                    $clean_search = mb_strtolower($cl, 'UTF-8');
                    $clean_search = str_replace(' ', '', $clean_search);
                    $safe_color = $this->db->escape_like_str($clean_search);
                    $this->db->or_where("REPLACE(LOWER(color), ' ', '') LIKE '%" . $safe_color . "%'", NULL, FALSE);
                }
            }
            $this->db->group_end();
        }

        $price_range = $this->input->get('price_range');
        if (!empty($price_range)) {
            $arr_p = explode('-', $price_range);
            if (count($arr_p) == 2) {
                $min_p = intval($arr_p[0]);
                $max_p = intval($arr_p[1]);
                $this->db->where("(price - discount) >= $min_p", NULL, FALSE);
                $this->db->where("(price - discount) <= $max_p", NULL, FALSE);
            }
        }

        $count_db = clone $this->db;
        $total = $count_db->count_all_results('product');
        $this->data['total'] = $total;

        $this->load->library('pagination');
        $config = array(
            'base_url'             => base_url('moi'),
            'total_rows'           => $total,
            'per_page'             => 8,
            'page_query_string'    => TRUE,          
            'query_string_segment' => 'per_page',    
            'reuse_query_string'   => TRUE,          
            'full_tag_open'      => '<div class="jm-modern-pagination"><ul>',
            'full_tag_close'     => '</ul></div>',
            'first_link'         => false,
            'last_link'          => false,
            'next_link'          => 'Next <i class="glyphicon glyphicon-menu-right"></i>',
            'prev_link'          => '<i class="glyphicon glyphicon-menu-left"></i> Prev',
            'cur_tag_open'       => '<li class="active"><span>',
            'cur_tag_close'      => '</span></li>',
            'num_tag_open'       => '<li>',
            'num_tag_close'      => '</li>',
            'next_tag_open'      => '<li class="next">',
            'next_tag_close'     => '</li>',
            'prev_tag_open'      => '<li class="prev">',
            'prev_tag_close'     => '</li>'
        );
        $this->pagination->initialize($config);

        $segment = $this->input->get('per_page'); 
        $segment = intval($segment);

        $sort = $this->input->get('sort');
        if ($sort == 'price_asc') {
            $this->db->order_by('(price - discount)', 'ASC', FALSE);
        } elseif ($sort == 'price_desc') {
            $this->db->order_by('(price - discount)', 'DESC', FALSE);
        } else {
            $this->db->order_by('id', 'DESC');
        }
        
        $this->db->limit($config['per_page'], $segment);
        
        $product_list = $this->db->get('product')->result();
        $this->data['product_list'] = $product_list;
        
        $this->data['page_title'] = 'Sản phẩm mới nhất - ' . shop_name();
        $this->render_frontend_sub('site/product/new');
    }

    public function search_seo($keyword_slug = '', $offset = 0)
    {
        if ($keyword_slug === '' || $keyword_slug === null) {
            $kw = trim((string) $this->input->get('key-search'));
            if ($kw !== '') {
                seo_redirect(build_search_url($kw));
            }
        }
        $keyword = seo_search_keyword_from_slug($keyword_slug);
        $this->search($keyword, (int) $offset);
    }

    public function search($keyword = null, $seo_offset = null)
    {
        if ($this->uri->segment(1) === 'product' && $this->uri->segment(2) === 'search') {
            $kw = trim((string) $this->input->get('key-search'));
            $page = (int) $this->input->get('per_page');
            seo_redirect(build_search_url($kw, $page > 0 ? $page : null));
        }

        if ($keyword === null) {
            $keyword = $this->input->get('key-search');
        }
        $keyword = trim((string) $keyword);

        $total = 0;
        if (!empty($keyword)) {
            $this->db->like('name', $keyword);
            $total = $this->product_model->get_total();
        }

        $this->load->library('pagination');

        $use_seo_pagination = ($this->uri->segment(1) === 'tim-kiem');
        if ($use_seo_pagination) {
            $segment = ($seo_offset !== null) ? (int) $seo_offset : (int) $this->uri->segment(3);
            $config = array(
                'base_url'           => rtrim(build_search_url($keyword), '/') . '/',
                'total_rows'         => $total,
                'per_page'           => 8,
                'uri_segment'        => 3,
                'reuse_query_string' => TRUE,
                'full_tag_open'   => '<div class="jm-modern-pagination"><ul>',
                'full_tag_close'  => '</ul></div>',
                'first_link'      => false,
                'last_link'       => false,
                'next_link'       => 'Next <i class="glyphicon glyphicon-menu-right"></i>',
                'prev_link'       => '<i class="glyphicon glyphicon-menu-left"></i> Prev',
                'cur_tag_open'    => '<li class="active"><span>',
                'cur_tag_close'   => '</span></li>',
                'num_tag_open'    => '<li>',
                'num_tag_close'   => '</li>',
                'next_tag_open'   => '<li class="next">',
                'next_tag_close' => '</li>',
                'prev_tag_open'  => '<li class="prev">',
                'prev_tag_close' => '</li>',
            );
        } else {
            $config = array(
                'base_url'              => build_search_url($keyword),
                'total_rows'           => $total,
                'per_page'              => 8,
                'page_query_string'    => TRUE,
                'query_string_segment' => 'per_page',
                'full_tag_open'   => '<div class="jm-modern-pagination"><ul>',
                'full_tag_close'  => '</ul></div>',
                'first_link'      => false,
                'last_link'       => false,
                'next_link'       => 'Next <i class="glyphicon glyphicon-menu-right"></i>',
                'prev_link'       => '<i class="glyphicon glyphicon-menu-left"></i> Prev',
                'cur_tag_open'    => '<li class="active"><span>',
                'cur_tag_close'   => '</span></li>',
                'num_tag_open'    => '<li>',
                'num_tag_close'   => '</li>',
                'next_tag_open'   => '<li class="next">',
                'next_tag_close' => '</li>',
                'prev_tag_open'  => '<li class="prev">',
                'prev_tag_close' => '</li>',
            );
        }

        $this->pagination->initialize($config);

        if (!$use_seo_pagination) {
            $segment = (int) $this->input->get('per_page');
        }

        $product_list = array();
        if (!empty($keyword)) {
            $this->db->like('name', $keyword);
            $this->db->order_by('id', 'DESC');
            $this->db->limit($config['per_page'], $segment);
            $product_list = $this->product_model->get_list();
        }

        $this->data['total'] = $total;
        $this->data['product_list'] = $product_list;
        $this->data['keyword'] = $keyword;

        $canon = build_search_url($keyword);
        if ($use_seo_pagination && $segment > 0) {
            $canon = rtrim($canon, '/') . '/' . $segment;
        }
        $this->data['canonical_url'] = $canon;

        $this->render_frontend_sub('site/product/search');
    }

    public function raty()
    {
        $id = (int) $this->input->post('id');
        $score = (int) round((float) $this->input->post('score'));
        $result = array('complete' => FALSE);

        if ($id <= 0 || $score < 1 || $score > 5) {
            $result['msg'] = 'Dữ liệu đánh giá không hợp lệ';
            echo json_encode($result);
            exit();
        }

        $product = $this->product_model->get_info($id);
        if (!$product) {
            $result['msg'] = 'Sản phẩm không tồn tại';
            echo json_encode($result);
            exit();
        }

        if (!$this->db->table_exists('product_review')) {
            $result['msg'] = 'Chưa cấu hình bảng lưu đánh giá. Vui lòng chạy file SQL product_review.sql';
            echo json_encode($result);
            exit();
        }

        $user_login = $this->session->userdata('user');
        $user_id = ($user_login && isset($user_login->id)) ? (int) $user_login->id : 0;
        $user_name = ($user_login && !empty($user_login->name)) ? $user_login->name : 'Khách hàng';
        $session_token = $this->_get_rating_session_token();

        $existing = $this->product_review_model->get_user_review($id, $user_id, $session_token);
        if ($existing) {
            if ((int) $existing->stars === $score) {
                $result['complete'] = TRUE;
                $result['msg'] = 'Bạn đang đánh giá ' . $score . ' sao';
                echo json_encode($result);
                exit();
            }

            $this->product_review_model->update($existing->id, array(
                'stars' => $score,
                'user_name' => $user_name,
                'created' => time(),
            ));
            $this->product_review_model->sync_product_stats($id);

            $result['complete'] = TRUE;
            $result['updated'] = TRUE;
            $result['msg'] = 'Đã cập nhật đánh giá thành ' . $score . ' sao';
            echo json_encode($result);
            exit();
        }

        $review_data = array(
            'product_id' => $id,
            'user_id' => $user_id,
            'user_name' => $user_name,
            'stars' => $score,
            'session_token' => ($user_id > 0) ? '' : $session_token,
            'created' => time(),
        );

        if (!$this->product_review_model->create($review_data)) {
            $result['msg'] = 'Không lưu được đánh giá, vui lòng thử lại';
            echo json_encode($result);
            exit();
        }

        $this->product_review_model->sync_product_stats($id);

        $result['complete'] = TRUE;
        $result['msg'] = 'Cảm ơn bạn đã đánh giá ' . $score . ' sao';
        echo json_encode($result);
        exit();
    }

    public function raty_undo()
    {
        $id = (int) $this->input->post('id');
        $result = array('complete' => FALSE);

        if ($id <= 0) {
            $result['msg'] = 'Dữ liệu không hợp lệ';
            echo json_encode($result);
            exit();
        }

        if (!$this->db->table_exists('product_review')) {
            $result['msg'] = 'Chưa cấu hình bảng lưu đánh giá';
            echo json_encode($result);
            exit();
        }

        $product = $this->product_model->get_info($id);
        if (!$product) {
            $result['msg'] = 'Sản phẩm không tồn tại';
            echo json_encode($result);
            exit();
        }

        $user_login = $this->session->userdata('user');
        $user_id = ($user_login && isset($user_login->id)) ? (int) $user_login->id : 0;
        $session_token = $this->_get_rating_session_token();

        if (!$this->product_review_model->delete_user_review($id, $user_id, $session_token)) {
            $result['msg'] = 'Bạn chưa đánh giá sản phẩm này';
            echo json_encode($result);
            exit();
        }

        $this->product_review_model->sync_product_stats($id);

        $result['complete'] = TRUE;
        $result['msg'] = 'Đã hoàn tác đánh giá. Bạn có thể đánh giá lại.';
        echo json_encode($result);
        exit();
    }

    private function _get_rating_session_token()
    {
        $token = $this->session->userdata('rating_guest_token');
        if (!$token) {
            $token = bin2hex(random_bytes(16));
            $this->session->set_userdata('rating_guest_token', $token);
        }
        return $token;
    }

    // WHERE dùng chung cho đếm và phân trang danh mục
    private function _apply_product_catalog_filters($final_catalog_ids)
    {
        if (!empty($final_catalog_ids)) {
            $this->db->where_in('catalog_id', $final_catalog_ids);
        }

        $chosen_sizes = $this->input->get('size');
        if (!empty($chosen_sizes)) {
            if (!is_array($chosen_sizes)) {
                $chosen_sizes = array($chosen_sizes);
            }
            $this->db->group_start();
            foreach ($chosen_sizes as $sz) {
                if (trim($sz) !== '') {
                    $this->db->or_like('size', trim($sz));
                }
            }
            $this->db->group_end();
        }

        $chosen_colors = $this->input->get('color');
        if (!empty($chosen_colors)) {
            if (!is_array($chosen_colors)) {
                $chosen_colors = array($chosen_colors);
            }
            $this->db->group_start();
            foreach ($chosen_colors as $cl) {
                if (trim($cl) !== '') {
                    $clean_search = mb_strtolower($cl, 'UTF-8');
                    $clean_search = str_replace(' ', '', $clean_search);
                    $safe_color = $this->db->escape_like_str($clean_search);
                    $this->db->or_where("REPLACE(LOWER(color), ' ', '') LIKE '%" . $safe_color . "%'", NULL, FALSE);
                }
            }
            $this->db->group_end();
        }

        $price_range = $this->input->get('price_range');
        if (!empty($price_range)) {
            $arr_p = explode('-', $price_range);
            if (count($arr_p) == 2) {
                $min_p = intval($arr_p[0]);
                $max_p = intval($arr_p[1]);
                $this->db->where("(price - discount) >= $min_p", NULL, FALSE);
                $this->db->where("(price - discount) <= $max_p", NULL, FALSE);
            }
        }
    }
}