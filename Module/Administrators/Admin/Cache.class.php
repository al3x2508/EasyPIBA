<?php
namespace Module\Administrators\Admin;
class Cache {
	/**
	 * @param $key
	 * @param $value
	 */
	public function __set($key, $value) {
		$this->$key = $value;
	}
	public function __construct() {
		$this->title = __('Cache');
		$this->h1 = __('Cache');
		$this->js = array();
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