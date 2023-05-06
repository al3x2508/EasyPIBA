<?php

namespace Controller;

use Model\Model;

/**
 * Class AdminPage
 *
 * @package Controller
 */
abstract class AdminPage
{
    /**
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $this->$key = $value;
    }

    /**
     * @param $pagename
     *
     * @return bool|object
     */
    public static function getCurrentModule($pagename)
    {
        if (!empty($pagename)) {
            $mAR = new Model('module_admin_routes');
            $mAR = $mAR->getOneResult('url', $pagename);
            if ($mAR) {
                $admins_permissions = new Model('admins_permissions');
                $admins_permissions->admin
                    = AdminController::getCurrentUser()->id;
                $admins_permissions->permission = $mAR->permission;
                if (count($admins_permissions->get())) {
                    $class = 'Module\\'.$mAR->modules->name
                        .'\\Admin\\AdminPage';
                    return new $class();
                }
            }
        }
        return false;
    }

    /**
     * @param $link
     * @param $page_name
     *
     * @return string
     */
    public static function createLink($link, $page_name)
    {
        $isOpen = false;
        $href = $link['href'];
        if ($link['href'] == $page_name || arrayKeyExists('submenu', $link)) {
            if ($link['href'] == $page_name) {
                $isOpen = true;
                $href = '#';
            }
            if (arrayKeyExists('submenu', $link)) {
                foreach ($link['submenu'] as $submenu) {
                    if ($submenu['href'] == $page_name) {
                        $isOpen = true;
                    }
                }
            }
        }
        $ret = '<li class="sidebar-item'.($isOpen ? ' active' : '').'">
            <a href="'.$href.'"'.(arrayKeyExists('submenu', $link)
                ? ' data-bs-target="#'.$link['text']
                .'" data-bs-toggle="collapse"' : '').' class="sidebar-link'
            .($isOpen
                ? '' : ' collapsed')
            .'">
                <i class="align-middle fa fa-'.$link['class'].'"></i>
                <span class="align-middle">'.$link['text'].'</span>
            </a>'.PHP_EOL;
        if (arrayKeyExists('submenu', $link)) {
            $isActive = ($link['href'] === $page_name);
            $hclass = $link['class'];
            $ret .= '<ul id="'.$link['text']
                .'" class="sidebar-dropdown list-unstyled collapse'.($isActive
                    ? ' show' : '').'">
                        <li class="sidebar-item">
                            <a href="'.$link['href'].'" class="sidebar-link'
                .($isActive ? ' active' : '').'">
                                <span class="align-middle">'.$link['text'].'</span>
                            </a>'.PHP_EOL;
            foreach ($link['submenu'] as $submenu) {
                $isActive = ($submenu['href'] == $page_name);
                $ret .= '<li class="sidebar-item">
                        <a href="'.$submenu['href'].'" class="sidebar-link'
                    .($isActive ? ' active' : '').'">
                            <span class="align-middle">'.$submenu['text'].'</span>
                        </a>'.PHP_EOL;
            }
            $ret .= '</ul>'.PHP_EOL;
        }
        $ret .= '</li>'.PHP_EOL;
        return $ret;
    }

    public static function jsonOptions($array)
    {
        return json_encode($array, JSON_FORCE_OBJECT);
    }
}
