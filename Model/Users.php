<?php

namespace Model {

    class Users extends Model
    {
        public int $id;
        public string $firstname;
        public string $lastname;
        public string $email;
        public ?string $phone;
        public ?string $address;
        public ?string $city;
        public ?string $state;
        public ?int $country;
        public string $password;
        public ?int $status;
        public ?string $settings;

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
              'firstname' => 
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
              'lastname' => 
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
                'null' => 'NO',
                'DATA_TYPE' => 'varchar',
                'extra' => '',
                'table_reference' => NULL,
                'column_reference' => NULL,
                'trc' => NULL,
                'param_type' => 's',
              ),
              'phone' => 
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
              'address' => 
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
              'city' => 
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
              'state' => 
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
              'country' => 
              array (
                'default' => '0',
                'null' => 'YES',
                'DATA_TYPE' => 'int',
                'extra' => '',
                'table_reference' => 'countries',
                'column_reference' => 'id',
                'trc' => 
                array (
                  0 => 'code',
                  1 => 'id',
                  2 => 'language_code',
                  3 => 'name',
                ),
                'param_type' => 'i',
              ),
              'password' => 
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
              'settings' => 
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
            parent::__construct('users');
        }
    }
}