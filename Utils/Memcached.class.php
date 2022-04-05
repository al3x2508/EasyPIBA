<?php

namespace Utils;

use Exception;
use Memcached as MemcachedAlias;

/**
 * Class Memcached
 * @package Utils
 */
class Memcached extends MemcachedAlias
{
    private static ?Memcached $instance = null;

    /**
     * Memcached constructor.
     */
    public function __construct()
    {
        try {
            parent::__construct();
            if ($this->addServer(_MEMCACHE_HOST_, _MEMCACHE_PORT_)) {
                return $this;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getInstance(): ?Memcached
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function isConnected(): bool
    {
        $statuses = $this->getStats();
        return (isset($statuses[_MEMCACHE_HOST_ . ":" . _MEMCACHE_PORT_]) && $statuses[_MEMCACHE_HOST_ . ":" . _MEMCACHE_PORT_]["pid"] > 0);
    }

    /**
     * Check if an item exists
     */
    public function exists(string $key): bool
    {
        if ($this->get($key) && $this->getResultCode() == MemcachedAlias::RES_SUCCESS) {
            return true;
        }
        return false;
    }

    /**
     * Delete an item by its key
     */
    public function del(string $key): bool
    {
        return $this->delete($key);
    }
}
