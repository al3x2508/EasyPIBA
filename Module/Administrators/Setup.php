<?php

namespace Module\Administrators;

class Setup extends \Module\Setup
{
    const PERMISSION = 'Edit administrators';
    const ENTITY = 'admins';
    public function __construct()
    {
        parent::__construct();
        $this->registerBackendUrl(
            array(
                'permission' => self::PERMISSION,
                'url' => 'administrators',
                'menu_text' => 'Administrators',
                'menu_class' => 'users'
            )
        );
        $this->registerBackendUrl(
            array(
                'permission' => self::PERMISSION,
                'url' => 'cache',
                'menu_text' => 'Cache',
                'menu_class' => 'folder'
            )
        );
        return true;
    }
}