<?php

namespace Module\Media\Admin;

use Model\Model;
use Module\JSON\Admin;
use Module\Media\Admin\Act as Media;
use Module\Media\Setup;

require_once(dirname(__FILE__, 4) . '/Utils/Util.php');

class JSON extends Admin
{
    public static $ENTITY = Setup::ENTITY;
    public static $PERMISSION = Setup::PERMISSION;

    public function get()
    {
        if (!arrayKeyExists('id', $_REQUEST)) {
            $media = new Model('media');
            $itemsPerPage = (arrayKeyExists('length', $_REQUEST)) ? $_REQUEST['length'] : 10;
            $limit = ((arrayKeyExists('start', $_REQUEST)) ? $_REQUEST['start'] : 0) . ', ' . $itemsPerPage;
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
            $countFiltered = $media->countItems();
            $media->limit($limit);
            if (arrayKeyExists('order', $_REQUEST)) {
                $media->order($_REQUEST['order']);
            }
            $mediaOps = $media->get('AND');
            $media = [];
            foreach ($mediaOps as $file) {
                if ($file->type === 1) {
                    $path_parts = pathinfo($file->filename);
                    if (!$file->thumbfolder) {
                        $thumb_file_name = 'thumb' . $path_parts['filename'] . '.jpg';
                    } else {
                        $thumb_file_name = $file->filename;
                    }
                    $file->thumbnail = $thumb_file_name;
                }
                Media::addFileType($file);
                $media[] = $file;
            }
            $this->echoJsonResponse($media, $countTotal, $countFiltered);
        } else {
            $media = new Model('media');
            $media = $media->getOneResult('id', $_REQUEST['id']);
            Media::addFileType($media);
            echo json_encode($media);
        }
    }
}