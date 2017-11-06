<?php
namespace Module\Administrators\Admin;
use Controller\AdminAct;
use Model\Model;
use Utils\Bcrypt;

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/Utils/functions.php');

class Act extends AdminAct {
	public function __construct() {
		$this->permission = 'Edit administrators';
		$this->entity = new Model('admins');
		$bcrypt = new Bcrypt(10);
		$permissions = new Model('admins_permissions');
		foreach($_POST AS $key => $value) {
			if(!in_array($key, array('password', 'permission'))) $this->fields[$key] = $value;
			elseif($key == 'password' && strlen($value) > 5) $this->fields['password'] = $bcrypt->hash($_REQUEST['password']);
			if(array_key_exists('id', $_POST) && ($_POST['id'] > 0)) {
				$permissions->admin = $_POST['id'];
				$permissions->delete();
			}
		}
		if(array_key_exists('id', $_POST)) {
			foreach($_POST['permission'] AS $permissionId => $hasAccess) {
				if($hasAccess) {
					$permissions->admin = $_POST['id'];
					$permissions->permission = $permissionId;
					$permissions->create();
				}
			}
		}
		return $this->act();
	}
}