<?php
namespace Controller;
use Model\Model;

abstract class AdminAct {
	/**
	 * @var string
	 */
	public $permission;
	/**
	 * @var Model
	 */
	public $entity;
	/**
	 * @var array
	 */
	public $fields = array();

	/**
	 * @param bool $where
	 * @return bool|int|Model
	 */
	public function act($where = false) {
		$adminController = new AdminController();
		if($adminController->checkPermission($this->permission)) {
			if($where || arrayKeyExists('id', $this->fields)) {
				foreach($this->fields AS $key => $value) $this->entity->$key = $value;
				if($where || (arrayKeyExists('id', $this->fields) && $this->fields['id'] > 0)) {
					if($this->entity->update($where)) return $this->entity;
					else return false;
				}
				else {
					if($ret = $this->entity->checkFields()) {
						if(is_array($ret)) {
							echo json_encode(array('error' => $ret));
							return false;
						}
					}
					return $this->entity->create();
				}
			}
			elseif(arrayKeyExists('delete', $this->fields)) {
				$this->entity->id = $this->fields['delete'];
				return $this->entity->delete();
			}
		}
		return false;
	}
}