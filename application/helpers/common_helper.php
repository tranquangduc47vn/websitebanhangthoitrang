<?php 
	function public_url($url=''){
		return base_url('public/'.$url);
	}

	// Storefront assets: assets/site/
	function site_asset_url($url = '')
	{
		return base_url('assets/site/' . ltrim($url, '/'));
	}

	function render_frontend_view($view, array $data = array(), $layout = 'site/layout', $controller = null)
	{
		$CI = $controller ?: get_instance();
		$CI->data = array_merge($CI->data, $data);
		$CI->data['temp'] = $view;
		$CI->load->view($layout, $CI->data);
	}
	function pre($str=''){
		echo '<pre>';
		print_r($str);
		die;
	}
	function pagination($base_url='',$total='',$per='',$uri='')
	{
		$config = array();
		$config['base_url']    = $base_url;
		$config['total_rows']  = $total;
		$config['per_page']    = $per;
		$config['uri_segment'] = $uri;
		$config['full_tag_open']   = "<nav aria-label='Page navigation' class='text-right'><ul class='pagination'>";
		$config['full_tag_close'] = "</ul></nav>";
		$config['cur_tag_open']   = "<li class='active'><a href='#'>";
		$config['cur_tag_close'] = "</a></li>";
		$config['next_link']   = "&raquo;";
		$config['next_tag_open']   = "<li><span aria-hidden='true'>";
		$config['next_tag_close'] = "</span></li>";
		$config['prev_link']   = "&laquo;";
		$config['prev_tag_open']   = "<li><span aria-hidden='true'>";
		$config['prev_tag_close'] = "</span></li>";
		$config['num_tag_open']   = "<li>";
		$config['num_tag_close'] = "</li>";
		return $config;
	}
	function covert_vi_to_en($str)
	{
	   if(!$str) return false;
	    $unicode = array(
 
			'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
			 
			'd'=>'đ',
			 
			'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
			 
			'i'=>'í|ì|ỉ|ĩ|ị',
			 
			'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
			 
			'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
			 
			'y'=>'ý|ỳ|ỷ|ỹ|ỵ',
			 
			'A'=>'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
			 
			'D'=>'Đ',
			 
			'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
			 
			'I'=>'Í|Ì|Ỉ|Ĩ|Ị',
			 
			'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
			 
			'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
			 
			'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
			 
			);
		foreach($unicode as $nonUnicode=>$uni) $str = preg_replace("/($uni)/i",$nonUnicode,$str);
		$str = trim($str);
		$str = str_replace(' ', '-', $str);
		$str = str_replace(',', '', $str);
		return $str;
	}

	// Title-case product name for storefront display (UTF-8).
	function product_display_name($str)
	{
		$str = trim((string) $str);
		if ($str === '') {
			return '';
		}
		$parts = preg_split('/(\s+)/u', $str, -1, PREG_SPLIT_DELIM_CAPTURE);
		$out = '';
		foreach ($parts as $part) {
			if ($part === '' || preg_match('/^\s+$/u', $part)) {
				$out .= $part;
				continue;
			}
			$first = mb_strtoupper(mb_substr($part, 0, 1, 'UTF-8'), 'UTF-8');
			$rest = mb_strlen($part, 'UTF-8') > 1
				? mb_strtolower(mb_substr($part, 1, null, 'UTF-8'), 'UTF-8')
				: '';
			$out .= $first . $rest;
		}
		return $out;
	}
?>