<?php
namespace Module\Testimonials;

class Setup extends \Module\Setup {
	public function __construct() {
		parent::__construct();
		$this->registerFrontendUrl(array('url' => 'testimonials', 'type' => 0, 'menu_position' => 0));
		$this->registerFrontendUrl(array('url' => '^testimonial([0-9]+)\.html$', 'type' => 1, 'menu_position' => 0));
		$this->registerBackendUrl(array('permission' => 'Edit testimonials', 'url' => 'testimonials', 'menu_text' => 'Testimonials', 'menu_class' => 'fas fa-comment'));
	}
}