<?php

namespace Model {

    class News extends Model
    {
        public int $id;
        public ?string $language;
        public string $title;
        public string $content;
        public ?string $image;
        public ?int $admin;
        public ?int $status;

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
              'content' => 
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
              'image' => 
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
              'date_published' => 
              array (
                'default' => 'CURRENT_TIMESTAMP',
                'null' => 'YES',
                'DATA_TYPE' => 'timestamp',
                'extra' => 'DEFAULT_GENERATED',
                'table_reference' => NULL,
                'column_reference' => NULL,
                'trc' => NULL,
                'param_type' => 's',
              ),
              'admin' => 
              array (
                'default' => '0',
                'null' => 'YES',
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
              'status' => 
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
            );
        
        public function __construct() {
            parent::__construct('news');
        }
    }
}