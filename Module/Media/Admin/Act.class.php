<?php
namespace Module\Media\Admin;

use Controller\AdminAct;
use Controller\AdminController;
use Model\Model;
use Module\Users\Controller;
use PHPThumb\GD;

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/Utils/functions.php');

class Act extends AdminAct
{
    public function __construct()
    {
        $this->permission = 'Edit pages';
        $this->entity = new Model('media');
        $this->fields = $_POST;
        return true;
    }

    public function act()
    {
        $adminController = new AdminController();
        $userId = Controller::getCurrentUser(true);
        if ($adminController->checkPermission($this->permission) || $userId) {
            if (arrayKeyExists('id', $this->fields)) {
                foreach ($this->fields AS $key => $value) {
                    if ($key == 'image') {
                        unset($this->fields['image']);
                        $this->fields['filename'] = $value;
                        $filename = strip_tags($value);
                        $upload_dir = _APP_DIR_ . 'uploads/';
                        if(!AdminController::getCurrentUser()) {
                            $upload_dir .= $userId . '/';
                            if(!file_exists($upload_dir)) mkdir($upload_dir);
                        }
                        $uploaded_file = $upload_dir . $filename;
                        $path_parts = pathinfo($uploaded_file);
                        $pathFilename = $path_parts['filename'];
                        $extension = $path_parts['extension'];
                        $target_dir = _APP_DIR_ . 'assets/img/uploads/';
                        if(!AdminController::getCurrentUser()) {
                            $target_dir .= $userId . '/';
                            if(!file_exists($target_dir)) mkdir($target_dir);
                        }
                        $destination_file = $target_dir . $filename;
                        $thumb_file_name = 'thumb' . $path_parts['filename'] . '.jpg';
                        $thumb_target_file = $upload_dir . $thumb_file_name;
                        unlink($thumb_target_file);
                        rename($uploaded_file, $destination_file);
                        $thumb_file_name360 = $target_dir . $pathFilename . '-360x220.' . $extension;
                        $thumb_file_name720 = $target_dir . $pathFilename . '-720x220.' . $extension;
                        $thumb = new GD($destination_file);
                        $thumb->resize(720, 220);
                        $thumb->save($thumb_file_name720, $extension);
                        $thumb->resize(360, 220);
                        $thumb->save($thumb_file_name360, $extension);
                    }
                }
                return parent::act();
            } elseif (arrayKeyExists('delete', $this->fields)) {
                $media = $this->entity->getOneResult('id', $this->fields['delete']);
                $filename = $media->filename;
                $target_dir = _APP_DIR_ . 'assets/img/uploads/';
                if(!AdminController::getCurrentUser()) {
                    $target_dir .= $userId . '/';
                    if(!file_exists($target_dir)) mkdir($target_dir);
                }
                $uploaded_file = $target_dir . $filename;
                $path_parts = pathinfo($uploaded_file);
                $pathFilename = $path_parts['filename'];
                $extension = $path_parts['extension'];
                $thumb_file_name360 = $target_dir . $pathFilename . '-360x220.' . $extension;
                $thumb_file_name720 = $target_dir . $pathFilename . '-720x220.' . $extension;
                unlink($uploaded_file);
                unlink($thumb_file_name360);
                unlink($thumb_file_name720);
                $media->delete();
                return true;
            } else {
                if (isset($_FILES) && arrayKeyExists('edimage', $_FILES)) {
                    if (is_array($_FILES['edimage']['name'])) {
                        $return = array();
                        for ($i = 0; $i < count($_FILES['edimage']['name']); $i++) {
                            $file = array();
                            foreach (array_keys($_FILES['edimage']) AS $key) {
                                $file[$key] = $_FILES['edimage'][$key][$i];
                            }
                            $return[] = self::uploadImg($file, true);
                        }
                        echo json_encode($return);
                        exit;
                    } else {
                        $error = self::uploadImg($_FILES['edimage']);
                    }
                } elseif (arrayKeyExists('clearImg', $_POST)) {
                    $filename = strip_tags($_POST['clearImg']);
                    $target_dir = _APP_DIR_ . 'uploads/';
                    if(!AdminController::getCurrentUser()) {
                        $target_dir .= $userId . '/';
                        if(!file_exists($target_dir)) mkdir($target_dir);
                    }
                    $target_file = $target_dir . $filename;
                    $path_parts = pathinfo($target_file);
                    $thumb_file_name = 'thumb' . $path_parts['filename'] . '.jpg';
                    $thumb_target_file = $target_dir . $thumb_file_name;
                    if (file_exists($target_file)) {
                        unlink($target_file);
                    }
                    if (file_exists($thumb_target_file)) {
                        unlink($thumb_target_file);
                    }
                    exit;
                } else {
                    $error = __('No uploaded file');
                }
                echo json_encode(array('error' => $error));
            }
            return true;
        }
        return false;
    }

    private static function uploadImg($file, $return = false)
    {
        $userId = Controller::getCurrentUser(true);
        if ($file['size'] <= 5242880) {
            $allowedTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF);
            $detectedType = exif_imagetype($file['tmp_name']);
            if (in_array($detectedType, $allowedTypes)) {
                $uid = uniqid('img_');
                $filename = $uid . basename($file['name']);
                $target_dir = _APP_DIR_ . 'uploads/';
                if(!AdminController::getCurrentUser()) {
                    $target_dir .= $userId . '/';
                    if(!file_exists($target_dir)) mkdir($target_dir);
                }
                $target_file = $target_dir . $filename;
                $check = getimagesize($file['tmp_name']);
                if ($check !== false) {
                    if (move_uploaded_file($file['tmp_name'], $target_file)) {
                        $path_parts = pathinfo($target_file);
                        $thumb_file_name = 'thumb' . $path_parts['filename'] . '.jpg';
                        $thumb_target_file = $target_dir . $thumb_file_name;
                        $thumb = new \PHPThumb\GD($target_file);
                        $thumb->resize(100, 100);
                        $thumb->save($thumb_target_file, 'jpg');
                        $targetThumbDir = _FOLDER_URL_ . 'uploads/';
                        if(!AdminController::getCurrentUser()) {
                            $targetThumbDir .= $userId . '/';
                            if(!file_exists($targetThumbDir)) mkdir($targetThumbDir);
                        }
                        if (!$return) {
                            echo json_encode(array('src' => $targetThumbDir . $thumb_file_name, 'image' => $filename));
                            exit;
                        } else {
                            return array('src' => $targetThumbDir . $thumb_file_name, 'image' => $filename);
                        }
                    } else {
                        $error = __('Error uploading');
                    }
                } else {
                    $error = __('File is not an image');
                }
            } else {
                $error = __('File is not an image');
            }
        } else {
            $error = sprintf(__('Max upload %s'), ini_get("upload_max_filesize"));
        }
        return $error;
    }
}

$media = new Act();
if ($media && arrayKeyExists('id', $_REQUEST) || arrayKeyExists('delete', $_REQUEST) || arrayKeyExists('edimage',
        $_FILES) || arrayKeyExists('clearImg', $_REQUEST)
) {
    return $media->act();
}