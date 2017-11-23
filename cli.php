<?php
if(php_sapi_name() == "cli") {
	if($argv[1] == "modules:reread") {
		require_once dirname(__FILE__) . '/admin/modules.php';
		reread();
	}
}