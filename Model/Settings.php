<?php

namespace Model {

    class Settings extends Model
    {
        public string $setting;
        public string $value;

        public array $schema = array (
              'setting' => 
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
              'value' => 
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
            );
        
        public function __construct() {
            parent::__construct('settings');
        }
    }
}