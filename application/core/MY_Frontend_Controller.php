<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Frontend_Controller extends MY_Controller
{
	const LAYOUT_MAIN = 'site/layout';
	const LAYOUT_SUB = 'site/layoutsub';
	const LAYOUT_RAW = 'site/layouts/raw';

	protected function render_frontend($view, array $data = array(), $layout = self::LAYOUT_MAIN)
	{
		render_frontend_view($view, $data, $layout, $this);
	}

	protected function render_frontend_main($view, array $data = array())
	{
		$this->render_frontend($view, $data, self::LAYOUT_MAIN);
	}

	protected function render_frontend_sub($view, array $data = array())
	{
		$this->render_frontend($view, $data, self::LAYOUT_SUB);
	}

	protected function render_frontend_standalone($view, array $data = array())
	{
		$this->render_frontend($view, $data, self::LAYOUT_RAW);
	}
}
