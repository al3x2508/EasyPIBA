<?php
namespace Module\Testimonials;

class Setup extends \Module\Setup {
	public function __construct() {
		parent::__construct();
		$this->registerFrontendUrl(array('url' => 'testimoniale', 'type' => 0, 'menu_position' => 0));
		$this->registerFrontendUrl(array('url' => '^testimoniale\/(.*)$', 'type' => 1, 'menu_position' => 0));
		$this->registerBackendUrl(array('permission' => 'Edit testimonials', 'url' => 'testimoniale', 'menu_text' => 'Testimonials', 'menu_class' => 'fal fa-comment'));
	}
}