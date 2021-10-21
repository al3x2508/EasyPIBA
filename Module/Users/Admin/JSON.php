<?php

namespace Module\Users\Admin;

use Model\Model;
use Module\JSON\Admin;
use Module\Users\Setup;

require_once(dirname(__FILE__, 4) . '/Utils/Util.php');

class JSON extends Admin
{
    public static $ENTITY = Setup::ENTITY;
    public static $PERMISSION = Setup::PERMISSION;

    public function __construct()
    {
        $this->columnsMap = array(
            'id' => 'ID',
            'firstname' => __('Firstname'),
            'lastname' => __('Lastname'),
            'email' => __('Email'),
            'phone' => __('Phone'),
            'address' => __('Address'),
            'city' => __('City'),
            'state' => __('State'),
            'countries.name' => __('Country'),
            'status' => __('Status ID')
        );
        $this->instanceName = __('Users');
        parent::__construct();
    }

    public function get()
    {
        if (!arrayKeyExists('id', $_REQUEST)) {
            $itemsPerPage = (arrayKeyExists('start', $_REQUEST)) ? $_REQUEST['length'] : 10;
            $limit = ((arrayKeyExists('start', $_REQUEST)) ? $_REQUEST['start'] : 0) . ', ' . $itemsPerPage;
            $users = new Model('users');
            if (!arrayKeyExists('cRecords', $_REQUEST)) {
                $this->countTotal = $users->countItems();
            }
            if (arrayKeyExists('filters', $_REQUEST)) {
                $where = array();
                foreach ($_REQUEST['filters'] as $key => $value) {
                    if (in_array($key, array(
                        'firstname',
                        'lastname',
                        'email'
                    ))) {
                        $users->$key = array('%' . $value . '%', ' LIKE ');
                    } elseif ($key == 'name') {
                        $where['CONCAT(firstname, " ", lastname)'] = array(
                            '%' . $value . '%',
                            'LIKE',
                            's'
                        );
                    } else {
                        $users->$key = $value;
                    }
                }
                if (count($where)) {
                    $users->where($where);
                }
            }
            if (!arrayKeyExists('cRecords', $_REQUEST)) {
                $this->countFiltered = $users->countItems();
            } else {
                $users->addCustomField('SUM(IF(status = 1, 1, 0)) AS confirmed');
                $users->addCustomField('COUNT(*) AS totalRecords');
                $ops = $users->get('AND', true);
                $this->data = $ops;
                parent::output();
                exit;
            }
            if (!arrayKeyExists('export', $_REQUEST)) {
                $users->limit($limit);
            }
            $ops = $users->get('AND', true);
            $this->data = $ops;
            $users = array();
            if (!arrayKeyExists('secho', $_REQUEST)) {
                foreach ($ops as $user) {
                    $users['#' . $user->id] = $user->firstname . ' ' . $user->lastname;
                }
                $this->data = $users;
            }
            parent::output();
        } else {
            $user = new Model('users');
            $user = $user->getOneResult('id', $_REQUEST['id']);
            unset($user->password);
            echo json_encode($user);
        }
    }
}