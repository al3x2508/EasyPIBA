<?php

namespace Model {

    class ModuleRoutes extends Model
    {
        public int $id;
        public int $module;
        public string $url;
        public ?int $type;
        public ?int $mustBeLoggedIn;
        public ?int $hiddenForLoggedIn;
        public ?int $menu_position;
        public ?string $menu_text;
        public ?string $submenu_text;
        public ?int $menu_order;
        public ?string $menu_parent;

        public array $schema = array (
              'id' => 
              array (
                'default' => NULL,
                'null' => 'NO',
                'DATA_TYPE' => 'int',
                'extra' => 'auto_increment',
                'table_reference' => NULL,
                'column_reference' => NULL,
                'trc' => NULL,
                'param_type' => 'i',
              ),
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
              'type' => 
              array (
                'default' => '0',
                'null' => 'YES',
                'DATA_TYPE' => 'tinyint',
                'extra' => '',
                'table_reference' => NULL,
                'column_reference' => NULL,
                'trc' => NULL,
                'param_type' => 'i',
              ),
              'mustBeLoggedIn' => 
              array (
                'default' => '0',
                'null' => 'YES',
                'DATA_TYPE' => 'tinyint',
                'extra' => '',
                'table_reference' => NULL,
                'column_reference' => NULL,
                'trc' => NULL,
                'param_type' => 'i',
              ),
              'hiddenForLoggedIn' => 
              array (
                'default' => '0',
                'null' => 'YES',
                'DATA_TYPE' => 'tinyint',
                'extra' => '',
                'table_reference' => NULL,
                'column_reference' => NULL,
                'trc' => NULL,
                'param_type' => 'i',
              ),
              'menu_position' => 
              array (
                'default' => '0',
                'null' => 'YES',
                'DATA_TYPE' => 'tinyint',
                'extra' => '',
                'table_reference' => NULL,
                'column_reference' => NULL,
                'trc' => NULL,
                'param_type' => 'i',
              ),
              'menu_text' => 
              array (
                'default' => NULL,
                'null' => 'YES',
                'DATA_TYPE' => 'varchar',
                'extra' => '',
                'table_reference' => NULL,
                'column_reference' => NULL,
                'trc' => NULL,
                'param_type' => 's',
              ),
              'submenu_text' => 
              array (
                'default' => NULL,
                'null' => 'YES',
                'DATA_TYPE' => 'varchar',
                'extra' => '',
                'table_reference' => NULL,
                'column_reference' => NULL,
                'trc' => NULL,
                'param_type' => 's',
              ),
              'menu_order' => 
              array (
                'default' => '0',
                'null' => 'YES',
                'DATA_TYPE' => 'tinyint',
                'extra' => '',
                'table_reference' => NULL,
                'column_reference' => NULL,
                'trc' => NULL,
                'param_type' => 'i',
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
            parent::__construct('module_routes');
        }
    }
}