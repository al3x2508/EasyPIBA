<?php
function reread()
{
    require_once dirname(dirname(__FILE__)) . '/Utils/functions.php';
    $modules = new \Model\Model('modules');
    //This where is necessary, because otherwise without a where parameter the delete() call will not complete to prevent data erasing by mistake
    $modules->where(array('id' => array(0, ' > ', 'i')));
    $modules->delete();
    $objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(dirname(dirname(__FILE__)) . '/Module/'),
        \RecursiveIteratorIterator::SELF_FIRST);
    $matches = new \RegexIterator($objects, '/^.+\/Setup\.class\.php$/i', \RecursiveRegexIterator::GET_MATCH);
    $files = array_keys(iterator_to_array($matches));
    sort($files);
    foreach ($files as $filename) {
        $moduleName = basename(dirname($filename));
        if ($moduleName != 'Module') {
            $modules->name = $moduleName;
            $modules->create();
            $moduleName = 'Module\\' . $moduleName . '\\Setup';
            new $moduleName();
        }
    }
}