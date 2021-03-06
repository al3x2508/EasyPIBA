<?php

namespace Utils;

/**
 * Class Memcached
 * @package Utils
 */
class Memcached extends \Memcached
{
    /**
     * @var Memcached|null
     */
    private static $instance = null;

    /**
     * Memcached constructor.
     */
    public function __construct()
    {
        if (extension_loaded('Memcached')) {
            try {
                parent::__construct();
                if ($this->addServer(_MEMCACHE_HOST_, _MEMCACHE_PORT_)) {
                    return $this;
                }
                return false;
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * @return Memcached|false
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @return bool
     */
    public function isConnected()
    {
        $statuses = $this->getStats();
        return (isset($statuses[_MEMCACHE_HOST_ . ":" . _MEMCACHE_PORT_]) && $statuses[_MEMCACHE_HOST_ . ":" . _MEMCACHE_PORT_]["pid"] > 0);
    }

    /**
     * Check if an item exists
     * @param string $key
     * @return bool
     */
    public function exists($key)
    {
        if ($this->get($key) && $this->getResultCode() == Memcached::RES_SUCCESS) {
            return true;
        }
        return false;
    }

    /**
     * Delete an item
     * @param string $key <p>
     * The key to be deleted.
     * </p>
     * @return bool
     */
    public function del($key)
    {
        return $this->delete($key);
    }
}
