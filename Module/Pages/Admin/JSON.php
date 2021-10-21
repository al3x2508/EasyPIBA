<?php

namespace Module\Pages\Admin;

use Model\Model;
use Module\JSON\Admin;
use Module\Pages\Setup;

require_once(dirname(__FILE__, 4) . '/Utils/Util.php');

class JSON extends Admin
{
    public static $ENTITY = Setup::ENTITY;
    public static $PERMISSION = Setup::PERMISSION;

    public function get()
    {
        if (!arrayKeyExists('id', $_REQUEST)) {
            $pages = new Model('pages');
            if (!arrayKeyExists('menu', $_REQUEST)) {
                $itemsPerPage = (arrayKeyExists('start', $_REQUEST)) ? $_REQUEST['length'] : 10;
                $limit = ((arrayKeyExists('start', $_REQUEST)) ? $_REQUEST['start'] : 0) . ', ' . $itemsPerPage;
                $countTotal = $pages->countItems();
                if (arrayKeyExists('filters', $_REQUEST)) {
                    foreach ($_REQUEST['filters'] as $key => $value) {
                        if (in_array($key, array(
                            'url',
                            'title',
                            'menu_text',
                            'submenu_text'
                        ))) {
                            $pages->$key = array('%' . $value . '%', ' LIKE ');
                        } else {
                            $pages->$key = $value;
                        }
                    }
                }
                $countFiltered = $pages->countItems();
                $pages->limit($limit);
                $pg = $pages->get('AND');
                $pages = array();
                foreach ($pg as $page) {
                    unset($page->content);
                    $pages[] = $page;
                }
                $arrayResponse = array(
                    'sEcho' => $_REQUEST['secho'],
                    'iTotalRecords' => $countTotal,
                    'iTotalDisplayRecords' => $countFiltered,
                    'aaData' => $pages
                );
            } else {
                if (!empty($_REQUEST['language'])) {
                    $pages->language = $_REQUEST['language'];
                }
                $pages->order('menu_parent ASC , menu_order ASC');
                $pg = $pages->get();
                $pages = array();
                foreach ($pg as $page) {
                    unset($page->content);
                    $pages[] = $page;
                }
                $arrayResponse = $pages;
            }
            $response = json_encode($arrayResponse);
            if (arrayKeyExists('callback', $_GET)) {
                $response = $_GET['callback'] . '(' . $response . ')';
            }
            echo $response;
        } else {
            $page = new Model('pages');
            $page = $page->getOneResult('id', $_REQUEST['id']);
            echo json_encode($page);
        }
    }
}