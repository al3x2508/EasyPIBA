<?php
namespace Act;
use Controller\AdminController;
use Model\Model;

/**
 * Class act
 * @package Act
 */
abstract class act {
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
	 * @return bool|int|mixed
	 */
	public function act($where = false) {
		$adminController = new AdminController();
		if($adminController->checkPermission($this->permission)) {
			if($where || array_key_exists('id', $this->fields)) {
				foreach($this->fields AS $key => $value) $this->entity->$key = $value;
				if($where || (array_key_exists('id', $this->fields) && $this->fields['id'] > 0)) {
					$this->entity->update($where);
					return array_key_exists('id', $this->fields)?$this->fields['id']:1;
				}
				else return $this->entity->create();
			}
			elseif(array_key_exists('delete', $this->fields)) {
				$this->entity->id = $this->fields['delete'];
				$this->entity->delete();
				return true;
			}
		}
		return false;
	}
}