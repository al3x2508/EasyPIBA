<?php

use Controller\AdminController;
use Controller\AdminPage;
use Model\Model;

/**
 * @property string h1
 * @property string title
 * @property stdClass page
 * @property array js
 * @property array css
 * @property string APP_NAME
 * Class Template
 */
class Template
{
    /**
     * @var
     */
    protected $filename;
    /**
     * @var string
     */
    private $template = '';
    /**
     * @var string
     */
    private $adminName;
    private $page_name;
    private $currentUrl;
    private $copyright;

    /**
     * Template constructor.
     * @param string $filename
     * @param string $adminName
     * @param string $url
     */
    public function __construct(string $filename, string $adminName = '', string $url = '')
    {
        $this->filename = $filename;
        $this->adminName = $adminName;
        $query_position = ($_SERVER['QUERY_STRING'] != '') ? strpos(
            $_SERVER['REQUEST_URI'],
            $_SERVER['QUERY_STRING']
        ) : false;
        $page_url = ($query_position !== false) ? trim(
            substr($_SERVER['REQUEST_URI'], 0, $query_position - 1),
            '/'
        ) : trim($_SERVER['REQUEST_URI'], '/');
        $this->page_name = str_replace(
            array(basename(dirname(__FILE__)) . DIRECTORY_SEPARATOR),
            '',
            trim($page_url, '/')
        );
        $this->currentUrl = $url;
        $this->copyright = '';
    }

    /**
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $this->$key = $value;
    }

    public function output($start_time)
    {
        $this->loadTemplate();
        $end_time = microtime();
        $end_time = explode(' ', $end_time);
        $end_time = $end_time[1] + $end_time[0];
        $total_time = round(($end_time - $start_time), 4);
        $this->template = str_replace('{loading_time}', $total_time . ' ' . __('second', 'seconds', $total_time), $this->template);
        echo $this->template;
    }

    public function loadTemplate()
    {
        if (!file_exists($this->filename) || is_dir($this->filename)) {
            die("Error loading template ($this->filename).");
        }
        $this->template = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->filename);
        $userLanguage = Util::getUserLanguage();
        $this->LANGUAGE = $userLanguage;
        $this->myaccountlink = '';
        $this->logoutlink = '<a href="' . _FOLDER_URL_ . 'logout" class="btn btn-sm btn-default btn-flat"><i class="fa fa-power-off"></i> ' . __(
                'Logout'
            ) . '</a>';
        $this->loading_time_string = __('Loading time');

        $this->changeTemplateVars(get_object_vars($this));
        $this->changeTemplateVars(get_defined_constants(true)['user']);
        if (property_exists($this->page, 'css') && count($this->page->css)) {
            $replacement = '';
            foreach ($this->page->css as $script) {
                if (strpos($script, _FOLDER_URL_) === false || strpos($script, _FOLDER_URL_) != 0) {
                    $scriptFolder = (strpos($script, 'Module/') !== 0) ? _ADMIN_FOLDER_ : _FOLDER_URL_;
                } else {
                    $scriptFolder = '';
                }
                $replacement .= '		<link rel="stylesheet" href="' . $scriptFolder . $script . '" />' . PHP_EOL;
            }
            $pos = strripos($this->template, "</body>");
            $this->template = substr_replace($this->template, $replacement, $pos, 0);
        }
        if (property_exists($this->page, 'js') && count($this->page->js)) {
            $replacement = '';
            foreach ($this->page->js as $script) {
                if (strpos($script, 'http') !== 0 && (strpos($script, _FOLDER_URL_) === false || strpos($script, _FOLDER_URL_) != 0)) {
                    $scriptFolder = (strpos($script, 'Module/') !== 0) ? _ADMIN_FOLDER_ : _FOLDER_URL_;
                } else {
                    $scriptFolder = '';
                }
                $replacement .= '		<script src="' . $scriptFolder . $script . '"' . (strpos($script, 'http') === 0?' crossorigin':'') . '></script>' . PHP_EOL;
            }
            $pos = strripos($this->template, "</body>");
            $this->template = substr_replace($this->template, $replacement, $pos, 0);
        }
        if (property_exists($this->page, 'module') && count($this->page->module)) {
            $replacement = '';
            foreach ($this->page->module as $script) {
                if (strpos($script, 'http') !== 0 && (strpos($script, _FOLDER_URL_) === false || strpos($script, _FOLDER_URL_) != 0)) {
                    $scriptFolder = (strpos($script, 'Module/') !== 0) ? _ADMIN_FOLDER_ : _FOLDER_URL_;
                } else {
                    $scriptFolder = '';
                }
                $replacement .= '		<script type="module" src="' . $scriptFolder . $script . '"' . (strpos($script, 'http') === 0?' crossorigin':'') . '></script>' . PHP_EOL;
            }
            $pos = strripos($this->template, "</body>");
            $this->template = substr_replace($this->template, $replacement, $pos, 0);
        }
        $replace = '<nav class="mt-2">';
        $replacement = '<nav class="mt-2">' . $this->getLinks();
        $this->template = str_replace($replace, $replacement, $this->template);
    }

    public function getLinks(): string
    {
        $return = array();
        $return[] = AdminPage::createLink(
            array(
                'href' => _FOLDER_URL_ . basename(dirname(__FILE__)) . '/',
                'text' => __('Statistics'),
                'class' => 'nav-icon fa fa-tachometer-alt'
            ),
            empty($this->currentUrl) ? '/' . basename(dirname(__FILE__)) . '/' : $this->currentUrl
        );
        $admins_permissions = new Model('admins_permissions');
        $admins_permissions->admin = AdminController::getCurrentUser()->id;
        $admins_permissions = $admins_permissions->get();
        $idPermissions = array();
        foreach ($admins_permissions as $permission) {
            $idPermissions[] = $permission->permission;
        }
        $mAR = new Model('module_admin_routes');
        $mAR->order('j_modules.sort ASC, menu_parent ASC, permission ASC, url ASC');
        $mAR = $mAR->get();
        $menuArray = [];
        foreach ($mAR as $moduleURL) {
            if (in_array($moduleURL->permission, $idPermissions) && !empty($moduleURL->menu_text)) {
                if (empty($moduleURL->menu_parent)) {
                    $menuArray[$moduleURL->url] = array(
                        'href' => $moduleURL->url,
                        'text' => __($moduleURL->menu_text),
                        'class' => 'nav-icon fa fa-' . $moduleURL->menu_class
                    );
                } else {
                    $menuArray[$moduleURL->menu_parent]['submenu'][] = array(
                        'href' => $moduleURL->url,
                        'text' => __($moduleURL->menu_text),
                        'class' => 'nav-icon fa fa-' . $moduleURL->menu_class
                    );
                }
            }
        }
        foreach ($menuArray as $menu) {
            $return[] = AdminPage::createLink($menu, $this->currentUrl);
        }
        return '<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">' . join(
                '',
                $return
            ) . '</ul>';
    }

    private function changeTemplateVars($array) {
        foreach ($array as $key => $value) {
            if (!is_object($value) && !is_array($value)) {
                $change = "{" . $key . "}";
                $this->template = str_replace($change, $value, $this->template);
            } elseif ($key == 'page') {
                foreach ($value as $pageKey => $pageValue) {
                    if (!is_object($pageValue) && !is_array($pageValue)) {
                        $change = "{" . $pageKey . "}";
                        $this->template = str_replace($change, $pageValue, $this->template);
                    }
                }
            }
        }
    }
}