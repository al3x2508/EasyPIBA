<?php

use Controller\AdminController;
use Controller\AdminPage;
use Model\Model;

/**
 * @property string h1
 * @property string title
 * @property array  css
 * @property string APP_NAME
 * Class Template
 */
class Template
{
    protected $filename;
    private string $template = '';
    private array $js = ['main.js'];
    private string $adminName = '';
    private $page_name;
    private $currentUrl = '';

    /**
     * Template constructor.
     *
     * @param          $filename
     * @param  string  $adminName
     */
    public function __construct($filename, $adminName = '', $url = '')
    {
        $this->filename = $filename;
        $this->adminName = $adminName;
        $query_position = ($_SERVER['QUERY_STRING'] != '')
            ? strpos($_SERVER['REQUEST_URI'], $_SERVER['QUERY_STRING']) : false;
        $page_url = ($query_position !== false)
            ? trim(substr($_SERVER['REQUEST_URI'], 0, $query_position - 1), '/')
            : trim($_SERVER['REQUEST_URI'], '/');
        $this->page_name = str_replace(array(
            basename(dirname(__FILE__)).DIRECTORY_SEPARATOR
        ), '', trim($page_url, '/'));
        $this->currentUrl = $url;
    }

    /**
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $this->$key = $value;
    }

    public function loadTemplate()
    {
        if (!file_exists($this->filename) || is_dir($this->filename)) {
            die("Error loading template ({$this->filename}).");
        }
        $adminFolder = $_ENV['FOLDER_URL'].basename(dirname(__FILE__)).'/';
        $this->template = file_get_contents(dirname(__FILE__)
            .DIRECTORY_SEPARATOR.$this->filename);
        $this->APP_NAME = $_ENV['APP_NAME'];
        $this->LOGO = $_ENV['FOLDER_URL'].'img/'.$_ENV['LOGO'];
        $this->FOLDER_URL = $_ENV['FOLDER_URL'];
        $this->ADMIN_FOLDER_URL = $adminFolder;
        $this->LANGUAGE = $_SESSION['userLanguage'];
        $this->logout = __('Logout');
        foreach (get_object_vars($this) as $key => $value) {
            if (!is_object($value) && !is_array($value)) {
                $change = "{".$key."}";
                $this->template = str_replace($change, $value, $this->template);
            } else {
                if ($key == 'page') {
                    foreach ($value as $pageKey => $pageValue) {
                        if (!is_object($pageValue) && !is_array($pageValue)) {
                            $change = "{".$pageKey."}";
                            $this->template = str_replace($change, $pageValue,
                                $this->template);
                        }
                    }
                }
            }
        }
        if (property_exists($this->page, 'css') && count($this->page->css)) {
            $replacement = '';
            foreach ($this->page->css as $script) {
                $scriptFolder = (strpos($script, 'Module/') !== 0)
                    ? $adminFolder : $_ENV['FOLDER_URL'];
                $replacement .= '<link rel="stylesheet" href="'
                    .$scriptFolder.$script.'" />'.PHP_EOL;
            }
            $pos = strripos($this->template, "</body>");
            $this->template = substr_replace($this->template, $replacement,
                $pos, 0);
        }
        if (property_exists($this->page, 'js') && count($this->page->js)) {
            $replacement = '';
            foreach ($this->page->js as $script) {
                $scriptFolder = (strpos($script, 'Module/') !== 0)
                    ? $adminFolder : $_ENV['FOLDER_URL'];
                $replacement .= '<script src="'.$scriptFolder.$script
                    .'"></script>'.PHP_EOL;
            }
            $pos = strripos($this->template, "</body>");
            $this->template = substr_replace($this->template, $replacement,
                $pos, 0);
        }
        $this->template = str_replace('{mainMenu}', $this->getLinks(),
            $this->template);
        $this->template = preg_replace_callback('/\{__([^}]*)}/', function($matches) {
            return __($matches[1]);
        }, $this->template);
    }

    public function getLinks()
    {
        $return = array();
        $return[] = AdminPage::createLink(array(
            'href'  => $_ENV['FOLDER_URL'].basename(dirname(__FILE__)).'/',
            'text'  => __('Statistics'),
            'class' => 'fa fa-chart-pie'
        ), empty($this->currentUrl) ? '/'.basename(dirname(__FILE__)).'/'
            : $this->currentUrl);
        $admin = AdminController::getCurrentUser();
        if($admin) {
            $admins_permissions = new Model('admins_permissions');
            $admins_permissions->admin = AdminController::getCurrentUser()->id;
            $admins_permissions = $admins_permissions->get();
            $idPermissions = array();
            foreach ($admins_permissions as $permission) {
                $idPermissions[] = $permission->permission;
            }
            $mAR = new Model('module_admin_routes');
            $mAR->order('menu_parent ASC, permission ASC, url ASC');
            $mAR = $mAR->get();
            $menuArray = [];
            foreach ($mAR as $moduleURL) {
                if (in_array($moduleURL->permission, $idPermissions)
                    && !empty($moduleURL->menu_text)
                ) {
                    if (empty($moduleURL->menu_parent)) {
                        $menuArray[$moduleURL->url]
                            = array(
                            'href'  => $moduleURL->url,
                            'text'  => __($moduleURL->menu_text),
                            'class' => $moduleURL->menu_class
                        );
                    } else {
                        $menuArray[$moduleURL->menu_parent]['submenu'][]
                            = array(
                            'href'  => $moduleURL->url,
                            'text'  => __($moduleURL->menu_text),
                            'class' => $moduleURL->menu_class
                        );
                    }
                }
            }
            foreach ($menuArray as $menu) {
                $return[] = AdminPage::createLink($menu, $this->currentUrl);
            }
        }
        return join('', $return);
    }

    public function output()
    {
        $this->loadTemplate();
        echo $this->template;
    }
}
