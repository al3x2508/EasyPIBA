<?php
namespace Module\Administrators;

class Setup extends \Module\Setup {
	public function __construct() {
		parent::__construct();
		$this->registerBackendUrl(array('permission' => 'Edit administrators', 'url' => 'administrators', 'menu_text' => 'Administrators', 'menu_class' => 'users'));
		$this->registerBackendUrl(array('permission' => 'Edit administrators', 'url' => 'cache', 'menu_text' => 'Cache', 'menu_class' => 'folder-o'));
		return true;
	}
}