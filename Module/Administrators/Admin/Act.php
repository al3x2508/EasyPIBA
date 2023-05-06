<?php

namespace Module\Administrators\Admin;

use Controller\AccessDeniedException;
use Controller\AdminAct;
use Controller\EntityException;
use Exception;
use Model\Model;
use Module\Users\Admin\Act as UsersAct;
use Utils\Bcrypt;

class Act extends AdminAct
{
    public array $ignoredFields = array('permission');

    /**
     * @throws AccessDeniedException
     * @throws EntityException
     * @throws Exception
     */
    public function __construct($id = false)
    {
        $this->permission = 'Edit administrators';
        $this->entity = new Model('admins');
        $bcrypt = new Bcrypt(10);

        switch (strtolower($_SERVER['REQUEST_METHOD'])) {
            //Create administrator
            case 'post':
                UsersAct::checkValidPassword($_POST['password'] ?? '');
                parent::__construct($id);
                $this->fields['password'] = $bcrypt->hash($_POST['password']);
                try {
                    $admin = $this->create();
                } catch (Exception $e) {
                    self::throwError(
                        400,
                        $e->getMessage() ?? 'Error creating the entity'
                    );
                }
                if (is_a($admin, 'Model\Model')) {
                    $this->addPermissions($admin, $_POST['permission']);
                    $this->response = $admin;
                } else {
                    self::throwError(
                        400,
                        $admin['error'] ?? 'Error creating the entity'
                    );
                }
                break;
            //Update administrator
            case 'patch':
                parse_str(file_get_contents('php://input'), $_PATCH);
                parent::__construct($id);
                if (!empty($_PATCH['password'])) {
                    $this->fields['password']
                        = $bcrypt->hash($_PATCH['password']);
                } else {
                    unset($this->fields['password']);
                }
                try {
                    $admin = $this->update();
                    if (is_a($admin, 'Model\Model')) {
                        $this->clearPermissions($admin);
                        $this->addPermissions($admin, $_PATCH['permission']);
                        $this->response = $admin;
                    } else {
                        self::throwError(
                            400,
                            $admin['error'] ?? 'Error updating the entity'
                        );
                    }
                } catch (EntityException $e) {
                    self::throwError(400, $e->getMessage());
                }
                break;
            //Delete administrator
            case 'delete':
                parent::__construct($id);
                try {
                    $this->response = $this->delete();
                } catch (EntityException $e) {
                    self::throwError(400, $e->getMessage());
                }
                break;
            default:
                break;
        }
    }

    private function clearPermissions($admin)
    {
        $permissions = new Model('admins_permissions');
        $permissions->admin = $admin->id;
        $permissions->delete();
    }

    private function addPermissions($admin, $permissionsArray)
    {
        $permissions = new Model('admins_permissions');
        foreach ($permissionsArray as $permissionId => $hasAccess) {
            if ($hasAccess) {
                $permissions->admin = $admin->id;
                $permissions->permission = $permissionId;
                $permissions->create();
            }
        }
    }
}
