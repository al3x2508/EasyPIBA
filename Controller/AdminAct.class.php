<?php

namespace Controller;

use Model\Model;
use Module\Users\Controller AS UserController;

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
	 * @return bool|int|Model
	 */
	public function hasAccess() {
		$loggedIn = false;
		$user = UserController::getCurrentUser();
		$adminController = new AdminController();
		if ($user) $loggedIn = true;
		else {
			if ($adminController::getCurrentUser()) $loggedIn = true;
		}
		if ($loggedIn) {
			if ($adminController::getCurrentUser() && !$adminController->checkPermission($this->permission)) return false;
			return true;
		}
		return false;
	}

	public function create() {
		foreach ($this->fields AS $key => $value) $this->entity->$key = $value;
		$entity = $this->entity->create();
		if ($entity) $this->sendStatus($entity);
		else $this->sendStatus($entity, __('Error creating the entity'));
	}

	public function patch() {
		if ($this->fields['id']) {
			foreach ($this->fields AS $key => $value) $this->entity->$key = $value;
			$entity = $this->entity->update();
			if ($entity) $this->sendStatus($entity);
			else $this->sendStatus($entity, __('Error updating the entity'));
		}
		$this->sendStatus(false, __('No ID set'));
	}

	public function delete() {
		if ($this->fields['id']) {
			$this->entity->id = $this->fields['id'];
			$deleted = $this->entity->delete();
			if ($deleted) $this->sendStatus(true);
			else $this->sendStatus(false, __('Error deleting the entity'));
		}
		$this->sendStatus(false, __('No ID set'));
	}

	public function sendStatus($result, $message = false) {
		if ($result instanceof Model || $result === true) { // model for create page, true for save page
			$responseData = ['code' => 200, 'message' => 'ok', 'entity' => $result];
		}
		elseif (is_array($result) && arrayKeyExists('error', $result)) {
			$responseData = ['code' => 500, 'error' => $result['error']];
		}
		else {
			$responseData = ['code' => 500, 'message' => 'error'];
		}
		if($message) $responseData['message'] = $message;

		http_response_code($responseData['code']);
		header('Content-Type: application/json');
		echo json_encode($responseData);
		exit;
	}
}