<?php

namespace Module\Pages;

use Model\Model;
use Module\JSON\Page;
use Module\Sitemap\Page as Sitemap;
use Module\Users\Controller as UserController;
use Util;

class Controller
{
    public $mustBeLoggedIn = false;
    public $url = '';
    public $language = '';
    public $title = false;
    public $content = false;
    public $description = '';
    public $ogimage = '';
    public $breadcrumbs = array();
    public $sidebar = array();
    public $rsidebar = false;
    public $js = array();
    public $css = array();
    public $visible = true;
    public $darkHeader = true;
    public $template = 'template.html';

    /**
     * Pages constructor.
     * @param $url
     * @param string $language
     */
    public function __construct($url, $language = _DEFAULT_LANGUAGE_)
    {
        $cache = Util::getCache();
        $url = filter_var($url, FILTER_SANITIZE_STRING);
        if (strpos($url, 'amp/') === 0) {
            $url = str_replace('amp/', '', $url);
            $this->template = 'amp_template.html';
            $this->HTML_URL = _FOLDER_URL_ . $url;
        } else {
            $this->AMP_META = '<link rel="amphtml" href="' . _FOLDER_URL_ . 'amp/' . $url . '" />';
        }
        $this->url = $url;
        $this->language = $language;
        $cacheKey = _CACHE_PREFIX_ . $url . '|' . $language;
        if ($cache && $cache->exists($cacheKey)) {
            $page = json_decode($cache->get($cacheKey));
            $user = UserController::getCurrentUser(false);
            if ($user && !$page->forceFrontEnd) {
                $page->adminName = $user->firstname . ' ' . $user->lastname;
                $page->myaccountlink = '<a href="' . _FOLDER_URL_ . 'my-account" class="btn btn-sm btn-default btn-flat"><i class="fa fa-user"></i> ' . __(
                        'My account'
                    ) . '</a>';
                $page->logoutlink = '<a href="' . _FOLDER_URL_ . 'logout" class="btn btn-sm btn-default btn-flat"><i class="fa fa-power-off"></i> ' . __(
                        'Logout'
                    ) . '</a>';
                $page->ADMIN_FOLDER_URL = _FOLDER_URL_ . 'admin1009/';
                $page->template = '../admin1009/template.html';
                $page->noMainCss = true;
            } else {
                $page->adminName = __('Guest user');
                $page->myaccountlink = '';
                $page->logoutlink = '<a href="' . _FOLDER_URL_ . 'login" class="btn btn-sm btn-default btn-flat"><i class="fa fa-power-on"></i> ' . __(
                        'Login'
                    ) . '</a>';
            }
        } else {
            $page = new Model('pages');
            $page->language = $language;
            $page->url = $url;
            $page->visible = 1;
            $page = $page->get();
            if (count($page)) {
                $page = $page[0];
                $page->forceFrontEnd = true;
                if ($cache) {
                    $cache->set($cacheKey, json_encode($page));
                }
            } else {
                $page = false;
            }
        }
        if ($page) {
            if (!empty($page->js)) {
                $explodedJs = explode(",", $page->js);
                $page->js = array();
                foreach ($explodedJs as $expJs) {
                    $page->js[] = trim($expJs);
                }
            } else {
                $page->js = array();
            }
            if (!empty($page->css)) {
                $explodedCss = explode(",", $page->css);
                $page->css = array();
                foreach ($explodedCss as $expCss) {
                    $page->css[] = trim($expCss);
                }
            } else {
                $page->css = array();
            }
        } else {
            if (strpos($url, 'json') === 0) {
                echo Page::output();
                exit;
            } elseif ($url == 'sitemap.xml') {
                echo Sitemap::output();
                exit;
            } else {
                $module_routes = new Model('module_routes');
                $module_routes->where(
                    array(
                        '(`url` = \'' . $module_routes->escape($url) . '\' AND `type` = 0)' => 1,
                        '(\'' . $module_routes->escape($url) . '\' REGEXP `url` AND `type` = 1)' => 1
                    )
                );
                $module_routes->limit(1);
                $module_routes = $module_routes->get('OR');

                if (count($module_routes)) {
                    $modules = new Model('modules');
                    $modules->where(array('(`id` = \'' . $module_routes[0]->module . '\')' => 1));
                    $modules->limit(1);
                    $modules = $modules->get();

                    if (count($modules)) {
                        $class = 'Module\\' . $modules[0]->name . '\\Page';
                        $class = new $class();
                        if ($class && property_exists($class, 'useCache') && $class->useCache && $cache) {
                            $page = json_decode($cache->get($cacheKey));
                        }
                        if (!$page) {
                            $page = $class->output();
                            if ($page && property_exists($page, 'useCache') && $page->useCache && $cache) {
                                $cache->set($cacheKey, json_encode($page));
                            }
                        }
                    }
                }
            }
        }
        if ($page && property_exists($page, 'disableAmp') && $page->disableAmp) {
            $this->AMP_META = '';
        }
        if ($page) {
            foreach (get_object_vars($page) as $key => $value) {
                $this->$key = $value;
            }
        } else {
            header('HTTP/1.0 404 Not Found');
            $this->content = '<div class="text-center">
					<h1>' . __('Page not found') . ' <span class="text-danger"><small>' . __('Error 404') . '</small></span></h1>
					<br />
					<p>' . __(
                    'The page you requested could not be found, either contact your webmaster or try again. Use your browsers <strong>Back</strong> button to navigate to the page you have previously come from.'
                ) . '</p>
					<p><strong>' . __('Or you could just press this neat little button') . ':</strong></p>
					<a href="' . _ADDRESS_ . _FOLDER_URL_ . '" class="btn btn-large btn-info"><i class="icon-home icon-white"></i> ' . __(
                    'Take Me Home'
                ) . '</a>
				</div>';
            $this->title = __('Page not found');
            $this->description = __('Page not found');
        }
    }

    public static function getMenu($forceFrontEnd = false)
    {
        $userLanguage = Util::getUserLanguage();
        $langUrl = ($userLanguage == _DEFAULT_LANGUAGE_) ? '' : $userLanguage . '/';
        $pages = new Model('pages');
        $pages->language = $userLanguage;
        $pages->visible = 1;
        $pages->menu_order = array('0', '!=');
        $pages->order('menu_parent ASC, menu_order ASC');
        $array_pages = $pages->get();
        $array_menu = array();
        foreach ($array_pages as $page) {
            if (!arrayKeyExists($page->menu_parent, $array_menu)) {
                $array_menu[$page->menu_parent] = array();
            }
            $pag = array(
                'id' => $page->id,
                'url' => _FOLDER_URL_ . $langUrl . $page->url,
                'menu_text' => $page->menu_text,
                'submenu_text' => $page->submenu_text,
                'menu_parent' => $page->menu_parent
            );
            //If page url is the same as the current url set link class as active
            if ($_SERVER['REQUEST_URI'] == _FOLDER_URL_ . $langUrl . $page->url) {
                $pag['classes'] = 'active';
            }
            $array_menu[$page->menu_parent][] = $pag;
        }
        $module_routes = new Model('module_routes');
        $module_routes->type = 0;
        if (!UserController::getCurrentUser()) {
            $module_routes->mustBeLoggedIn = 0;
        } else {
            $module_routes->hiddenForLoggedIn = 0;
        }
        $module_routes->menu_position = array(0, '>');
        $module_routes->order('menu_position ASC, menu_parent ASC, menu_order ASC');
        $module_routes = $module_routes->get();
        foreach ($module_routes as $module_route) {
            if ($module_route->menu_position !== 2) {
                if ($userLanguage == _DEFAULT_LANGUAGE_) {
                    if ($forceFrontEnd && $module_route->mustBeLoggedIn) {
                        if (!arrayKeyExists('app', $array_menu[0])) {
                            $array_menu[0]['app'] = array(
                                'id' => 'app',
                                'url' => _FOLDER_URL_ . 'dashboard',
                                'menu_text' => __('App'),
                                'submenu_text' => __('Dashboard'),
                                'menu_parent' => 0
                            );
                        }
                        $menuParent = 'app';
                    } else {
                        $menuParent = (empty($module_route->menu_parent)) ? 0 : $module_route->menu_parent;
                    }
                    if ($menuParent === 0) {
                        $module_route->menu_order += count($array_pages);
                    }
                    if (!arrayKeyExists($menuParent, $array_menu)) {
                        $array_menu[$menuParent] = array();
                    }
                    if ($module_route->url != 'dashboard') {
                        $pag = array(
                            'url' => _FOLDER_URL_ . $langUrl . $module_route->url,
                            'menu_text' => $module_route->menu_text,
                            'submenu_text' => $module_route->submenu_text,
                            'menu_parent' => $menuParent
                        );
                        //If page url is the same as the current url set link class as active
                        if ($_SERVER['REQUEST_URI'] == _FOLDER_URL_ . $langUrl . $module_route->url || $_SERVER['REQUEST_URI'] == _FOLDER_URL_ . $langUrl . $module_route->url) {
                            $pag['classes'] = 'active';
                        }
                        $array_menu[$menuParent][$module_route->menu_order] = $pag;
                    }
                    ksort($array_menu[$menuParent]);
                }
            } else {
                $menuParent = (empty($module_route->menu_parent)) ? 0 : $module_route->menu_parent;
                if (!arrayKeyExists('menu_right', $array_menu)) {
                    $array_menu['menu_right'] = array();
                }
                if (!arrayKeyExists($menuParent, $array_menu['menu_right'])) {
                    $array_menu['menu_right'][$menuParent] = array();
                }
                $pag = array(
                    'url' => _FOLDER_URL_ . $langUrl . $module_route->url,
                    'menu_text' => $module_route->menu_text,
                    'submenu_text' => $module_route->submenu_text,
                    'menu_parent' => $menuParent
                );
                $pag['id'] = $module_route->url;
                //If page url is the same as the current url set link class as active
                if ($_SERVER['REQUEST_URI'] == _FOLDER_URL_ . $langUrl . $module_route->url || $_SERVER['REQUEST_URI'] == _FOLDER_URL_ . $langUrl . $module_route->url) {
                    $pag['classes'] = 'active';
                }
                $array_menu['menu_right'][$menuParent][] = $pag;
            }
        }
        return $array_menu;
    }
}