<?php
namespace Module\Pages\Admin;
use Controller\AdminController;
use Module\Media\Admin\Media;

class AdminPage extends \Controller\AdminPage {
	public function __construct() {
		$this->permission = 'Edit pages';
		$adminController = new AdminController();
		$this->hasAccess = $adminController->checkPermission($this->permission);
		return $this;
	}
	public function getMenu($returnPermissions, $currentLink = '') {
		if($this->hasAccess) {
			if($returnPermissions) return array('pages', 'menu', 'media');
			else return self::createLink(array('href' => 'pages', 'text' => __('Pages'), 'class' => 'files-o', 'submenu' => array(array('href' => 'menu', 'text' => __('Menu'), 'class' => 'bars'), array('href' => 'media', 'text' => __('Media'), 'class' => 'picture-o'))), $currentLink);
		}
		return false;
	}
	public function output($filename) {
		switch($filename) {
			case 'pages':
				return new Pages();
			case 'menu':
				return new Menu();
			case 'media':
				return new Media();
			default:
				break;
		}
		return false;
	}
}