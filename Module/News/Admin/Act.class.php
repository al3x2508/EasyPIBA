<?php
namespace Module\News\Admin;

use Controller\AdminAct;
use Controller\AdminController;
use Model\Model;
use Module\News\Setup;
use PHPThumb\GD;
use Utils\Util;

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/Utils/functions.php');

class Act extends AdminAct
{
    public function __construct($id)
    {
        $this->permission = 'Edit news';
        $this->entity = new Model('news');
        $act = false;
        //Check if user has access
        if ($this->hasAccess()) {
            //$id is needed for update and delete, it is set in constructor by the admin router
            if ($id) $this->fields['id'] = $id;
            //The methods are POST for create, PATCH for update and DELETE for delete
            if (strtolower($_SERVER['REQUEST_METHOD']) == 'delete') {
                if ($id) {
                    $act = $this->delete();
                }
                else $this->sendStatus(false, __('No ID set'));
            }
            else {
                $method = 'patch';
                if (strtolower($_SERVER['REQUEST_METHOD']) == 'patch') {
                    //Set the global variable PATCH from the input
                    if ($id) parse_str(file_get_contents('php://input'), $_PATCH);
                    else $this->sendStatus(false, __('No ID set'));
                }
                else {
                    $method = 'create';
                    //Set the admin user for the post current admin
                    $this->fields['admin'] = AdminController::getCurrentUser()->id;
                }
                foreach ($method == 'patch' ? $_PATCH : $_POST AS $key => $value) $this->fields[$key] = $value;
                try {
                    $act = call_user_func_array(array($this, $method), array());
                }
                catch (\Exception $e) {
                    $this->sendStatus(false, $e->getMessage());
                }
            }
        }
        $this->sendStatus($act);
    }

    //Create post
    public function create() {
        $this->createThumbnail()->setupNewsContent()->setupNewsUrl();
        foreach ($this->fields AS $key => $value) $this->entity->$key = $value;
        return $this->entity->create();
    }

    public function patch() {
        $this->createThumbnail()->setupNewsContent()->setupNewsUrl(true);
        foreach ($this->fields AS $key => $value) $this->entity->$key = $value;
        return $this->entity->update();
    }

    public function delete() {
        $this->entity->id = $this->fields['id'];
        //We have to delete the URL for the post
        $oldPostValues = new Model('news');
        $oldPostValues = $oldPostValues->getOneResult('id', $this->fields['id']);
        $oldUrl = 'noutati/' . Util::getUrlFromString($oldPostValues->title) . '.html';
        $mR = new Model('module_routes');
        $mR = $mR->getOneResult('url', $oldUrl);
        if ($mR && $mR->modules->name == 'News') {
            $mR->delete();
        }
        return $this->entity->delete();
    }

    private function createThumbnail() {
        if(arrayKeyExists('image', $this->fields)) {
            $value = $this->fields['image'];
            $filename = strip_tags($value);
            if(!empty(trim($filename))) {
                $upload_dir = _APP_DIR_ . 'uploads/';
                $uploaded_file = $upload_dir . $filename;
                $path_parts = pathinfo($uploaded_file);
                $fname = $path_parts['filename'];
                $extension = $path_parts['extension'];
                $target_dir = _APP_DIR_ . 'assets/img/news/';
                $destination_file = $target_dir . $filename;
                $thumb_file_name = 'thumb' . $path_parts['filename'] . '.jpg';
                $thumb_target_file = $upload_dir . $thumb_file_name;
                unlink($thumb_target_file);
                rename($uploaded_file, $destination_file);
                $thumb_file_name360 = $target_dir . $fname . '-360x220.' . $extension;
                $thumb_file_name720 = $target_dir . $fname . '-720x220.' . $extension;
                $thumb = new GD($destination_file);
                $thumb->resize(720, 220);
                $thumb->save($thumb_file_name720, $extension);
                $thumb->resize(360, 220);
                $thumb->save($thumb_file_name360, $extension);
            }
            else unset($this->fields['image']);
        }
        return $this;
    }

    private function setupNewsContent() {
        if(arrayKeyExists('content', $this->fields)) $this->fields['content'] = htmlspecialchars_decode($this->fields['content']);
        return $this;
    }

    private function setupNewsUrl($isUpdate = false) {
        //If it is an update, delete the old url for the post if it's changed
        if($isUpdate) {
            $oldPostValues = new Model('news');
            $oldPostValues = $oldPostValues->getOneResult('id', $this->fields['id']);
            if($oldPostValues->title != $this->fields['title']) {
                $oldUrl = 'noutati/' . Util::getUrlFromString($oldPostValues->title) . '.html';
                $mR = new Model('module_routes');
                $mR = $mR->getOneResult('url', $oldUrl);
                if ($mR && $mR->modules->name == 'News') {
                    $mR->delete();
                }
            }
        }
        $url = 'noutati/' . Util::getUrlFromString($this->fields['title']) . '.html';
        $mR = new Model('module_routes');
        $mR = $mR->getOneResult('url', $url);
        if(!$mR) {
            $setup = new Setup();
            $setup->registerFrontendUrl(array('url' => $url, 'type' => 0, 'mustBeLoggedIn' => 0, 'menu_position' => 0));
        }
        return $this;
    }
}