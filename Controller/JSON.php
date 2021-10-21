<?php

namespace Controller;

use Module\JSON\Admin;

class JSON extends Admin
{
    public static $PERMISSION;
    public static $ENTITY;
    public static $WILDCARDS;

    public function __construct($permission, $entity, $wildcards = ['name'])
    {
        self::$PERMISSION = $permission;
        self::$ENTITY = $entity;
        self::$WILDCARDS = $wildcards;
        parent::__construct();
    }
}