<?php
if (php_sapi_name() == "cli") {
    require_once dirname(__FILE__) . '/Utils/functions.php';
    switch ($argv[1]) {
        case "modules:reread":
            require_once _ADMIN_FOLDER_ . '/modules.php';
            reread();
            break;
        case "buildcss":
            $buildCss = new \Utils\BuildInPageCSS($argv[2]);
            break;
        default:
            break;
    }
}