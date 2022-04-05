<?php
if(php_sapi_name() == "cli") {
	require_once dirname(__FILE__) . '/Utils/functions.php';
	switch($argv[1]) {
		case "modules:reread":
			require_once dirname(__FILE__) . '/admin/modules.php';
			reread();
			break;
		default:
			break;
	}
}