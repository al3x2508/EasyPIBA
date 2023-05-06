<?php

namespace Model {

    class Media extends Model
    {
        public int $id;
        public string $filename;

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
              'filename' => 
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
            );
        
        public function __construct() {
            parent::__construct('media');
        }
    }
}