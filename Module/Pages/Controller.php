<?php

namespace Module\Pages;

use Model\Model;
use Module\JSON\Page;
use Utils\Util;
use Module\Sitemap\Page as Sitemap;
use Module\Users\Controller as UserController;

class Controller
{
    public $mustBeLoggedIn = false;
    public $url = '';
    public $language = '';
    public $title = false;
    public $content = false;
    public $description = '';
    public $keywords = '';
    public $ogimage = '';
    public $h1 = '';
    public $breadcrumbs = array();
    public $sidebar = array();
    public $rsidebar = false;
    public $js = array();
    public $css = array();
    public $visible = true;
    public $template = 'template.html';

    /**
     * Pages constructor.
     *
     * @param          $url
     * @param  string  $language
     */
    public function __construct($url, $language)
    {
        $cache = Util::getCache();
        if (strpos($url, 'amp/') === 0) {
            $url = str_replace('amp/', '', $url);
            $this->template = 'amp_template.html';
            $this->HTML_URL = $_ENV['FOLDER_URL'].$url;
        } else {
            $this->AMP_META = '<link rel="amphtml" href="'.$_ENV['FOLDER_URL']
                .'amp/'.$url.'" />';
        }
        $this->url = $url;
        $md5url = md5($url);
        $this->language = $language;
        $cacheKey = $_ENV['CACHE_PREFIX'].$md5url.'|'.$language;
        $page = false;
        if ($cache && $cache->exists($cacheKey)) {
            $page = json_decode($cache->get($cacheKey));
            if (arrayKeyExists('visible', $page) && !$page->visible) {
                $page = false;
            }
            if (arrayKeyExists('status', $page) && !$page->status) {
                $page = false;
            }
        }
        if (!$page) {
            $page = new Model('pages');
            $page->language = $language;
            $page->url = $url;
            $page->visible = 1;
            $page = $page->get();
            if (count($page)) {
                $page = $page[0];
                if ($cache) {
                    $cache->set($cacheKey, json_encode($page));
                }
            } else {
                $page = false;
            }
        }
        if ($page) {
            $jsArray = [];
            $cssArray = [];
            if (!empty($page->js)) {
                $explodedJs = explode(",", $page->js);
                foreach ($explodedJs as $expJs) {
                    $jsArray[] = trim($expJs);
                }
                $page->js = implode(",", $jsArray);
            }
            if (!empty($page->css)) {
                $explodedCss = explode(",", $page->css);
                foreach ($explodedCss as $expCss) {
                    $cssArray[] = trim($expCss);
                }
                $page->css = implode(",", $cssArray);
            }
        } else {
            if (strpos($url, 'json/') === 0) {
                echo Page::output();
                exit;
            } elseif ($url == 'sitemap.xml') {
                echo Sitemap::output();
                exit;
            } else {
                $module_routes = new Model('module_routes');
                $module_routes->where(array(
                    '(`url` = \''.$module_routes->escape($url)
                    .'\' AND `type` = 0)'              => 1,
                    '(\''.$module_routes->escape($url)
                    .'\' REGEXP `url` AND `type` = 1)' => 1
                ));
                $module_routes->limit(1);
                $module_routes = $module_routes->get('OR');
                if (count($module_routes)) {
                    $class = 'Module\\'.$module_routes[0]->modules->name
                        .'\\Page';
                    $class = new $class();
                    if ($class && property_exists($class, 'useCache')
                        && $class->useCache
                        && $cache
                    ) {
                        $page = json_decode($cache->get($cacheKey));
                    }
                    if (!$page) {
                        $page = $class->output();
                        if ($page && property_exists($page, 'useCache')
                            && $page->useCache
                            && $cache
                        ) {
                            $cache->set($cacheKey, json_encode($page));
                        }
                    }
                }
            }
        }
        if ($page) {
            $page->disableAmp = true;
        }
        if ($page && property_exists($page, 'disableAmp')
            && $page->disableAmp
        ) {
            $this->AMP_META = '';
        }
        if ($page) {
            foreach (get_object_vars($page) as $key => $value) {
                $this->$key = $value;
            }
        } else {
            Util::pageNotFound();
            $this->content = '<div class="text-center">
                    <h1>'.__('Page not found')
                .' <span class="text-danger"><small>'.__('Error 404').'</small></span></h1>
                    <br />
                    <p>'
                .__('The page you requested could not be found, either contact your webmaster or try again. Use your browsers <strong>Back</strong> button to navigate to the page you have previously come from.')
                .'</p>
                    <p><strong>'
                .__('Or you could just press this neat little button').':</strong></p>
                    <a href="'.$_ENV['ADDRESS'].$_ENV['FOLDER_URL']
                .'" class="btn btn-large btn-info"><i class="icon-home icon-white"></i> '
                .__('Take Me Home').'</a>
                </div>';
            $this->title = __('Page not found');
            $this->description = __('Page not found');
        }
    }

    public static function getMenu() {
        $userLanguage = Util::getUserLanguage();
        $langUrl = ($userLanguage == $_ENV['DEFAULT_LANGUAGE']) ? '' : $userLanguage . '/';
        $pages = new Model('pages');
        $pages->language = $userLanguage;
        $pages->visible = 1;
        $pages->menu_order = array('0', '!=');
        $pages->order('menu_parent ASC, menu_order ASC');
        $array_pages = $pages->get();
        $array_menu = array();
        foreach($array_pages AS $page) {
            if(!arrayKeyExists($page->menu_parent, $array_menu)) $array_menu[$page->menu_parent] = array();
            $pag = array('id' => $page->id, 'url' => $_ENV['FOLDER_URL'] . $langUrl . $page->url, 'menu_text' => $page->menu_text, 'submenu_text' => $page->submenu_text, 'menu_parent' => $page->menu_parent);
            if($page->url == 'despre-noi') {
                $pagTestimoniale = array('id' => -1, 'url' => $_ENV['FOLDER_URL'] . $langUrl . 'testimonials', 'menu_text' => 'Testimonials', 'submenu_text' => '', 'menu_parent' => $page->id);
                if($_SERVER['REQUEST_URI'] == $pagTestimoniale['url']) $pagTestimoniale['classes'] = 'active';
                $array_menu[$page->id][] = $pagTestimoniale;

                /*$pagBlog = array('id' => -2, 'url' => $_ENV['FOLDER_URL'] . $langUrl . 'blog', 'menu_text' => 'Blog', 'submenu_text' => '', 'menu_parent' => $page->id);
                if($_SERVER['REQUEST_URI'] == $pagBlog['url']) $pagBlog['classes'] = 'active';
                $array_menu[$page->id][] = $pagBlog;*/
            }
            //If page url is the same as the current url set link class as active
            if($_SERVER['REQUEST_URI'] == $_ENV['FOLDER_URL'] . $langUrl . $page->url) $pag['classes'] = 'active';
            $array_menu[$page->menu_parent][] = $pag;
        }
        $module_routes = new Model('module_routes');
        $module_routes->type = 0;
        if(!UserController::getCurrentUser()) $module_routes->mustBeLoggedIn = 0;
        else $module_routes->hiddenForLoggedIn = 0;
        $module_routes->menu_position = array(0, '>');
        $module_routes->order('menu_position ASC, menu_parent ASC, menu_order ASC');
        $module_routes = $module_routes->get();
        foreach($module_routes AS $module_route) {
            if($module_route->menu_position !== 2) {
                if($userLanguage == $_ENV['DEFAULT_LANGUAGE']) {
                    $menuParent = (empty($module_route->menu_parent)) ? 0 : $module_route->menu_parent;
                    if($menuParent === 0) $module_route->menu_order += count($array_pages);
                    if(!arrayKeyExists($menuParent, $array_menu)) $array_menu[$menuParent] = array();
                    $pag = array('url' => $_ENV['FOLDER_URL'] . $langUrl . $module_route->url, 'menu_text' => $module_route->menu_text, 'submenu_text' => $module_route->submenu_text, 'menu_parent' => $menuParent);
                    //If page url is the same as the current url set link class as active
                    if($_SERVER['REQUEST_URI'] == $_ENV['FOLDER_URL'] . $langUrl . $module_route->url || $_SERVER['REQUEST_URI'] == $_ENV['FOLDER_URL'] . $langUrl . $module_route->url) $pag['classes'] = 'active';
                    $array_menu[$menuParent][$module_route->menu_order] = $pag;
                    ksort($array_menu[$menuParent]);
                }
            }
            else {
                $menuParent = (empty($module_route->menu_parent))?0:$module_route->menu_parent;
                if(!arrayKeyExists('menu_right', $array_menu)) $array_menu['menu_right'] = array();
                if(!arrayKeyExists($menuParent, $array_menu['menu_right'])) $array_menu['menu_right'][$menuParent] = array();
                $pag = array('url' => $_ENV['FOLDER_URL'] . $langUrl . $module_route->url, 'menu_text' => $module_route->menu_text, 'submenu_text' => $module_route->submenu_text, 'menu_parent' => $menuParent);
                $pag['id'] = $module_route->url;
                //If page url is the same as the current url set link class as active
                if($_SERVER['REQUEST_URI'] == $_ENV['FOLDER_URL'] . $langUrl . $module_route->url || $_SERVER['REQUEST_URI'] == $_ENV['FOLDER_URL'] . $langUrl . $module_route->url) $pag['classes'] = 'active';
                $array_menu['menu_right'][$menuParent][] = $pag;
            }
        }
        return $array_menu;
    }
}