<?php

namespace Module\Media\Admin;

use Controller\AdminController;
use Model\Model;
use Module\JSON\Admin;
use Module\Media\Admin\Act as Media;
use Module\Users\Controller;

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
            $itemsPerPage = (arrayKeyExists('length', $_REQUEST))?$_REQUEST['length']:10;
            $limit = ((arrayKeyExists('start', $_REQUEST))?$_REQUEST['start']:0) . ', ' . $itemsPerPage;
            $countTotal = $media->countItems();
            if (arrayKeyExists('filters', $_REQUEST)) {
                foreach ($_REQUEST['filters'] as $key => $value) {
                    if (in_array($key, array(
                        'filename'
                    ))) {
                        $media->$key = array('%' . $value . '%', ' LIKE ');
                    } else {
                        $media->$key = $value;
                    }
                }
            }
            if (!AdminController::getCurrentUser()) {
                $media->user = Controller::getCurrentUser(true);
            }
            $countFiltered = $media->countItems();
            $media->limit($limit);
            if(arrayKeyExists('order', $_REQUEST)) $media->order($_REQUEST['order']);
            $mediaOps = $media->get('AND');
            $media = [];
            foreach ($mediaOps as $file) {
                if($file->type === 1) {
                    $path_parts = pathinfo($file->filename);
                    if(!$file->thumbfolder) $thumb_file_name = 'thumb' . $path_parts['filename'] . '.jpg';
                    else $thumb_file_name = $file->filename;
                    $file->thumbnail = $thumb_file_name;
                }
                Media::addFileType($file);
                $media[] = $file;
            }
            if (arrayKeyExists('secho', $_REQUEST)) {
                $responseArray = array(
                    'sEcho' => $_REQUEST['secho'],
                    'iTotalRecords' => $countTotal,
                    'iTotalDisplayRecords' => $countFiltered,
                    'aaData' => $media
                );
            } else {
                $responseArray = $media;
            }
            $response = json_encode($responseArray);
            if (arrayKeyExists('callback', $_GET)) {
                $response = $_GET['callback'] . '(' . $response . ')';
            }
            echo $response;
        } else {
            $media = new Model('media');
            $media = $media->getOneResult('id', $_REQUEST['id']);
            Media::addFileType($media);
            echo json_encode($media);
        }
    }
}