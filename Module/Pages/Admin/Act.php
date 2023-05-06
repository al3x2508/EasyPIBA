<?php

namespace Module\Pages\Admin;

use Controller\AdminAct;
use Controller\EntityException;
use Model\Model;
use Utils\Util;

require_once(dirname(__FILE__, 4).'/Utils/functions.php');

class Act extends AdminAct
{
    public function __construct($id = false)
    {
        $this->permission = 'Edit pages';
        $this->entity = new Model('pages');
        $cache = Util::getCache();

        switch (strtolower($_SERVER['REQUEST_METHOD'])) {
            //Create page
            case 'post':
                if (arrayKeyExists('menu', $_POST)) {
                    $this->menu();
                    break;
                }
                parent::__construct($id);
                $this->response = $this->create();

                if ($cache) {
                    $md5url = md5($this->response->url);
                    $language = $this->response->language;
                    $cacheKey = $_ENV['CACHE_PREFIX'].$md5url.'|'.$language;
                    $cache->set($cacheKey, json_encode($this->response));
                }
                break;
            //Update page
            case 'patch':
                parse_str(file_get_contents('php://input'), $_PATCH);

                if ($cache) {
                    $page = new Model('pages');
                    $page = $page->getOneResult('id', $id);
                    $md5url = md5($page->url);
                    $language = $page->language;
                    $cacheKey = $_ENV['CACHE_PREFIX'].$md5url.'|'.$language;
                    if ($cache->exists($cacheKey)) {
                        $cache->del($cacheKey);
                    }
                    $cacheKey = $_ENV['CACHE_PREFIX'].'output|'.$language.'|'
                        .$md5url;
                    if ($cache->exists($cacheKey)) {
                        $cache->del($cacheKey);
                    }
                }

                parent::__construct($id);
                try {
                    $this->response = $this->update();

                    if ($cache) {
                        $md5url = md5($this->response->url);
                        $language = $this->response->language;
                        $cacheKey = $_ENV['CACHE_PREFIX'].$md5url.'|'.$language;
                        $cache->set($cacheKey, json_encode($this->response));
                    }
                } catch (EntityException $e) {
                    self::throwError(400, $e->getMessage());
                }
                break;
            //Delete page
            case 'delete':
                if ($cache) {
                    $page = new Model('pages');
                    $page = $page->getOneResult('id', $id);
                    $md5url = md5($page->url);
                    $language = $page->language;
                    $cacheKey = $_ENV['CACHE_PREFIX'].$md5url.'|'.$language;
                    if ($cache->exists($cacheKey)) {
                        $cache->del($cacheKey);
                    }
                    $cacheKey = $_ENV['CACHE_PREFIX'].'output|'.$language.'|'
                        .$md5url;
                    if ($cache->exists($cacheKey)) {
                        $cache->del($cacheKey);
                    }
                }

                parent::__construct($id);
                try {
                    $this->response = $this->delete();
                } catch (EntityException $e) {
                    self::throwError(400, $e->getMessage());
                }
                break;
            default:
                break;
        }
    }

    private function menu()
    {
        $order = 0;
        $lastParent = 0;
        $menu = array();
        foreach ($_REQUEST['menu'] as $item) {
            if (!empty($item['item_id'])) {
                $currentParent = (!empty($item['parent_id']))
                    ? $item['parent_id'] : 0;
                if ($currentParent == $lastParent) {
                    $order++;
                } else {
                    $order = 1;
                    $lastParent = $currentParent;
                }
                $menu[$item['item_id']] = array(
                    'order'  => $order,
                    'parent' => $currentParent
                );
            }
        }
        if (count($menu)) {
            $this->actMenu($menu, $_REQUEST['language']);
        } else {
            $this->actMenu(false, $_REQUEST['language']);
        }
    }

    /**
     * @param  bool|array  $menu
     * @param              $language
     */
    private function actMenu($menu, $language)
    {
        $data = array();
        if (!$menu) {
            $sql = 'UPDATE `pages` SET `menu_order` = 0';
        } else {
            $menuOrder = ' menu_order = CASE id'.PHP_EOL;
            $menuParent = 'menu_parent = CASE id'.PHP_EOL;
            foreach ($menu as $id => $value) {
                $menuOrder .= 'WHEN '.$id.' THEN '.$value['order'].PHP_EOL;
                $menuParent .= 'WHEN '.$id.' THEN '.$value['parent'].PHP_EOL;
            }
            $menuOrder .= ' ELSE 0'.PHP_EOL;
            $menuParent .= ' ELSE 0'.PHP_EOL;
            $sql = 'UPDATE pages SET'.$menuOrder.'END, '.$menuParent.'END';
        }
        if (!empty($language)) {
            $paramType = 's';
            $sql .= ' WHERE language = ?';
            $data = array(&$paramType, &$language);
        }
        $pages = new Model('pages');
        $pages->runQuery($sql, $data, false);
    }
}
