<?php
namespace Module\Administrators\Admin;
class Modules {
	/**
	 * @param $key
	 * @param $value
	 */
	public function __set($key, $value) {
		$this->$key = $value;
	}
	public function __construct() {
		$this->title = __('Modules');
		$this->h1 = __('Modules');
		$this->js = array('../../Module/Administrators/Admin/modules.js');
		$this->css = array();
		$this->content = '';
		if(array_key_exists('reread', $_REQUEST)) {
			require_once _APP_DIR_ . 'admin/modules.php';
			reread();
			$this->content .= '<div class="alert alert-success" role="alert"><strong>' . __('Modules reread') . '.</strong></div>';
		}
		$this->content .= '<form action="#" method="post"><input type="hidden" name="reread" value="1" /><input type="submit" class="btn btn-primary" value="' . __('Reread modules') . '" /></form>';
	}
}