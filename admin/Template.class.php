<?php
use Controller\AdminController;
use Controller\AdminPage;
use Model\Model;
use Utils\Util;

/**
 * @property string h1
 * @property string title
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
    private $adminName = '';
    private $page_name = '';
    private $currentUrl = '';

    /**
     * Template constructor.
     * @param $filename
     * @param string $adminName
     */
    public function __construct($filename, $adminName = '', $url = '')
    {
        $this->filename = $filename;
        $this->adminName = $adminName;
        $query_position = ($_SERVER['QUERY_STRING'] != '')?strpos($_SERVER['REQUEST_URI'],
            $_SERVER['QUERY_STRING']):false;
        $page_url = ($query_position !== false)?trim(substr($_SERVER['REQUEST_URI'], 0, $query_position - 1),
            '/'):trim($_SERVER['REQUEST_URI'], '/');
        $this->page_name = str_replace(array(basename(dirname(__FILE__)) . DIRECTORY_SEPARATOR), '',
            trim($page_url, '/'));
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

    public function output($stime)
    {
        $this->loadTemplate();
        $ftime = microtime();
        $ftime = explode(' ', $ftime);
        $ftime = $ftime[1] + $ftime[0];
        $total_time = round(($ftime - $stime), 4);
        $this->template = str_replace('{loading_time}', $total_time . __(' seconds'), $this->template);
        echo $this->template;
    }

    public function loadTemplate()
    {
        if (!file_exists($this->filename) || is_dir($this->filename)) {
            die("Error loading template ({$this->filename}).");
        }
        $this->template = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->filename);
        $this->APP_NAME = _APP_NAME_;
        $this->FOLDER_URL = _FOLDER_URL_;
        $this->ADMIN_FOLDER_URL = _ADMIN_FOLDER_;
        $userLanguage = Util::getUserLanguage();
        $this->LANGUAGE = $userLanguage;
        $this->myaccountlink = '';
        $this->logoutlink = '<a href="' . _FOLDER_URL_ . 'logout" class="btn btn-sm btn-default btn-flat"><i class="fa fa-power-off"></i> ' . __('Logout') . '</a>';
        $this->loading_time_string = __('Loading time');
        foreach (get_object_vars($this) AS $key => $value) {
            if (!is_object($value) && !is_array($value)) {
                $change = "{" . $key . "}";
                $this->template = str_replace($change, $value, $this->template);
            } else {
                if ($key == 'page') {
                    foreach ($value AS $pageKey => $pageValue) {
                        if (!is_object($pageValue) && !is_array($pageValue)) {
                            $change = "{" . $pageKey . "}";
                            $this->template = str_replace($change, $pageValue, $this->template);
                        }
                    }
                }
            }
        }
        if (property_exists($this->page, 'css') && count($this->page->css)) {
            $replacement = '';
            foreach ($this->page->css AS $script) {
                if(strpos($script, _FOLDER_URL_) === false || strpos($script, _FOLDER_URL_) != 0) $scriptFolder = (strpos($script, 'Module/') !== 0)?_ADMIN_FOLDER_:_FOLDER_URL_;
                else $scriptFolder = '';
                $replacement .= '		<link rel="stylesheet" href="' . $scriptFolder . $script . '" />' . PHP_EOL;
            }
            $pos = strripos($this->template, "</body>");
            $this->template = substr_replace($this->template, $replacement, $pos, 0);
        }
        if (property_exists($this->page, 'js') && count($this->page->js)) {
            $replacement = '';
            foreach ($this->page->js AS $script) {
                if(strpos($script, _FOLDER_URL_) === false || strpos($script, _FOLDER_URL_) != 0) $scriptFolder = (strpos($script, 'Module/') !== 0)?_ADMIN_FOLDER_:_FOLDER_URL_;
                else $scriptFolder = '';
                $replacement .= '		<script src="' . $scriptFolder . $script . '"></script>' . PHP_EOL;
            }
            $pos = strripos($this->template, "</body>");
            $this->template = substr_replace($this->template, $replacement, $pos, 0);
        }
        $replace = '<nav id="mainMenu">';
        $replacement = '<nav id="mainMenu">' . $this->getLinks();
        $this->template = str_replace($replace, $replacement, $this->template);
    }

    public function getLinks()
    {
        $return = array();
        $return[] = AdminPage::createLink(array(
            'href' => _FOLDER_URL_ . basename(dirname(__FILE__)) . '/',
            'text' => __('Statistics'),
            'class' => 'fa fa-tachometer-alt'
        ), empty($this->currentUrl)?'/' . basename(dirname(__FILE__)) . '/':$this->currentUrl);
        $admins_permissions = new Model('admins_permissions');
        $admins_permissions->admin = AdminController::getCurrentUser()->id;
        $admins_permissions = $admins_permissions->get();
        $idPermissions = array();
        foreach ($admins_permissions AS $permission) {
            $idPermissions[] = $permission->permission;
        }
        $mAR = new Model('module_admin_routes');
        $mAR->order('menu_parent ASC, permission ASC, url ASC');
        $mAR = $mAR->get();
        $menuArray = [];
        foreach ($mAR AS $moduleURL) {
            if (in_array($moduleURL->permission, $idPermissions) && !empty($moduleURL->menu_text)) {
                if (empty($moduleURL->menu_parent)) {
                    $menuArray[$moduleURL->url] = array(
                        'href' => $moduleURL->url,
                        'text' => __($moduleURL->menu_text),
                        'class' => 'fa fa-' . $moduleURL->menu_class
                    );
                } else {
                    $menuArray[$moduleURL->menu_parent]['submenu'][] = array(
                        'href' => $moduleURL->url,
                        'text' => __($moduleURL->menu_text),
                        'class' => 'fa fa-' . $moduleURL->menu_class
                    );
                }
            }
        }
        foreach ($menuArray AS $menu) {
            $return[] = AdminPage::createLink($menu, $this->currentUrl);
        }
        return '<ul class="sidebar-menu">' . join('', $return) . '</ul>';
    }
}