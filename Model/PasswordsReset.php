<?php

namespace Model {

    class PasswordsReset extends Model
    {
        public int $user;
        public string $code;

        public array $schema = array (
              'user' => 
              array (
                'default' => NULL,
                'null' => 'NO',
                'DATA_TYPE' => 'int',
                'extra' => '',
                'table_reference' => 'users',
                'column_reference' => 'id',
                'trc' => 
                array (
                  0 => 'address',
                  1 => 'city',
                  2 => 'country',
                  3 => 'email',
                  4 => 'firstname',
                  5 => 'id',
                  6 => 'lastname',
                  7 => 'password',
                  8 => 'phone',
                  9 => 'settings',
                  10 => 'state',
                  11 => 'status',
                ),
                'param_type' => 'i',
              ),
              'code' => 
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
              'expiration_date' => 
              array (
                'default' => 'CURRENT_TIMESTAMP',
                'null' => 'NO',
                'DATA_TYPE' => 'timestamp',
                'extra' => 'DEFAULT_GENERATED',
                'table_reference' => NULL,
                'column_reference' => NULL,
                'trc' => NULL,
                'param_type' => 's',
              ),
            );
        
        public function __construct() {
            parent::__construct('passwords_reset');
        }
    }
}