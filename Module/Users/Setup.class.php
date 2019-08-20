<?php
namespace Module\Users;

class Setup extends \Module\Setup {
	public function __construct() {
		parent::__construct();
		$this->registerFrontendUrl(array('url' => 'login', 'type' => 0, 'mustBeLoggedIn' => 0, 'hiddenForLoggedIn' => 1, 'menu_position' => 2, 'menu_text' => 'Login / Register'));
		$this->registerFrontendUrl(array('url' => 'email_confirm', 'type' => 0, 'menu_position' => 0));
		$this->registerFrontendUrl(array('url' => 'password_reset', 'type' => 0, 'menu_position' => 0));
		$this->registerFrontendUrl(array('url' => 'my-account', 'type' => 0, 'mustBeLoggedIn' => 1, 'menu_position' => 2, 'menu_text' => 'My account', 'submenu_text' => 'My account', 'menu_parent' => '', 'menu_order' => 1));
		$this->registerFrontendUrl(array('url' => 'logout', 'type' => 0, 'mustBeLoggedIn' => 1, 'menu_position' => 2, 'menu_text' => 'Logout', 'submenu_text' => 'Logout', 'menu_parent' => 'my-account', 'menu_order' => 1));
		$this->registerBackendUrl(array('permission' => 'View users', 'url' => 'users', 'menu_text' => 'Users', 'menu_class' => 'fas fa-users'));
	}
}