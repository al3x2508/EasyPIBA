<?php

namespace Module\Administrators\Admin;

use Model\Model;
use Module\Administrators\Setup;
use Module\JSON\Admin;

require_once(dirname(__FILE__, 4) . '/Utils/Util.php');

class JSON extends Admin
{
    public static $ENTITY = Setup::ENTITY;
    public static $PERMISSION = Setup::PERMISSION;

    public function get()
    {
        $admins = new Model(self::$ENTITY);
        if (!arrayKeyExists('id', $_REQUEST)) {
            $itemsPerPage = (arrayKeyExists('start', $_REQUEST)) ? $_REQUEST['length'] : 10;
            $limit = ((arrayKeyExists('start', $_REQUEST)) ? $_REQUEST['start'] : 0) . ', ' . $itemsPerPage;
            $countTotal = $admins->countItems();
            if (arrayKeyExists('filters', $_REQUEST)) {
                foreach ($_REQUEST['filters'] as $key => $value) {
                    if (in_array($key, array(
                        'name',
                        'username'
                    ))) {
                        $admins->$key = array('%' . $value . '%', ' LIKE ');
                    } else {
                        $admins->$key = $value;
                    }
                }
            }
            $countFiltered = $admins->countItems();
            $admins->limit($limit);
            $admins = $admins->get('AND', true);
            if (arrayKeyExists('secho', $_REQUEST)) {
                $response = json_encode(
                    array(
                        'sEcho' => $_REQUEST['secho'],
                        'iTotalRecords' => $countTotal,
                        'iTotalDisplayRecords' => $countFiltered,
                        'aaData' => $admins
                    )
                );
            } else {
                $adminsArray = array();
                foreach ($admins as $admin) {
                    $adminsArray[] = ['value' => $admin->id, 'text' => $admin->name];
                }
                $response = json_encode($adminsArray);
            }
            if (arrayKeyExists('callback', $_GET)) {
                $response = $_GET['callback'] . '(' . $response . ')';
            }
            echo $response;
        } else {
            $admin = $admins->getOneResult('id', $_REQUEST['id']);
            unset($admin->password);
            $access = new Model('admins_permissions');
            $access->admin = $_REQUEST['id'];
            $admin->access = $access->get();
            echo json_encode($admin);
        }
    }
}