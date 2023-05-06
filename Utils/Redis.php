<?php

namespace Utils;

class Redis
{
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance == null) {
            if (extension_loaded('redis')) {
                self::$instance = new \Redis();
                try {
                    self::$instance->connect('127.0.0.1');
                } catch (\Exception $e) {
                    self::$instance = false;
                }
            } else {
                self::$instance = false;
            }
        }
        return self::$instance;
    }
}