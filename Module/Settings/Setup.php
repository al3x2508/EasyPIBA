<?php
namespace Module\Settings;

use Controller\AdminController;

class Setup extends \Module\Setup {
	public function __construct() {
		parent::__construct();
        $permissionName = 'Edit settings';
        $permission = AdminController::registerPermission($permissionName);
		$this->registerBackendUrl(array('permission' => $permissionName, 'url' => 'settings', 'menu_text' => 'Settings', 'menu_class' => 'fal fa-gears'));
		return true;
	}
}