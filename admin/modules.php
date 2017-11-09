<?php
if (php_sapi_name() == "cli") {
	if($argv[1] == 'reread') {
		require_once dirname(dirname(__FILE__)) . '/Utils/functions.php';
		$modules = new \Model\Model('modules');
		$modules->where(array('id' => array(0, ' > ', 'i')));
		$modules->delete();
		$objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(dirname(dirname(__FILE__))), \RecursiveIteratorIterator::SELF_FIRST);
		$Regex = new \RegexIterator($objects, '/^.+\/Page\.class\.php$/i', \RecursiveRegexIterator::GET_MATCH);
		foreach ($Regex as $filename => $object) {
			$class = basename(dirname($filename));
			if(!in_array($class, array('JSON', 'Sitemap'))) {
				$modules->name = $class;
				$modules->has_frontend = 1;
				$modules->create();
			}
		}
		$objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(dirname(dirname(__FILE__)) . '/Module'), \RecursiveIteratorIterator::SELF_FIRST);
		$Regex = new \RegexIterator($objects, '/^.+\/Admin\/AdminPage\.class\.php$/i', \RecursiveRegexIterator::GET_MATCH);
		foreach ($Regex as $filename => $object) {
			$class = basename(dirname(dirname($filename)));
			$module = $modules->getOneResult('name', $class);
			if(!$module) {
				$modules->name = $class;
				$modules->has_backend = 1;
				$modules->create();
			}
			else {
				$module->has_backend = 1;
				$module->update();
			}
		}
	}
}