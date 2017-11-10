<?php
if (php_sapi_name() == "cli") {
	if($argv[1] == 'reread') {
		require_once dirname(dirname(__FILE__)) . '/Utils/functions.php';
		$modules = new \Model\Model('modules');
		//This where is necessary, because otherwise without a where parameter the delete() call will not complete to prevent data erasing by mistake
		$modules->where(array('id' => array(0, ' > ', 'i'), ''));
		$modules->delete();
		$objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(dirname(dirname(__FILE__))), \RecursiveIteratorIterator::SELF_FIRST);
		$Regex = new \RegexIterator($objects, '/^.+\/Setup\.class\.php$/i', \RecursiveRegexIterator::GET_MATCH);
		foreach ($Regex as $filename => $object) {
			$moduleName = basename(dirname($filename));
			if($moduleName != 'Module') {
				$modules->name = $moduleName;
				$modules->create();
				$moduleName = 'Module\\' . $moduleName . '\\Setup';
				$class = new $moduleName();
			}
		}
	}
}