<?php

namespace Model {

    class AdminsPermissions extends Model
    {
        public int $admin;
        public int $permission;

        public array $schema = array (
              'admin' => 
              array (
                'default' => NULL,
                'null' => 'NO',
                'DATA_TYPE' => 'int',
                'extra' => '',
                'table_reference' => 'admins',
                'column_reference' => 'id',
                'trc' => 
                array (
                  0 => 'id',
                  1 => 'name',
                  2 => 'password',
                  3 => 'status',
                  4 => 'username',
                ),
                'param_type' => 'i',
              ),
              'permission' => 
              array (
                'default' => NULL,
                'null' => 'NO',
                'DATA_TYPE' => 'tinyint',
                'extra' => '',
                'table_reference' => 'permissions',
                'column_reference' => 'id',
                'trc' => 
                array (
                  0 => 'id',
                  1 => 'name',
                ),
                'param_type' => 'i',
              ),
            );
        
        public function __construct() {
            parent::__construct('admins_permissions');
        }
    }
}