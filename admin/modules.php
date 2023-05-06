<?php

use Model\Model;
use Utils\Util;

function reread() {
	require_once dirname(__FILE__, 2) . '/Utils/functions.php';
	$modules = new Model('modules');
	//This where is necessary, because otherwise without a where parameter the delete() call will not complete to prevent data erasing by mistake
	$modules->where(array('id' => array(0, ' > ', 'i')));
	$modules->delete();
	$objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(dirname(__FILE__, 2) . '/Module/'), \RecursiveIteratorIterator::SELF_FIRST);
	$matches = new \RegexIterator($objects, '/^.+\/Setup\.php$/i', \RecursiveRegexIterator::GET_MATCH);
	$files = array_keys(iterator_to_array($matches));
	sort($files);
	foreach ($files as $filename) {
		$moduleName = basename(dirname($filename));
		if($moduleName != 'Module') {
			$modules->name = $moduleName;
			$modules->create();
			$moduleName = 'Module\\' . $moduleName . '\\Setup';
			new $moduleName();
		}
	}
	$cache = Util::getCache();
//	if($cache) exec('redis-cli --scan --pattern "' . $_ENV['CACHE_PREFIX'] . '*" | xargs -L 500 redis-cli DEL');
}