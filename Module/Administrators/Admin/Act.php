<?php

namespace Module\Administrators\Admin;

use Controller\AdminAct;
use Model\Model;
use Module\Administrators\Setup;
use Bcrypt;

require_once(dirname(__FILE__, 4) . '/Utils/Util.php');

class Act extends AdminAct
{
    public static $PERMISSION = Setup::PERMISSION;
    public static $ENTITY = Setup::ENTITY;
    private $bcrypt;
    private $permissions;

    public function __construct($id)
    {
        try {
            $this->bcrypt = new Bcrypt(10);
        } catch (\Exception $e) {
            $this->sendStatus(false);
        }
        $this->permissions = new Model('admins_permissions');
        parent::__construct($id);
    }

    public function pre_create_hook()
    {
        unset($this->entity->permission);
        if (!property_exists($this->entity, 'password') || strlen($this->entity->password) > 5) {
            $this->entity->password = $this->bcrypt->hash($this->entity->password);
        } else {
            $this->sendStatus(['error' => ['password' => 'length']]);
        }
    }

    public function post_create_hook()
    {
        if (arrayKeyExists('permission', $this->fields)) {
            $this->create_permissions();
        }
    }

    public function pre_update_hook()
    {
        unset($this->entity->permission);
        if (property_exists($this->entity, 'password')) {
            if (strlen($this->entity->password) > 0) {
                if (strlen($this->entity->password) > 5) {
                    $this->entity->password = $this->bcrypt->hash($this->entity->password);
                } else {
                    $this->sendStatus(['error' => ['password' => 'length']]);
                }
            } else {
                unset($this->entity->password);
            }
        }
    }

    public function post_update_hook()
    {
        $this->permissions->admin = $this->entity->id;
        $this->permissions->delete();

        if (arrayKeyExists('permission', $this->fields)) {
            $this->create_permissions();
        }
    }

    private function create_permissions()
    {
        foreach ($this->fields['permission'] as $permissionId => $hasAccess) {
            if ($hasAccess) {
                $this->permissions->admin = $this->entity->id;
                $this->permissions->permission = $permissionId;
                $this->permissions->create();
            }
        }
    }
}