<?php
namespace Module\Administrators\Admin;
use Controller\AdminAct;
use Model\Model;
use Utils\Bcrypt;

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/Utils/functions.php');

class Act extends AdminAct {
    public function __construct($id) {
        $this->permission = 'Edit administrators';
        $this->entity = new Model('admins');
        $act = false;
        if ($this->hasAccess()) {
            if ($id) $this->fields['id'] = $id;
            if (strtolower($_SERVER['REQUEST_METHOD']) == 'delete') {
                $this->delete();
            }
            else {
                $method = 'patch';
                if (strtolower($_SERVER['REQUEST_METHOD']) == 'patch') {
                    if ($id) parse_str(file_get_contents('php://input'), $_PATCH);
                    else $this->sendStatus(false, __('No ID set'));
                }
                else $method = 'create';
                $permissions = new Model('admins_permissions');
                try {
                    $bcrypt = new Bcrypt(10);
                }
                catch (\Exception $e) {
                }
                foreach ($method == 'patch'?$_PATCH:$_POST AS $key => $value) {
                    if (!in_array($key, array('password', 'permission'))) $this->fields[$key] = $value;
                    elseif ($key == 'password' && strlen($value) > 5) $this->fields['password'] = $bcrypt->hash($_REQUEST['password']);
                }
                if($method == 'patch') {
                    $permissions->admin = $id;
                    $permissions->delete();
                }
                $act = call_user_func_array(array($this, $method), array());
                foreach ($method == 'patch'?$_PATCH:$_POST AS $key => $value) {
                    if($key == 'permission') {
                        foreach ($value AS $permissionId => $hasAccess) {
                            if ($hasAccess) {
                                $permissions->admin = $act->id;
                                $permissions->permission = $permissionId;
                                $permissions->create();
                            }
                        }
                        break;
                    }
                }
            }
        }
        $this->sendStatus($act);
    }

    public function create() {
        foreach ($this->fields AS $key => $value) $this->entity->$key = $value;
        $entity = $this->entity->create();
        return $entity;
    }

    public function patch() {
        foreach ($this->fields AS $key => $value) $this->entity->$key = $value;
        $entity = $this->entity->update();
        return $entity;
    }

}