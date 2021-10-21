<?php

use Model\Model;

function reread()
{
    require_once dirname(__FILE__, 2) . '/Utils/Util.php';
    $modules = new Model('modules');
    //This where is necessary, because otherwise without a where parameter delete() call will not complete to prevent data erasing by mistake
    $modules->where(array('id' => array(0, ' > ', 'i')));
    $modules->delete();
    $objects = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(dirname(__FILE__, 2) . '/Module/'), RecursiveIteratorIterator::SELF_FIRST
    );
    $matches = new RegexIterator($objects, '/^.+\/Setup\.class\.php$/i', RegexIterator::GET_MATCH);
    $files = array_keys(iterator_to_array($matches));
    sort($files);
    foreach ($files as $filename) {
        $moduleName = basename(dirname($filename));
        if ($moduleName != 'Module') {
            $modules->name = $moduleName;
            $moduleConfigFile = _APP_DIR_ . 'Module/' . $moduleName . '/Admin/admin.json';
            if (file_exists($moduleConfigFile)) {
                $moduleConfig = json_decode(file_get_contents($moduleConfigFile));
                if (property_exists($moduleConfig, 'sort')) {
                    $modules->sort = $moduleConfig->sort;
                }
            }
            $modules->create();
            unset($modules->name);
            if (property_exists($modules, 'sort')) {
                unset($modules->sort);
            }
            $moduleName = 'Module\\' . $moduleName . '\\Setup';
            new $moduleName();
        }
    }
}