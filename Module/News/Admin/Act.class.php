<?php
namespace Module\News\Admin;
use Controller\AdminAct;
use Controller\AdminController;
use Model\Model;
use Utils\PHPThumb\GD;

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/Utils/functions.php');

class Act extends AdminAct {
	public function __construct() {
		$this->permission = 'Edit news';
		$this->entity = new Model('news');
		$this->fields = $_POST;
		$this->fields['admin'] = AdminController::getCurrentUser()->id;
		return $this->act();
	}
	public function act() {
		$adminController = new AdminController();
		if($adminController->checkPermission($this->permission)) {
			if(array_key_exists('id', $this->fields)) {
				foreach($this->fields AS $key => $value) {
					if($key == 'image') {
						if(!empty(trim(strip_tags($value)))) {
							$filename = strip_tags($value);
							$upload_dir = _APP_DIR_ . 'uploads/';
							$uploaded_file = $upload_dir . $filename;
							$path_parts = pathinfo($uploaded_file);
							$fname = $path_parts['filename'];
							$extension = $path_parts['extension'];
							$target_dir = _APP_DIR_ . 'img/news/';
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
					elseif($key == 'content') $this->fields[$key] = htmlspecialchars_decode($value);
				}
			}
		}
		return parent::act();
	}
}