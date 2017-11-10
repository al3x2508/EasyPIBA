<?php
namespace Module\Administrators\Admin;

class AdminPage extends \Controller\AdminPage {
	public function output($filename) {
		switch($filename) {
			case 'administrators':
				return new Admins();
			case 'reread':
				return new Modules();
			default:
				break;
		}
		return false;
	}
}