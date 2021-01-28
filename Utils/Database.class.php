<?php
namespace Utils;

use mysqli;

/**
 * Class Database
 * @package Utils
 */
class Database extends mysqli
{
    /**
     * @var Database|null
     */
    private static $instance = null;

    /**
     * Database constructor.
     */
    public function __construct()
    {
        parent::__construct(_DB_HOST_, _DB_USER_, _DB_PASS_, _DB_NAME_, _DB_PORT_);
    }

    /**
     * @return Database|null
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        if (mysqli_connect_error()) {
            return null;
        }
        return self::$instance;
    }

    public function close() {
        parent::close();
        self::$instance = null;
    }
}
