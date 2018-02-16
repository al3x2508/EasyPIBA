<?php
namespace Module\Users\Admin;
use Controller\AdminAct;
use Model\Model;
use Utils\Bcrypt;

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/Utils/functions.php');

class Act extends AdminAct {
	public function __construct() {
		$this->permission = 'Edit users';
		$this->entity = new Model('users');
		if(!arrayKeyExists('delete', $_POST)) {
			$bcrypt = new Bcrypt(10);
			if(arrayKeyExists('password', $_POST) && $_POST['password'] != $_POST['confirmPassword']) return false;
			foreach($_POST AS $key => $value) {
				if(!in_array($key, array('password', 'confirmPassword'))) $this->fields[$key] = $value;
				elseif($key == 'password' && strlen($value) > 5) $this->fields['password'] = $bcrypt->hash($_REQUEST['password']);
			}
			$act = $this->act();
		}
		else {
			foreach($_POST AS $key => $value) $this->fields[$key] = $value;
			$act = $this->act();
		}
		return $act;
	}
}