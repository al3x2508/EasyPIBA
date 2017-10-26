<?php
namespace Act;

use Model\Model;
use Utils\Bcrypt;

require_once(dirname(dirname(dirname(__FILE__))) . '/Utils/functions.php');
require_once(dirname(__FILE__) . '/act.class.php');

class Admin extends act {
	public function __construct() {
		$bcrypt = new Bcrypt(10);
		$this->permission = 'Edit administrators';
		$this->entity = new Model('admins');
		foreach($_POST AS $key => $value) {
			if(!in_array($key, array('password', 'permission'))) $this->fields[$key] = $value;
			elseif($key == 'password' && strlen($value) > 5) $this->fields['password'] = $bcrypt->hash($_REQUEST['password']);
		}
		$this->fields['access'] = json_encode(array_keys($_POST['permission'], 1));
		$act = $this->act();
		return $act;
	}
}

new Admin();