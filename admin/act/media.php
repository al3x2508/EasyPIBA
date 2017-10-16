<?php
namespace Act;
require_once(dirname(dirname(dirname(__FILE__))) . '/Utils/functions.php');
require_once(dirname(__FILE__) . '/act.class.php');
class Media extends act {
	public function __construct() {
		$this->permission = 'Edit pages';
		$this->entity = new \Model\Model('media');
		$this->fields = $_POST;
		return true;
	}
	public function act() {
		$adminController = new \Controller\AdminController();
		if($adminController->checkPermission($this->permission)) {
			if(array_key_exists('id', $this->fields)) {
				foreach($this->fields AS $cheie => $valoare) {
					if($cheie == 'image') {
						unset($this->fields['image']);
						$this->fields['filename'] = $valoare;
						$filename = strip_tags($valoare);
						$upload_dir = _APP_DIR_ . 'img/uploads/';
						$uploaded_file = $upload_dir . $filename;
						$path_parts = pathinfo($uploaded_file);
						$fname = $path_parts['filename'];
						$extension = $path_parts['extension'];
						$target_dir = _APP_DIR_ . 'img/uploads/';
						$destination_file = $target_dir . $filename;
						$thumb_file_name = 'thumb' . $path_parts['filename'] . '.jpg';
						$thumb_target_file = $upload_dir . $thumb_file_name;
						unlink($thumb_target_file);
						rename($uploaded_file, $destination_file);
						$thumb_file_name360 = $target_dir . $fname . '-360x220.' . $extension;
						$thumb_file_name720 = $target_dir . $fname . '-720x220.' . $extension;
						$thumb = new \Utils\PHPThumb\GD($destination_file);
						$thumb->resize(720, 220);
						$thumb->save($thumb_file_name720, $extension);
						$thumb->resize(360, 220);
						$thumb->save($thumb_file_name360, $extension);
					}
				}
			}
			elseif(array_key_exists('delete', $this->fields)) {
				$media = $this->entity->getOneResult('id', $this->fields['delete']);
				$filename = $media->filename;
				$target_dir = dirname(dirname(dirname(__FILE__))) . '/img/uploads/';
				$uploaded_file = $target_dir . $filename;
				$path_parts = pathinfo($uploaded_file);
				$fname = $path_parts['filename'];
				$extension = $path_parts['extension'];
				$thumb_file_name360 = $target_dir . $fname . '-360x220.' . $extension;
				$thumb_file_name720 = $target_dir . $fname . '-720x220.' . $extension;
				unlink($uploaded_file);
				unlink($thumb_file_name360);
				unlink($thumb_file_name720);
				$media->delete();
				return true;
			}
		}
		return parent::act();
	}
}
$media = new \Act\Media();
if($media && array_key_exists('id', $_REQUEST) || array_key_exists('delete', $_REQUEST)) return $media->act();