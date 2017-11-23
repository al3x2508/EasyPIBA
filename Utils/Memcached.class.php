<?php
namespace Utils;
/**
 * Class Memcached
 * @package Utils
 */
class Memcached extends \Memcached {
	/**
	 * @var Memcached|null
	 */
	private static $instance = null;

	/**
	 * Memcached constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->addServer('127.0.0.1', 11211);
	}
	/**
	 * @return Memcached|null
	 */
	public static function getInstance() {
		if (self::$instance == null) self::$instance = new self();
		return self::$instance;
	}
}
