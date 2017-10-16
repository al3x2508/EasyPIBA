<?php
namespace Controller;
use \Model\Model;
class AdminController {
	public static function getCurrentUser() {
		if(array_key_exists('admin', $_SESSION)) {
			$admin = new Model('admin');
			return $admin->getOneResult('id', $_SESSION['admin']);
		}
		return false;
	}
	public static function checkAuth($user, $pass) {
		$bcrypt = new \Utils\Bcrypt(15);
		$admin = new Model('admin');
		$admin = $admin->getOneResult('user', $user);
		if($admin && property_exists($admin, 'id')) {
			if($admin->stare_admin == 1) {
				$id = $admin->id;
				$isGood = $bcrypt->verify($pass, $admin->parola);
				if($isGood) {
					\setcookie('CHTID', $id, time() + 60 * 60 * 24 * 30);
					$_SESSION['admin'] = $id;
					return $id;
				}
				else {
					$mesaj = "Parola gresita";
					return array("mesaj" => $mesaj);
				}
			}
			else {
				$mesaj = "Utilizator blocat";
				return array("mesaj" => $mesaj);
			}
		}
		else {
			$mesaj = "Numele de utilizator {$user} nu exista";
			return array("mesaj" => $mesaj);
		}
	}
	public static function checkPermission($perm) {
		$admin = self::getCurrentUser();
		if($admin) {
			if($admin->acces == -1) return true;
			$permisiuni = new Model('permisiuni');
			$permisiuni->nume = $perm;
			$permisiuni->where(array('EXISTS(SELECT * FROM {$this->tableName} WHERE id = ? AND acces REGEXP CONCAT(\'^\\\[([^\\,]+(\\\,))*\', permisiuni.id, \'((\\\,)[^\\,]+)*\\\]$\'))'));
			$permisiuni = $permisiuni->get();
			return count($permisiuni)?true:false;
		}
		return false;
	}
	public static function getPermisiuni() {
		$admin = self::getCurrentUser();
		$perms = array();
		if($admin) {
			$acces = $admin->acces;
			$perms = array();
			$permisiuni = array();
			$permisiuniEnt = new Model('permisiuni');
			$permisiuniEnt = $permisiuniEnt->get();
			foreach($permisiuniEnt AS $permisiune) $permisiuni[$permisiune['id']] = $permisiune['nume'];
			if($acces == '-1') $perms = array_merge(array_values($permisiuni), array('Editeaza administratori'));
			else {
				$acces = json_decode($acces, true);
				foreach($acces AS $acc) $perms[$acc] = $permisiuni[$acc];
			}
		}
		return $perms;
	}
}