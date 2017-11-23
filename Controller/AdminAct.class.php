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
			if($where || array_key_exists('id', $this->fields)) {
				foreach($this->fields AS $key => $value) $this->entity->$key = $value;
				if($where || (array_key_exists('id', $this->fields) && $this->fields['id'] > 0)) {
					if($this->entity->update($where)) return $this->entity;
					else return false;
				}
				else return $this->entity->create();
			}
			elseif(array_key_exists('delete', $this->fields)) {
				$this->entity->id = $this->fields['delete'];
				return $this->entity->delete();
			}
		}
		return false;
	}
}