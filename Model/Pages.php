<?php

namespace Model {

    class Pages extends Model
    {
        public int $id;
        public ?string $url;
        public ?string $language;
        public string $title;
        public string $description;
        public ?string $keywords;
        public string $h1;
        public ?string $js;
        public ?string $css;
        public ?string $menu_text;
        public ?string $submenu_text;
        public ?int $menu_order;
        public ?int $menu_parent;
        public ?int $visible;
        public ?string $metaog;

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
              'url' => 
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
              'language' => 
              array (
                'default' => 'en',
                'null' => 'YES',
                'DATA_TYPE' => 'varchar',
                'extra' => '',
                'table_reference' => 'languages',
                'column_reference' => 'code',
                'trc' => 
                array (
                  0 => 'code',
                  1 => 'name',
                ),
                'param_type' => 's',
              ),
              'title' => 
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
              'description' => 
              array (
                'default' => NULL,
                'null' => 'NO',
                'DATA_TYPE' => 'text',
                'extra' => '',
                'table_reference' => NULL,
                'column_reference' => NULL,
                'trc' => NULL,
                'param_type' => 's',
              ),
              'keywords' => 
              array (
                'default' => NULL,
                'null' => 'YES',
                'DATA_TYPE' => 'text',
                'extra' => '',
                'table_reference' => NULL,
                'column_reference' => NULL,
                'trc' => NULL,
                'param_type' => 's',
              ),
              'h1' => 
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
              'content' => 
              array (
                'default' => NULL,
                'null' => 'NO',
                'DATA_TYPE' => 'mediumtext',
                'extra' => '',
                'table_reference' => NULL,
                'column_reference' => NULL,
                'trc' => NULL,
                'param_type' => 's',
              ),
              'js' => 
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
              'css' => 
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
              'menu_text' => 
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
              'submenu_text' => 
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
                'default' => '0',
                'null' => 'YES',
                'DATA_TYPE' => 'tinyint',
                'extra' => '',
                'table_reference' => NULL,
                'column_reference' => NULL,
                'trc' => NULL,
                'param_type' => 'i',
              ),
              'visible' => 
              array (
                'default' => '1',
                'null' => 'YES',
                'DATA_TYPE' => 'tinyint',
                'extra' => '',
                'table_reference' => NULL,
                'column_reference' => NULL,
                'trc' => NULL,
                'param_type' => 'i',
              ),
              'metaog' => 
              array (
                'default' => NULL,
                'null' => 'YES',
                'DATA_TYPE' => 'text',
                'extra' => '',
                'table_reference' => NULL,
                'column_reference' => NULL,
                'trc' => NULL,
                'param_type' => 's',
              ),
            );
        
        public function __construct() {
            parent::__construct('pages');
        }
    }
}