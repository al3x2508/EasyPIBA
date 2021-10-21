<?php

namespace Module\Pages;

class Setup extends \Module\Setup
{
    const PERMISSION = 'Edit pages';
    const ENTITY = 'pages';

    public function __construct()
    {
        parent::__construct();
        $this->registerBackendUrl(
            array('permission' => self::PERMISSION, 'url' => 'pages', 'menu_text' => 'Pages', 'menu_class' => 'file')
        );
        $this->registerBackendUrl(
            array(
                'permission' => self::PERMISSION,
                'url' => 'menu',
                'menu_text' => 'Menu',
                'menu_class' => 'bars',
                'menu_parent' => 'pages'
            )
        );
        $this->registerBackendUrl(
            array(
                'permission' => self::PERMISSION,
                'url' => 'media',
                'menu_text' => 'Media',
                'menu_class' => 'images',
                'menu_parent' => 'pages'
            )
        );
        return true;
    }
}