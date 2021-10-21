<?php

namespace Module\Pages\Admin;

use Controller\AdminAct;
use Model\Model;
use Module\Pages\Setup;
use Util;

require_once(dirname(__FILE__, 4) . '/Utils/Util.php');

class Act extends AdminAct
{
    public static $PERMISSION = Setup::PERMISSION;
    public static $ENTITY = Setup::ENTITY;

    public function __construct($id)
    {
        $act = false;
        if ($this->hasAccess()) {
            if (!arrayKeyExists('menu', $_REQUEST)) {
                parent::__construct($id);
            } else {
                $act = $this->menu();
            }
        }
        $this->sendStatus($act);
    }

    public function post_create_hook()
    {
        $this->addToCache();
    }

    public function post_update_hook()
    {
        $this->addToCache();
    }

    public function pre_delete_hook()
    {
        $cache = Util::getCache();
        if ($cache) {
            $page = new Model('pages');
            $page = $page->getOneResult('id', $this->fields['id']);
            $url = $page->url;
            $language = $page->language;
            $cacheKey = _CACHE_PREFIX_ . $url . '|' . $language;
            if ($cache->exists($cacheKey)) {
                $cache->del($cacheKey);
            }
            $cacheKey = _CACHE_PREFIX_ . 'output|' . $language . '|' . md5($url);
            if ($cache->exists($cacheKey)) {
                $cache->del($cacheKey);
            }
        }
    }

    public function menu(): bool
    {
        $order = 0;
        $lastParent = 0;
        $menu = array();
        foreach ($_REQUEST['menu'] as $item) {
            if (!empty($item['item_id'])) {
                $currentParent = (!empty($item['parent_id'])) ? $item['parent_id'] : 0;
                if ($currentParent == $lastParent) {
                    $order++;
                } else {
                    $order = 1;
                    $lastParent = $currentParent;
                }
                $menu[$item['item_id']] = array('order' => $order, 'parent' => $currentParent);
            }
        }
        if (count($menu)) {
            $this->actMenu($menu, $_REQUEST['language']);
        } else {
            $this->actMenu(false, $_REQUEST['language']);
        }
        return true;
    }

    /**
     * @param bool|array $menu
     * @param $language
     */
    public function actMenu($menu, $language)
    {
        $data = array();
        if (!$menu) {
            $sql = 'UPDATE pages SET menu_order = 0';
        } else {
            $menuOrder = ' menu_order = CASE id' . PHP_EOL;
            $menuParent = 'menu_parent = CASE id' . PHP_EOL;
            foreach ($menu as $id => $value) {
                $menuOrder .= 'WHEN ' . $id . ' THEN ' . $value['order'] . PHP_EOL;
                $menuParent .= 'WHEN ' . $id . ' THEN ' . $value['parent'] . PHP_EOL;
            }
            $menuOrder .= ' ELSE 0' . PHP_EOL;
            $menuParent .= ' ELSE 0' . PHP_EOL;
            $sql = 'UPDATE pages SET' . $menuOrder . 'END, ' . $menuParent . 'END';
        }
        if (!empty($language)) {
            $paramType = 's';
            $sql .= ' WHERE language = ?';
            $data = array(&$paramType, &$language);
        }
        $pages = new Model('pages');
        $pages->runQuery($sql, $data, false);
    }

    private function addToCache()
    {
        $cache = Util::getCache();
        if ($cache) {
            $url = $this->fields['url'];
            $language = $this->fields['language'];
            $cacheKey = _CACHE_PREFIX_ . $url . '|' . $language;
            $cache->set($cacheKey, json_encode($this->fields));
        }
    }
}