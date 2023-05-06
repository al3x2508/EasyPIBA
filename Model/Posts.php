<?php

namespace Model {

    class Posts extends Model
    {
        public int $id;
        public string $title;
        public string $content;
        public string $description;
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
              'date_created' => 
              array (
                'default' => NULL,
                'null' => 'YES',
                'DATA_TYPE' => 'timestamp',
                'extra' => '',
                'table_reference' => NULL,
                'column_reference' => NULL,
                'trc' => NULL,
                'param_type' => 's',
              ),
              'date_updated' => 
              array (
                'default' => 'CURRENT_TIMESTAMP',
                'null' => 'YES',
                'DATA_TYPE' => 'timestamp',
                'extra' => 'DEFAULT_GENERATED on update CURRENT_TIMESTAMP',
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
            parent::__construct('posts');
        }
    }
}