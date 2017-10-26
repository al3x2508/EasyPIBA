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
			if($admin->access == -1) return true;
			$permissions = new Model('permissions');
			$permissions->name = $perm;
			$permissions->where(array('EXISTS(SELECT * FROM admins WHERE id = ' . $admin->id . ' AND access REGEXP CONCAT(\'^\\\[([^\\,]+(\\\,))*\', permissions.id, \'((\\\,)[^\\,]+)*\\\]$\'))' => 'complexW'));
			$permissions = $permissions->get();
			return count($permissions)?true:false;
		}
		return false;
	}
	public static function getPermissions() {
		$admin = self::getCurrentUser();
		$perms = array();
		if($admin) {
			$access = $admin->access;
			$perms = array();
			$permissions = array();
			$permissionsEntity = new Model('permissions');
			$permissionsEntity = $permissionsEntity->get();
			foreach($permissionsEntity AS $permission) $permissions[$permission->id] = $permission->name;
			if($access == '-1') $perms = array_merge(array_values($permissions), array('Edit administrators'));
			else {
				$access = json_decode($access, true);
				foreach($access AS $acc) $perms[$acc] = $permissions[$acc];
			}
		}
		return $perms;
	}
}