<?php

stringEncode_Autoloader::register();
class stringEncode_Autoloader {
    /**
     * Register the Autoloader with SPL
     *
     */
    public static function register()
    {
        if (function_exists('__stringEncodeautoload')) {
            // Register any existing autoloader function with SPL, so we don't get any clashes
            spl_autoload_register('__stringEncodeautoload');
        }
        // Register ourselves with SPL
        if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
            return spl_autoload_register(array('stringEncode_Autoloader', 'load'), true, true);
        } else {
            return spl_autoload_register(array('stringEncode_Autoloader', 'load'));
        }
    }

    /**
     * Autoload a class identified by name
     *
     * @param    string    $pClassName        Name of the object to load
     */
	public static function load($pClassName)
	{
		if ((class_exists($pClassName, false)) || (strpos($pClassName, 'stringEncode') !== 0)) {
			// Either already loaded, or not a stringEncode class request
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
