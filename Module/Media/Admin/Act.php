<?php

namespace Module\Media\Admin;

use Controller\AdminAct;
use Controller\AdminController;
use finfo;
use Model\Model;
use Module\Media\Setup;
use Module\Users\Controller;
use PHPThumb\GD;
use Util;

require_once(dirname(__FILE__, 4) . '/Utils/Util.php');

class Act extends AdminAct
{
    public static $PERMISSION = Setup::PERMISSION;
    public static $ENTITY = Setup::ENTITY;

    public function pre_delete_hook() {
        $this->entity->id = $this->fields['id'];
        $user = Controller::getCurrentUser();
        if (!AdminController::getCurrentUser()) {
            $this->entity->user = $user;
        }
        if ($this->entity->countItems()) {
            $media = $this->entity->getOneResult('id', $this->fields['id']);
            $filename = $media->filename;
            $target_dir = _APP_DIR_ . 'uploads/';
            if (!AdminController::getCurrentUser()) {
                $target_dir .= $user . '/';
                if (file_exists($target_dir)) {
                    $path_parts = pathinfo($filename);
                    $thumb_file_name = 'thumb' . $path_parts['filename'] . '.jpg';
                    @unlink($target_dir . $filename);
                    @unlink($target_dir . $thumb_file_name);
                }
            }
            return true;
        }
        $this->sendStatus('error');
        return false;
    }

    public function create()
    {
        $user = Controller::getCurrentUser();
        $error = false;
        if (isset($_FILES) && arrayKeyExists('edimage', $_FILES)) {
            if (is_array($_FILES['edimage']['name'])) {
                for ($i = 0; $i < count($_FILES['edimage']['name']); $i++) {
                    $file = array();
                    foreach (array_keys($_FILES['edimage']) as $key) {
                        $file[$key] = $_FILES['edimage'][$key][$i];
                    }
                    $upload = self::uploadImg($file);
                    if (!is_array($upload)) {
                        $error = $upload;
                        $upload = false;
                    }
                    $media = new Model('media');
                    $media->filename = $upload['media'];
                    $media->original_filename = $_FILES['edimage']['name'][$i];
                    if (!AdminController::getCurrentUser()) {
                        $media->user = $user;
                    }
                    $media->type = $upload['type'];
                    $media->folder = _FOLDER_URL_ . 'uploads/';
                    if (!AdminController::getCurrentUser()) {
                        $media->folder .= $user . '/';
                    }
                    $media->create();
                }
            } else {
                $upload = self::uploadImg($_FILES['edimage']);
                if (!is_array($upload)) {
                    $error = $upload;
                    $upload = false;
                }
                $media = new Model('media');
                $media->filename = $upload['media'];
                $media->original_filename = $_FILES['edimage']['name'];
                if (!AdminController::getCurrentUser()) {
                    $media->user = $user;
                }
                $media->type = $upload['type'];
                $media->folder = _FOLDER_URL_ . 'uploads/';
                if (!AdminController::getCurrentUser()) {
                    $media->folder .= $user . '/';
                }
                $media->create();
            }
        } elseif (isset($_FILES) && arrayKeyExists('edfile', $_FILES)) {
            if (is_array($_FILES['edfile']['name'])) {
                for ($i = 0; $i < count($_FILES['edfile']['name']); $i++) {
                    $file = array();
                    foreach (array_keys($_FILES['edfile']) as $key) {
                        $file[$key] = $_FILES['edfile'][$key][$i];
                    }
                    $upload = self::uploadFile($file);
                    if (!is_array($upload)) {
                        $error = $upload;
                        $upload = false;
                    }
                    $media = new Model('media');
                    $media->filename = $upload['media'];
                    $media->original_filename = $_FILES['edfile']['name'][$i];
                    if (!AdminController::getCurrentUser()) {
                        $media->user = $user;
                    }
                    $media->type = $upload['type'];
                    $media->folder = _FOLDER_URL_ . 'uploads/';
                    if (!AdminController::getCurrentUser()) {
                        $media->folder .= $user . '/';
                    }
                    self::addFileType($media);
                    $media->create();
                }
            } else {
                $upload = self::uploadFile($_FILES['edfile']);
                if (!is_array($upload)) {
                    $error = $upload;
                    $upload = false;
                }
                $media = new Model('media');
                $media->filename = $upload['media'];
                $media->original_filename = $_FILES['edfile']['name'];
                if (!AdminController::getCurrentUser()) {
                    $media->user = $user;
                }
                $media->type = $upload['type'];
                $media->folder = _FOLDER_URL_ . 'uploads/';
                if (!AdminController::getCurrentUser()) {
                    $media->folder .= $user . '/';
                }
                self::addFileType($media);
                $media->create();
            }
        } elseif (arrayKeyExists('clearImg', $_POST)) {
            $id = strip_tags($_POST['clearImg']);
            $media = new Model('media');
            $media = $media->getOneResult('id', $id);
            if ($media) {
                $target_dir = _APP_DIR_ . 'uploads/';
                if (!AdminController::getCurrentUser()) {
                    if ($media->user != $user) {
                        return array(false, __('You don\'t have permission to delete this file'));
                    }
                }
                $target_file = $target_dir . $media->filename;
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                if (true === $ext = array_search($finfo->file($target_file), array(
                        'jpg' => 'image/jpeg',
                        'png' => 'image/png',
                        'gif' => 'image/gif',
                    ),                           true)) {
                    $path_parts = pathinfo($target_file);
                    $thumb_file_name = 'thumb' . $path_parts['filename'] . '.jpg';
                    $thumb_target_file = $target_dir . $thumb_file_name;
                    if (file_exists($thumb_target_file)) {
                        unlink($thumb_target_file);
                    }
                }
                if (file_exists($target_file)) {
                    unlink($target_file);
                }
                $media->delete();
            } else {
                return array(false, __('Nothing to delete'));
            }
            exit;
        } else {
            $error = __('No uploaded file');
        }
        return $error ? array('error' => $error) : $media;
    }

    private static function uploadImg($file, $moveUploaded = true)
    {
        $userId = Controller::getCurrentUser(true);
        if ($file['size'] <= 104857600) {
            $uid = uniqid('media_');
            $filename = $uid . basename($file['name']);
            $target_dir = _APP_DIR_ . 'uploads/';
            if (!AdminController::getCurrentUser()) {
                $target_dir .= $userId . '/';
                if (!file_exists($target_dir)) {
                    mkdir($target_dir);
                }
            }
            $target_file = $target_dir . $filename;
            $path_parts = pathinfo($target_file);
            if (strtolower($path_parts['extension']) === 'webp') {
                $filename = $uid . $path_parts['filename'] . '.jpg';
                $target_file = $target_dir . $filename;
                $im = imagecreatefromwebp($file['tmp_name']);
                imagejpeg($im, $target_file, 100);
                imagedestroy($im);

                $path_parts = pathinfo($target_file);
                $thumb_file_name = 'thumb' . $path_parts['filename'] . '.jpg';
                $thumb_target_file = $target_dir . $thumb_file_name;
                $thumb = new GD($target_file);
                $thumb->resize(800, 600);
                $thumb->save($thumb_target_file, 'jpg');
                return array(
                    'src' => $target_dir . $thumb_file_name,
                    'media' => $filename,
                    'type' => 1
                );
            } else {
                $mediaType = self::returnMediaType($file['tmp_name']);
                if ($mediaType === 1) {
                    $check = getimagesize($file['tmp_name']);
                    if ($check !== false) {
                        if ($moveUploaded && move_uploaded_file($file['tmp_name'], $target_file)) {
                            $path_parts = pathinfo($target_file);
                            $thumb_file_name = 'thumb' . $path_parts['filename'] . '.jpg';
                            $thumb_target_file = $target_dir . $thumb_file_name;
                            $thumb = new GD($target_file);
                            $thumb->resize(800, 600);
                            $thumb->save($thumb_target_file, 'jpg');
                            return array(
                                'src' => $target_dir . $thumb_file_name,
                                'media' => $filename,
                                'type' => $mediaType
                            );
                        } else {
                            $error = __('Error uploading');
                        }
                    } else {
                        $error = __('File is not an image');
                    }
                } elseif ($mediaType === 2) {
                    if (move_uploaded_file($file['tmp_name'], $target_file)) {
                        $path_parts = pathinfo($target_file);
                        exec(
                            "ffmpeg -i \"{$target_file}\" \"{$target_dir}{$path_parts['filename']}." . ((strtolower(
                                    $path_parts['extension']
                                ) == 'mp4') ? 'ogv' : 'mp4') . "\" > /dev/null &"
                        );
                        return array('src' => $target_file, 'media' => $filename, 'type' => $mediaType);
                    } else {
                        $error = __('Error uploading');
                    }
                } elseif ($mediaType === 3) {
                    if (move_uploaded_file($file['tmp_name'], $target_file)) {
                        $path_parts = pathinfo($target_file);
                        exec(
                            "ffmpeg -i \"{$target_file}\" \"{$target_dir}{$path_parts['filename']}." . ((strtolower(
                                    $path_parts['extension']
                                ) == 'mp3') ? 'ogg' : 'mp3') . "\" > /dev/null &"
                        );
                        return array('src' => $target_file, 'media' => $filename, 'type' => $mediaType);
                    } else {
                        $error = __('Error uploading');
                    }
                } else {
                    $error = __('File is not a media > ') . $mediaType;
                }
            }
        } else {
            $error = sprintf(__('Max upload %s'), ini_get("upload_max_filesize"));
        }
        return $error;
    }

    public static function returnMediaType($file)
    {
        if (file_exists($file)) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $ext = array_search($finfo->file($file), array(
                'jpg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
            ),                  true);
            if ($ext) {
                return 1;
            }
            $ext = array_search($finfo->file($file), array(
                'ogm' => 'video/ogm',
                'ogv' => 'video/ogv',
                'avi' => 'video/avi',
                'asx' => 'video/x-ms-asf',
                'mp4' => 'video/mp4',
                '3gp' => 'video/3gpp',
                'mpeg' => 'video/mpeg',
                'm4v' => 'video/m4v',
                'mpg' => 'video/mpeg',
                'mov' => 'video/quicktime',
                'flv' => 'video/flv',
                'wmv' => 'video/wmv',
                'webm' => 'video/webm',
            ),                  true);
            if ($ext) {
                return 2;
            }
            $ext = array_search($finfo->file($file), array(
                'ogg' => 'audio/ogg',
                'mp3' => 'audio/mpeg',
                'mp4' => 'audio/mp4',
                'wav' => 'audio/wav',
                'mka' => 'audio/x-matroska',
            ),                  true);
            if ($ext) {
                return 3;
            }

            $ext = array_search($finfo->file($file), array(
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ),                  true);
            if ($ext) {
                return 4;
            }

            $ext = array_search($finfo->file($file), array(
                'ppt' => 'application/vnd.ms-powerpoint',
                'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation'
            ),                  true);
            if ($ext) {
                return 5;
            }

            $ext = array_search($finfo->file($file), array(
                'pdf' => 'application/pdf',
            ),                  true);
            if ($ext) {
                return 6;
            }

            $ext = array_search($finfo->file($file), array(
                'txt' => 'text/plain',
            ),                  true);
            if ($ext) {
                return 7;
            }

            return $finfo->file($file);
        } else {
            return '';
        }
    }

    private static function uploadFile($file, $moveUploaded = true)
    {
        $userId = Controller::getCurrentUser(true);
        if ($file['size'] <= 104857600) {
            $uid = uniqid('media_');
            $path_parts = pathinfo($file['name']);
            $filename = $uid . Util::getUrlFromString(basename($path_parts['filename'])) . '.' . strtolower(
                    $path_parts['extension']
                );
            $target_dir = _APP_DIR_ . 'uploads/';
            if (!AdminController::getCurrentUser()) {
                $target_dir .= $userId . '/';
                if (!file_exists($target_dir)) {
                    mkdir($target_dir);
                }
            }
            $target_file = $target_dir . $filename;
            $mediaType = self::returnMediaType($file['tmp_name']);
            if ($moveUploaded && move_uploaded_file($file['tmp_name'], $target_file)) {
                return array(
                    'src' => $target_dir . $filename,
                    'media' => $filename,
                    'type' => $mediaType
                );
            } else {
                $error = __('Error uploading');
            }
        } else {
            $error = sprintf(__('Max upload %s'), ini_get("upload_max_filesize"));
        }
        return $error;
    }

    public static function addFileType(&$media)
    {
        switch ($media->type) {
            case 4:
                $media->filetype = 'file-word';
                break;
            case 5:
                $media->filetype = 'presentation';
                break;
            case 6:
                $media->filetype = 'file-pdf';
                break;
            case 7:
                $media->filetype = 'file-alt';
                break;
            default:
                break;
        }
    }
}