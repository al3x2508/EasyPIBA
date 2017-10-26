<?php
namespace Act;

use Model\Model;
use Utils\Bcrypt;

require_once(dirname(dirname(dirname(__FILE__))) . '/Utils/functions.php');
require_once(dirname(__FILE__) . '/act.class.php');

class Users extends act {
	public function __construct() {
		$this->permission = 'Edit users';
		$this->entity = new Model('users');
		if(!array_key_exists('delete', $_POST)) {
			$bcrypt = new Bcrypt(10);
			if(array_key_exists('password', $_POST) && $_POST['password'] != $_POST['confirmPassword']) return false;
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

new Users();