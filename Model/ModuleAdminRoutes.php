<?php

namespace Model {

    class ModuleAdminRoutes extends Model
    {
        public int $module;
        public int $permission;
        public string $url;
        public string $menu_text;
        public ?string $menu_class;
        public ?string $menu_parent;

        public array $schema = array (
              'module' => 
              array (
                'default' => NULL,
                'null' => 'NO',
                'DATA_TYPE' => 'int',
                'extra' => '',
                'table_reference' => 'modules',
                'column_reference' => 'id',
                'trc' => 
                array (
                  0 => 'id',
                  1 => 'name',
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
              'url' => 
              array (
                'default' => NULL,
                'null' => 'NO',
                'DATA_TYPE' => 'varchar',
                'extra' => '',
                'table_reference' => NULL,
                'column_reference' => NULL,
                'trc' => NULL,
                'param_type' => 's',
              ),
              'menu_text' => 
              array (
                'default' => NULL,
                'null' => 'NO',
                'DATA_TYPE' => 'varchar',
                'extra' => '',
                'table_reference' => NULL,
                'column_reference' => NULL,
                'trc' => NULL,
                'param_type' => 's',
              ),
              'menu_class' => 
              array (
                'default' => '',
                'null' => 'YES',
                'DATA_TYPE' => 'varchar',
                'extra' => '',
                'table_reference' => NULL,
                'column_reference' => NULL,
                'trc' => NULL,
                'param_type' => 's',
              ),
              'menu_parent' => 
              array (
                'default' => '',
                'null' => 'YES',
                'DATA_TYPE' => 'varchar',
                'extra' => '',
                'table_reference' => NULL,
                'column_reference' => NULL,
                'trc' => NULL,
                'param_type' => 's',
              ),
            );
        
        public function __construct() {
            parent::__construct('module_admin_routes');
        }
    }
}