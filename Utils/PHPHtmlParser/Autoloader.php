<?php

PHPHtmlParser_Autoloader::register();
class PHPHtmlParser_Autoloader {
    /**
     * Register the Autoloader with SPL
     *
     */
    public static function register()
    {
        if (function_exists('__phphtmlparserautoload')) {
            // Register any existing autoloader function with SPL, so we don't get any clashes
            spl_autoload_register('__phphtmlparserautoload');
        }
        // Register ourselves with SPL
        if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
            return spl_autoload_register(array('PHPHtmlParser_Autoloader', 'load'), true, true);
        } else {
            return spl_autoload_register(array('PHPHtmlParser_Autoloader', 'load'));
        }
    }

    /**
     * Autoload a class identified by name
     *
     * @param    string    $pClassName        Name of the object to load
     */
    public static function load($pClassName)
    {
        if ((class_exists($pClassName, false)) || (strpos($pClassName, 'PHPHtmlParser') !== 0)) {
            // Either already loaded, or not a PHPHtmlParser class request
            return false;
        }

        $pClassFilePath = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR .
            str_replace('\\', DIRECTORY_SEPARATOR, str_replace('_', DIRECTORY_SEPARATOR, $pClassName)) .
            '.php';

        if ((file_exists($pClassFilePath) === false) || (is_readable($pClassFilePath) === false)) {
            // Can't load
            return false;
        }

        require($pClassFilePath);
    }
}
