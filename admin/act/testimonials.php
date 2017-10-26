<?php
namespace Act;

use Model\Model;

require_once(dirname(dirname(dirname(__FILE__))) . '/Utils/functions.php');
require_once(dirname(__FILE__) . '/act.class.php');

class Testimonials extends act {
	public function __construct() {
		$this->permission = 'Edit testimonials';
		$this->entity = new Model('testimonials');
		$this->fields = $_POST;
		$act = $this->act();
		return $act;
	}
}

new Testimonials();