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
		try {
			parent::__construct();
			$this->addServer('127.0.0.1', 11211);
			return $this;
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * @return Memcached|false
	 */
	public static function getInstance() {
		if (extension_loaded('Memcached')) {
			try {
				if (self::$instance == null) self::$instance = new self();
				return self::$instance;
			} catch (\Exception $e) {
				return false;
			}
		}
		return false;
	}
}
