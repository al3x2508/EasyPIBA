<?php

namespace Controller;

use Model\Model;
use Model\ModelException;

abstract class AdminAct
{
    public $response;
    public string $permission;
    public Model $entity;
    public array $fields = array();
    public array $ignoredFields = array();

    public function __construct($id = false)
    {
        try {
            $this->checkPermission();
        } catch (AccessDeniedException $e) {
            self::throwError(401, $e->getMessage());
        }
        switch (strtolower($_SERVER['REQUEST_METHOD'])) {
            case 'post':
                foreach ($_POST as $key => $value) {
                    if (!in_array($key, $this->ignoredFields)) {
                        $this->fields[$key] = $value;
                    }
                }
                break;
            case 'patch':
                parse_str(file_get_contents('php://input'), $_PATCH);
                foreach ($_PATCH as $key => $value) {
                    if (!in_array($key, $this->ignoredFields)) {
                        $this->fields[$key] = $value;
                    }
                }
                $this->fields['id'] = $id;
                break;
            case 'delete':
                $this->fields['delete'] = $id;
                break;
            default:
                break;
        }
    }

    public function response()
    {
        return $this->response;
    }

    /**
     * @throws AccessDeniedException
     */
    public function checkPermission(): bool
    {
        $adminController = new AdminController();
        if (!$adminController->checkPermission($this->permission)) {
            throw new AccessDeniedException("You don't have permission to create or update this entity");
        }
        return true;
    }

    /**
     * @throws ModelException
     */
    public function create()
    {
        foreach ($this->fields as $key => $value) {
            $this->entity->$key = $value;
        }
        return $this->entity->create();
    }

    /**
     * @throws EntityException
     */
    public function update()
    {
        if ($this->fields['id'] > 0) {
            foreach ($this->fields as $key => $value) {
                $this->entity->$key = $value;
            }
            return $this->entity->update();
        }
        throw new EntityException("No where or ID set. Fields are: ["
            .json_encode($this->fields)."]");
    }

    /**
     * @throws EntityException
     */
    public function delete()
    {
        if (!$this->fields['delete']) {
            throw new EntityException("No delete value set. Fields are: ["
                .json_encode($this->fields)."]");
        }
        $this->entity->id = $this->fields['delete'];
        return $this->entity->delete();
    }

    public static function throwError($errorCode, $errorMessage)
    {
        switch ($errorCode) {
            case 400:
                header('HTTP/1.1 400 BAD REQUEST');
                break;
            case 401:
                header('HTTP/1.1 401 Unauthorized');
                break;
            default:
                break;
        }
        echo json_encode(['error' => $errorMessage]);
    }
}
