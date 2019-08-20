<?php
namespace Module\Media\Admin;

use Model\Model;
use Module\JSON\Admin;

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/Utils/functions.php');

class JSON extends Admin
{
    public function __construct()
    {
        $this->permission = "Edit pages";
        parent::__construct();
    }

    public function get()
    {
        if (!arrayKeyExists('id', $_REQUEST)) {
            $media = new Model('media');
            $itemsPerPage = (arrayKeyExists('start', $_REQUEST))?$_REQUEST['length']:10;
            $limit = ((arrayKeyExists('start', $_REQUEST))?$_REQUEST['start']:0) . ', ' . $itemsPerPage;
            $countTotal = $media->countItems();
            if (arrayKeyExists('filters', $_REQUEST)) {
                foreach ($_REQUEST['filters'] AS $key => $value) {
                    if (in_array($key, array(
                        'filename'
                    ))) {
                        $media->$key = array('%' . $value . '%', ' LIKE ');
                    } else {
                        $media->$key = $value;
                    }
                }
            }
            $countFiltered = $media->countItems();
            $media->limit($limit);
            $media = $media->get('AND');
            $responseArray = array(
                'sEcho' => $_REQUEST['secho'],
                'iTotalRecords' => $countTotal,
                'iTotalDisplayRecords' => $countFiltered,
                'aaData' => $media
            );
            $response = json_encode($responseArray);
            if (arrayKeyExists('callback', $_GET)) {
                $response = $_GET['callback'] . '(' . $response . ')';
            }
            echo $response;
        } else {
            $media = new Model('media');
            $media = $media->getOneResult('id', $_REQUEST['id']);
            echo json_encode($media);
        }
    }
}