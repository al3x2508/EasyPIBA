<?php

namespace Controller;

class Act extends AdminAct
{
    public static $PERMISSION;
    public static $ENTITY;
    public function __construct($permission, $entity, $id = false)
    {
        self::$PERMISSION = $permission;
        self::$ENTITY = $entity;
        parent::__construct($id);
    }
}