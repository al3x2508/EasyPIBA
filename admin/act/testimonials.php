<?php
namespace Act;
require_once(dirname(dirname(dirname(__FILE__))) . '/Utils/functions.php');
require_once(dirname(__FILE__) . '/act.class.php');
class Testimoniale extends act {
	public function __construct() {
		$this->permission = 'Edit testimonials';
		$this->entity = new \Model\Model('testimoniale');
		$this->fields = $_POST;
		$act = $this->act();
		return $act;
	}
}
new \Act\Testimoniale();
?>