<?php
namespace Module\Testimonials\Admin;
use Controller\AdminAct;
use Controller\AdminController;
use Model\Model;
use \PHPThumb\GD;

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/Utils/functions.php');

class Act extends AdminAct {
	public function __construct($id) {
		$this->permission = 'Edit testimonials';
		$this->entity = new Model('testimonials');
		if ($this->hasAccess()) {
			if ($id) $this->fields['id'] = $id;
			if (strtolower($_SERVER['REQUEST_METHOD']) == 'delete') {
				if ($id) $this->delete();
				else $this->sendStatus(false, __('No ID set'));
			}
			else {
				$method = 'patch';
				if (strtolower($_SERVER['REQUEST_METHOD']) == 'patch') {
					if ($id) parse_str(file_get_contents('php://input'), $_PATCH);
					else $this->sendStatus(false, __('No ID set'));
				}
				else $method = 'create';
				foreach ($method == 'patch'?$_PATCH:$_POST AS $key => $value) $this->fields[$key] = $value;
				$this->act();
				$act = call_user_func_array(array($this, $method), array());
			}
		}
		$this->sendStatus($act);
	}
	public function act() {
		foreach($this->fields AS $key => $value) {
			if($key == 'image') {
				if(!empty(trim(strip_tags($value)))) {
                    $filename = strip_tags($value);
                    $upload_dir = _APP_DIR_ . 'uploads/';
                    $uploaded_file = $upload_dir . $filename;
                    $path_parts = pathinfo($uploaded_file);
                    $fname = $path_parts['filename'];
                    $extension = $path_parts['extension'];
                    $target_dir = _APP_DIR_ . 'assets/img/testimonials/';
                    $destination_file = $target_dir . $filename;
                    copy($uploaded_file, $destination_file);
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