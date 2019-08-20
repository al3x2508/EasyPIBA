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
        $act = false;
        //Check if user has access
        if ($this->hasAccess()) {
            //$id is needed for update and delete, it is set in constructor by the admin router
            if ($id) $this->fields['id'] = $id;
            //The methods are POST for create, PATCH for update and DELETE for delete
            if (strtolower($_SERVER['REQUEST_METHOD']) == 'delete') {
                $this->delete();
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
        $this->createThumbnail()->setupTestimonialContent();
        foreach ($this->fields AS $key => $value) $this->entity->$key = $value;
        return $this->entity->create();
    }

    public function patch() {
        $this->createThumbnail()->setupTestimonialContent();
        foreach ($this->fields AS $key => $value) $this->entity->$key = $value;
        return $this->entity->update();
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
                $target_dir = _APP_DIR_ . 'assets/img/testimonials/';
                $destination_file = $target_dir . $filename;
                $thumb_file_name = 'thumb' . $path_parts['filename'] . '.jpg';
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
        return $this;
    }

    private function setupTestimonialContent() {
        if(arrayKeyExists('content', $this->fields)) $this->fields['content'] = htmlspecialchars_decode($this->fields['content']);
        return $this;
    }
}