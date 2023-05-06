<?php

namespace Utils;

use mysqli;

/**
 * Class Database
 * @package Utils
 */
class Database extends mysqli
{
    private static ?Database $instance = null;

    /**
     * Database constructor.
     */
    public function __construct()
    {
        parent::__construct($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $_ENV['DB_NAME'], $_ENV['DB_PORT']);
    }

    public static function getInstance(): ?Database
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        if (mysqli_connect_error()) {
            return null;
        }
        return self::$instance;
    }
}
