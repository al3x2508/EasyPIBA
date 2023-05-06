<?php
namespace Module\Testimonials\Admin;
use Controller\AdminAct;
use Controller\AdminController;
use Model\Model;
use \PHPThumb\GD;
use Utils\Util;

require_once(dirname(__FILE__, 4) . '/Utils/functions.php');

class Act extends AdminAct {
	public function __construct() {
		$this->permission = 'Edit testimonials';
		$this->entity = new Model('testimonials');
		$this->fields = $_POST;
		return $this->act();
	}
	public function act($where = false) {
		$adminController = new AdminController();
		if($adminController->checkPermission($this->permission)) {
			if(arrayKeyExists('id', $this->fields)) {
				foreach($this->fields AS $key => $value) {
					if($key == 'image') {
						if(!empty(trim(strip_tags($value)))) {
							$filename = strip_tags($value);
							$upload_dir = $_ENV['APP_DIR'] . 'uploads/';
							$uploaded_file = $upload_dir . $filename;
							$path_parts = pathinfo($uploaded_file);
							$fname = Util::getUrlFromString($path_parts['filename']);
							$extension = $path_parts['extension'];
							$target_dir = $_ENV['APP_DIR'] . 'assets/img/testimonials/';
							$destination_file = $target_dir . $filename;
							$thumb_file_name = 'thumb' . $fname . '.jpg';
							$thumb_target_file = $upload_dir . $thumb_file_name;
							unlink($thumb_target_file);
							rename($uploaded_file, $destination_file);
							$thumb_file_name160 = $target_dir . $fname . '-160x160.' . $extension;
							$thumb = new GD($destination_file);
							$thumb->resize(160, 160);
							$thumb->save($thumb_file_name160, $extension);
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