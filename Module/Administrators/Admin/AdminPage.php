<?php

namespace Module\Administrators\Admin;

class AdminPage extends \Controller\AdminPage
{
    public function output($filename)
    {
        switch ($filename) {
            case 'administrators':
                return new Admins();
            case 'cache':
                return new Cache();
            default:
                break;
        }
        return false;
    }
}
