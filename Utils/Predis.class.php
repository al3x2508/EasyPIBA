<?php
namespace Utils;
use Predis\Client;

require_once dirname(__FILE__) . "/predis/autoload.php";
/**
 * Class Predis
 * @package Utils
 */
class Predis extends Client {
	/**
	 * @var Predis|null
	 */
	private static $instance = null;

	/**
	 * Predis constructor.
	 */
	public function __construct() {
		parent::__construct();
		try {
			parent::connect();
		}
		catch (\Predis\Connection\ConnectionException $e) {
			return false;
		}
		return $this;
	}
	/**
	 * @return Client|null
	 */
	public static function getInstance() {
		if (self::$instance == null) self::$instance = new self();
		return self::$instance;
	}
}
