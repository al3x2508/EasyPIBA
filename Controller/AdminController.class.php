<?php
namespace Controller;
use \Model\Model;
use Utils\Bcrypt;

class AdminController {
	public static function getCurrentUser() {
		if(array_key_exists('admin', $_SESSION)) {
			$admin = new Model('admins');
			return $admin->getOneResult('id', $_SESSION['admin']);
		}
		return false;
	}
	public static function checkAuth($user, $pass) {
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
		else return array("message" => __('Username') . " " . $user . " " . _('does not exist'));
	}
	public static function checkPermission($perm) {
		$admin = self::getCurrentUser();
		if($admin) {
			$permissions = new Model('admins_permissions');
			$permissions->admin = $admin->id;
			$permissions->where(array('j_permissions.name' => $perm));
			$permissions = $permissions->get();
			return count($permissions)?true:false;
		}
		return false;
	}
}