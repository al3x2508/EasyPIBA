<?php

namespace Controller;

use Model\Model;
use Module\Users\Controller as UserController;

abstract class AdminAct
{
    /**
     * @var string
     */
    public static $PERMISSION;
    /**
     * @var string
     */
    public static $ENTITY;
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

    public function __construct($id = false)
    {
        $this->entity = new Model(static::$ENTITY);
        $act = false;
        if ($this->hasAccess()) {
            if ($id) {
                $this->fields['id'] = $id;
            }
            if (strtolower($_SERVER['REQUEST_METHOD']) == 'delete') {
                if ($id) {
                    $this->delete();
                } else {
                    $this->sendStatus(false, __('No ID set'));
                }
            } else {
                $method = 'update';
                if (strtolower($_SERVER['REQUEST_METHOD']) == 'patch') {
                    if ($id) {
                        parse_str(file_get_contents('php://input'), $_PATCH);
                    } else {
                        $this->sendStatus(false, __('No ID set'));
                    }
                } else {
                    $method = 'create';
                }
                foreach ($method == 'update' ? $_PATCH : $_POST as $key => $value) {
                    $this->fields[$key] = $value;
                }
                $act = call_user_func_array(array($this, $method), array());
            }
        }
        $this->sendStatus($act);
        return $act;
    }

    public function hasAccess(): bool
    {
        $user = UserController::getCurrentUser();
        $adminController = new AdminController();
        $loggedIn = ($user || $adminController::getCurrentUser());
        if ($loggedIn) {
            if ($adminController::getCurrentUser() && !$adminController->checkPermission(static::$PERMISSION)) {
                return false;
            }
            return true;
        }
        return false;
    }

    public function create()
    {
        foreach ($this->fields as $key => $value) {
            $this->entity->$key = $value;
        }
        $this->pre_create_hook();
        $entity = $this->entity->create();
        $this->post_create_hook();
        if ($entity) {
            $this->sendStatus($entity);
        } else {
            $this->sendStatus($entity, __('Error creating the entity'));
        }
    }

    public function update()
    {
        if ($this->fields['id']) {
            foreach ($this->fields as $key => $value) {
                $this->entity->$key = $value;
            }
            $this->pre_update_hook();
            $entity = $this->entity->update();
            $this->post_update_hook();
            if ($entity) {
                $this->sendStatus($entity);
            } else {
                $this->sendStatus($entity, __('Error updating the entity'));
            }
        }
        $this->sendStatus(false, __('No ID set'));
    }

    public function delete()
    {
        if ($this->fields['id']) {
            $this->entity->id = $this->fields['id'];
            $this->pre_delete_hook();
            $deleted = $this->entity->delete();
            $this->post_delete_hook();
            if ($deleted) {
                $this->sendStatus(true);
            } else {
                $this->sendStatus(false, __('Error deleting the entity'));
            }
        }
        $this->sendStatus(false, __('No ID set'));
    }

    public function pre_create_hook()
    {
    }

    public function post_create_hook()
    {
    }

    public function pre_update_hook()
    {
    }

    public function post_update_hook()
    {
    }

    public function pre_delete_hook()
    {
    }

    public function post_delete_hook()
    {
    }

    public function sendStatus($result, $message = false)
    {
        if ($result instanceof Model || $result === true) { // model for create page, true for save page
            $responseData = ['code' => 200, 'message' => 'ok', 'entity' => $result];
        } elseif (is_array($result) && arrayKeyExists('error', $result)) {
            $responseData = ['code' => 500, 'error' => $result['error']];
        } else {
            $responseData = ['code' => 500, 'message' => 'error'];
        }
        if ($message) {
            $responseData['message'] = $message;
        }

        http_response_code($responseData['code']);
        header('Content-Type: application/json');
        echo json_encode($responseData);
        exit;
    }
}