<?php
namespace Controller;
use \Model\Model;
use Utils\Bcrypt;

class AdminController {
	public static function getCurrentUser() {
		if(arrayKeyExists('admin', $_SESSION)) {
			$admin = new Model('admins');
			$admin = $admin->getOneResult('id', $_SESSION['admin']);
			return $admin;
		}
		return false;
	}
	public static function checkAuth($user, $pass) {
		try {
			$bcrypt = new Bcrypt(15);
			$admin = new Model('admins');
			$admin = $admin->getOneResult('username', $user);
			if($admin && property_exists($admin, 'id')) {
				if($admin->status == 1) {
					$id = $admin->id;
					$isGood = $bcrypt->verify($pass, $admin->password);
					if($isGood) {
						$_SESSION['admin'] = $id;
						return $id;
					}
					else return array("message" => __('Incorrect password'));
				}
				else return array("message" => __('Username blocked'));
			}
			else return array("message" => __('Username') . " " . $user . " " . __('does not exist'));
		}
		catch (\Exception $e) {
			return array("message" => __('Webserver error'));
		}
	}
	public static function checkPermission($permissionName) {
		$admin = self::getCurrentUser();
		if($admin) {
			$permissions = new Model('admins_permissions');
			$permissions->admin = $admin->id;
			$permissions->where(array('j_permissions.name' => $permissionName));
			$permissions = $permissions->get();
			return count($permissions)?true:false;
		}
		return false;
	}
	public static function registerPermission($permissionName) {
		$permission = new Model('permissions');
		//Check if permission exists
		if($permission->getOneResult('name', $permissionName) === false) {
			//Create permission
			$permission->name = $permissionName;
			$permission = $permission->create();
		}
		return $permission;
	}
}