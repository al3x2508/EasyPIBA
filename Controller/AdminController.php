<?php

namespace Controller;

use Exception;
use Model\Model;
use Bcrypt;

class AdminController
{
    /**
     * @param $user string
     * @param $pass string
     * @return array|int|string[]
     */
    public static function checkAuth(string $user, string $pass)
    {
        try {
            $bcrypt = new Bcrypt(15);
            $admin = new Model('admins');
            $admin = $admin->getOneResult('username', $user);
            if ($admin && property_exists($admin, 'id')) {
                if ($admin->status == 1) {
                    /*
                     * @var $id int
                     */
                    $id = $admin->id;
                    $isGood = $bcrypt->verify($pass, $admin->password);
                    if ($isGood) {
                        $_SESSION['admin'] = $id;
                        return $id;
                    } else {
                        return array("message" => __('Incorrect password'));
                    }
                } else {
                    return array("message" => __('Username blocked'));
                }
            } else {
                return array("message" => __('Username') . " " . $user . " " . __('does not exist'));
            }
        } catch (Exception $e) {
            return array("message" => __('Webserver error'));
        }
    }

    public static function checkPermission($permissionName, $branchId = 0): bool
    {
        $admin = self::getCurrentUser();
        if ($admin) {
            $permissions = new Model('admins_permissions');
            $permissions->admin = $admin->id;
            if ($branchId) {
                $permissions->admins->branch = $branchId;
            }
            $permissions->where(['j_permissions.name' => $permissionName]);
            $permissions = $permissions->get();
            return (bool)count($permissions);
        }
        return false;
    }

    public static function getCurrentUser()
    {
        if (arrayKeyExists('admin', $_SESSION)) {
            $admin = new Model('admins');
            return $admin->getOneResult('id', $_SESSION['admin']);
        }
        return false;
    }

    /**
     * @param string $department Department name
     * @param int $branchId Branch ID
     * @return bool
     */
    public static function isInDepartment(string $department, int $branchId = 0): bool
    {
        $admin = self::getCurrentUser();
        if ($admin) {
            if ($branchId && $admin->branch != $branchId) {
                return false;
            }
            return $admin->departments->name == $department;
        }
        return false;
    }

    public static function registerPermission($permissionName, $global = 1)
    {
        $permission = new Model('permissions');
        //Check if permission exists
        if ($permission->getOneResult('name', $permissionName) === false) {
            //Create permission
            $permission->name = $permissionName;
            $permission->global = $global;
            $permission = $permission->create();
        }
        return $permission;
    }
}