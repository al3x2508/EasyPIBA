<?php

namespace Model {

    class PostsComments extends Model
    {
        public int $id;
        public int $post;
        public string $username;
        public ?string $email;
        public string $content;
        public int $reply_to;
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
              'post' => 
              array (
                'default' => NULL,
                'null' => 'NO',
                'DATA_TYPE' => 'int',
                'extra' => '',
                'table_reference' => 'posts',
                'column_reference' => 'id',
                'trc' => 
                array (
                  0 => 'admin',
                  1 => 'content',
                  2 => 'date_created',
                  3 => 'date_updated',
                  4 => 'description',
                  5 => 'id',
                  6 => 'image',
                  7 => 'status',
                  8 => 'title',
                ),
                'param_type' => 'i',
              ),
              'username' => 
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
              'email' => 
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
              'date' => 
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
              'reply_to' => 
              array (
                'default' => NULL,
                'null' => 'NO',
                'DATA_TYPE' => 'int',
                'extra' => '',
                'table_reference' => NULL,
                'column_reference' => NULL,
                'trc' => NULL,
                'param_type' => 'i',
              ),
              'status' => 
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
            );
        
        public function __construct() {
            parent::__construct('posts_comments');
        }
    }
}