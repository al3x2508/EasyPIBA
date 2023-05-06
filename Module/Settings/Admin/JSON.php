<?php

namespace Module\Settings\Admin;

use Model\Model;
use Module\JSON\Admin;

require_once(dirname(__FILE__, 4) . '/Utils/functions.php');

class JSON extends Admin
{
    public function __construct()
    {
        $this->permission = "Edit settings";
        parent::__construct();
    }

    public function get()
    {
        $settings = new Model('settings');
        if (!arrayKeyExists('id', $_REQUEST)) {
            $itemsPerPage = (arrayKeyExists('start', $_REQUEST)) ? $_REQUEST['length'] : 10;
            $limit = ((arrayKeyExists('start', $_REQUEST)) ? $_REQUEST['start'] : 0) . ', ' . $itemsPerPage;
            $countTotal = $settings->countItems();
            if (arrayKeyExists('filters', $_REQUEST)) {
                foreach ($_REQUEST['filters'] as $key => $value) {
                    if ($key == 'setting') {
                        $settings->$key = array('%' . $value . '%', ' LIKE ');
                    } else {
                        $settings->$key = $value;
                    }
                }
            }
            $countFiltered = $settings->countItems();
            $settings->limit($limit);
            $settings = $settings->get();
            if (arrayKeyExists('secho', $_REQUEST)) {
                $response = json_encode(array(
                    'sEcho' => $_REQUEST['secho'],
                    'iTotalRecords' => $countTotal,
                    'iTotalDisplayRecords' => $countFiltered,
                    'aaData' => $settings
                ));
            } else {
                $settingsArray = array();
                foreach ($settings as $setting) {
                    $settingsArray[$setting->setting] = $setting->value;
                }
                unset($settings->schema);
                $response = json_encode($settings);
            }
            if (arrayKeyExists('callback', $_GET)) {
                $response = $_GET['callback'] . '(' . $response . ')';
            }
            echo $response;
        } else {
            $setting = $settings->getOneResult('setting', $_REQUEST['id']);
            echo json_encode($setting);
        }
    }
}