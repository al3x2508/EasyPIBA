<?php
namespace Module\Media\Admin;
use Controller\AdminController;
use Controller\AdminAct;
use Model\Model;
use \PHPThumb\GD;
use Utils\Util;

require_once(dirname(__FILE__, 4) . '/Utils/functions.php');

class Act extends AdminAct {
	public function __construct() {
		$this->permission = 'Edit pages';
		$this->entity = new Model('media');
		$this->fields = $_POST;
		return true;
	}
	public function act($where = false) {
		$adminController = new AdminController();
		if($adminController->checkPermission($this->permission)) {
			if(arrayKeyExists('id', $this->fields)) {
				$protected = arrayKeyExists('protected', $this->fields);
				if($protected) unset($this->fields['protected']);
				foreach($this->fields AS $key => $value) {
					if($key == 'image') {
						unset($this->fields['image']);
						$this->fields['filename'] = $value;
						$filename = strip_tags($value);
						$upload_dir = $_ENV['APP_DIR'] . 'uploads/';
						if($protected) $upload_dir .= 'protected/';
						$uploaded_file = $upload_dir . $filename;
						$path_parts = pathinfo($uploaded_file);
						$pathFilename = $path_parts['filename'];
						$extension = $path_parts['extension'];
						$allowedTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF);
						$detectedType = exif_imagetype($uploaded_file);
						if(in_array($detectedType, $allowedTypes)) {
							$target_dir = $_ENV['APP_DIR'] . 'assets/img/uploads/';
							if($protected) $target_dir = $upload_dir;
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
				}
				return parent::act();
			}
			elseif(arrayKeyExists('delete', $this->fields)) {
				$media = $this->entity->getOneResult('id', $this->fields['delete']);
				$filename = $media->filename;
				$target_dir = $_ENV['APP_DIR'] . 'assets/img/uploads/';
				$uploaded_file = $target_dir . $filename;
				if(!file_exists($uploaded_file)) {
					$target_dir = $_ENV['APP_DIR'] . 'uploads/protected/';
					$uploaded_file = $target_dir . $filename;
				}
				$allowedTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF);
				$detectedType = exif_imagetype($uploaded_file);
				if($detectedType && in_array($detectedType, $allowedTypes)) {
					$path_parts = pathinfo($uploaded_file);
					$pathFilename = $path_parts['filename'];
					$extension = $path_parts['extension'];
					$thumb_file_name360 = $target_dir . $pathFilename . '-360x220.' . $extension;
					$thumb_file_name720 = $target_dir . $pathFilename . '-720x220.' . $extension;
					unlink($uploaded_file);
					unlink($thumb_file_name360);
					unlink($thumb_file_name720);
				}
				else {
					$target_dir = $_ENV['APP_DIR'] . 'uploads/';
					$uploaded_file = $target_dir . $filename;
					if(!file_exists($uploaded_file)) {
						$target_dir = $_ENV['APP_DIR'] . 'uploads/protected/';
						$uploaded_file = $target_dir . $filename;
					}
					unlink($uploaded_file);
				}
				$media->delete();
				return true;
			}
			else {
				if(isset($_FILES) && arrayKeyExists('edimage', $_FILES)) {
					if(is_array($_FILES['edimage']['name'])) {
						$return = array();
						for($i = 0; $i < count($_FILES['edimage']['name']); $i++) {
							$file = array();
							foreach(array_keys($_FILES['edimage']) AS $key) $file[$key] = $_FILES['edimage'][$key][$i];
							$return[] = self::uploadImg($file, true);
						}
						echo json_encode($return);
						exit;
					}
					else $error = self::uploadImg($_FILES['edimage']);
				}
				elseif(arrayKeyExists('clearImg', $_POST)) {
					$filename = strip_tags($_POST['clearImg']);
					$target_dir = $_ENV['APP_DIR'] . 'uploads/';
					$target_file = $target_dir . $filename;
					if(!file_exists($target_file)) {
						$target_dir = $_ENV['APP_DIR'] . 'uploads/protected/';
						$target_file = $target_dir . $filename;
					}
					$path_parts = pathinfo($target_file);
					$thumb_file_name = 'thumb' . $path_parts['filename'] . '.jpg';
					$thumb_target_file = $target_dir . $thumb_file_name;
					if(file_exists($target_file)) unlink($target_file);
					if(file_exists($thumb_target_file)) unlink($thumb_target_file);
					exit;
				}
				else $error = __('No uploaded file');
				echo json_encode(array('error' => $error));
			}
			return true;
		}
		return false;
	}
	private static function uploadImg($file, $return = false) {
		$protected = arrayKeyExists('protected', $_REQUEST);
		if($file['size'] <= 5242880) {
			$allowedTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF);
			$detectedType = exif_imagetype($file['tmp_name']);
			if(in_array($detectedType, $allowedTypes)) {
				$uid = uniqid('img_');
				$filename = $uid . basename($file['name']);
				$target_dir = $_ENV['APP_DIR'] . 'uploads/';
				if($protected) {
					$target_dir .= 'protected/';
					$filename = Util::getUrlFromString($file['name'], true);
				}
				$target_file = $target_dir . $filename;
				$check = getimagesize($file['tmp_name']);
				if($check !== false) {
					if(move_uploaded_file($file['tmp_name'], $target_file)) {
						$path_parts = pathinfo($target_file);
						$thumb_file_name = 'thumb' . $path_parts['filename'] . '.jpg';
						$thumb_target_file = $target_dir . $thumb_file_name;
						$thumb = new \PHPThumb\GD($target_file);
						$thumb->resize(100, 100);
						$thumb->save($thumb_target_file, 'jpg');
						$targetThumbDir = $_ENV['FOLDER_URL'] . 'uploads/';
						if($protected) $targetThumbDir .= 'protected/';
						if(!$return) {
							echo json_encode(array('src' => $targetThumbDir . $thumb_file_name, 'image' => $filename));
							exit;
						}
						else return array('src' => $targetThumbDir . $thumb_file_name, 'image' => $filename);
					}
					else $error = __('Error uploading');
				}
				else $error = __('File is not an image');
			}
			else {
				$uid = uniqid('media_');
				$path_parts = pathinfo($file['name']);
				$filename = $uid . '.' . $path_parts['extension'];
				$target_dir = $_ENV['APP_DIR'] . 'uploads/';
				$abs_target_dir = $_ENV['FOLDER_URL'] . 'uploads/';
				if($protected) {
					$target_dir .= 'protected/';
					$abs_target_dir .= 'protected/';
					$filename = Util::getUrlFromString($file['name'], true);
				}
				$target_file = $target_dir . $filename;
				if(move_uploaded_file($file['tmp_name'], $target_file)) {
					if(!$return) {
						echo json_encode(array('src' => $abs_target_dir . $filename, 'image' => $filename));
						exit;
					}
					else return array('src' => $abs_target_dir . $filename, 'image' => $filename);
				}
				else $error = __('Error uploading');
			}
		}
		else $error = __('Max upload 5MB');
		return $error;
	}
}
$media = new Act();
if($media && arrayKeyExists('id', $_REQUEST) || arrayKeyExists('delete', $_REQUEST) || arrayKeyExists('edimage', $_FILES) || arrayKeyExists('clearImg', $_REQUEST)) return $media->act();