<?php
namespace Module\Users\Admin;
use Controller\AdminAct;
use Model\Model;
use Module\Users\Controller;
use Utils\Bcrypt;
use Utils\Util;

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/Utils/functions.php');

class Act extends AdminAct {
	public function __construct($id) {
		$this->permission = 'Edit users';
		$this->entity = new Model('users');
		$act = false;
		if ($this->hasAccess()) {
			if ($id) $this->fields['id'] = $id;
			if (strtolower($_SERVER['REQUEST_METHOD']) == 'delete') {
				if ($id) $act = Controller::deleteUser($id);
				else $this->sendStatus(false, __('No ID set'));
			}
			else {
				try {
					$bcrypt = new Bcrypt(10);
					$method = 'patch';
					if (strtolower($_SERVER['REQUEST_METHOD']) == 'patch') {
						if ($id) parse_str(file_get_contents('php://input'), $_PATCH);
						else $this->sendStatus(false, __('No ID set'));
					}
					else $method = 'create';
					$fields = $method == 'patch' ? $_PATCH : $_POST;
					if (arrayKeyExists('password', $fields) && $fields['password'] != $fields['confirmPassword']) $this->sendStatus(false, __('Wrong password confirm'));
					foreach ($fields AS $key => $value) {
						if (!in_array($key, array('password', 'confirmPassword'))) $this->fields[$key] = $value;
						elseif ($key == 'password' && strlen($value) > 5) $this->fields['password'] = $bcrypt->hash($value);
					}
					$act = call_user_func_array(array($this, $method), array($fields['password']));
				}
				catch (\Exception $e) {
					$this->sendStatus(false, $e->getMessage());
				}
			}
		}
		$this->sendStatus($act);
	}
}