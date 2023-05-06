<?php
namespace Module\Pages;

class Setup extends \Module\Setup {
    public function __construct() {
        parent::__construct();
        $this->registerBackendUrl(array('permission' => 'Edit pages', 'url' => 'pages', 'menu_text' => 'Pages', 'menu_class' => 'fal fa-file'));
        $this->registerBackendUrl(array('permission' => 'Edit pages', 'url' => 'menu', 'menu_text' => 'Menu', 'menu_class' => 'fal fa-bars', 'menu_parent' => 'pages'));
        $this->registerBackendUrl(array('permission' => 'Edit pages', 'url' => 'media', 'menu_text' => 'Media', 'menu_class' => 'fal fa-images', 'menu_parent' => 'pages'));
        return true;
    }
}