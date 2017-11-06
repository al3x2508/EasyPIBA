<?php
use Model\Model;
/**
 * @property string h1
 * @property string title
 * @property array js
 * @property array css
 * @property string APP_NAME
 * Class Template
 */
class Template {
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
	public function __construct($filename, $adminName = '', $url = '') {
		$this->filename = $filename;
		$this->adminName = $adminName;
		$query_position = ($_SERVER['QUERY_STRING'] != '')?strpos($_SERVER['REQUEST_URI'], $_SERVER['QUERY_STRING']):false;
		$page_url = ($query_position !== false)?trim(substr($_SERVER['REQUEST_URI'], 0, $query_position - 1), '/'):trim($_SERVER['REQUEST_URI'], '/');
		$this->page_name = str_replace(array(basename(dirname(__FILE__)) . DIRECTORY_SEPARATOR),'',trim($page_url,'/'));
		$this->currentUrl = $url;
	}

	/**
	 * @param $key
	 * @param $value
	 */
	public function __set($key, $value) {
		$this->$key = $value;
	}

	public function loadTemplate() {
		if(!file_exists($this->filename) || is_dir($this->filename)) die("Error loading template ({$this->filename}).");
		$this->template = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->filename);
		$this->APP_NAME = _APP_NAME_;
		$this->logout = __('Logout');
		foreach(get_object_vars($this) AS $key => $value) {
			if(!is_object($value) && !is_array($value)) {
				$change = "{" . $key . "}";
				$this->template = str_replace($change, $value, $this->template);
			}
			else if($key == 'page') {
				foreach($value AS $pageKey => $pageValue) {
					if(!is_object($pageValue) && !is_array($pageValue)) {
						$change = "{" . $pageKey . "}";
						$this->template = str_replace($change, $pageValue, $this->template);
					}
				}
			}
		}
		if(property_exists($this->page, 'css') && count($this->page->css)) {
			$replacement = '';
			foreach($this->page->css AS $script) $replacement .= '		<link rel="stylesheet" href="' . $script . '" />' . PHP_EOL;
			$pos = strripos($this->template, "\t</body>");
			$this->template = substr_replace($this->template, $replacement, $pos, 0);
		}
		if(property_exists($this->page, 'js') && count($this->page->js)) {
			$replacement = '';
			foreach($this->page->js AS $script) $replacement .= '		<script type="text/javascript" src="' . $script . '"></script>' . PHP_EOL;
			$pos = strripos($this->template, "\t</body>");
			$this->template = substr_replace($this->template, $replacement, $pos, 0);
		}
		$replace = '<nav id="mainMenu">';
		$replacement = '<nav id="mainMenu">' . $this->getLinks();
		$this->template = str_replace($replace, $replacement, $this->template);
	}

	public function getLinks() {
		$return = array();
		$return[] = \Controller\AdminPage::createLink(array('href' => '/' . basename(dirname(__FILE__)) . '/', 'text' => __('Statistics'), 'class' => 'dashboard'), empty($this->currentUrl)?'/' . basename(dirname(__FILE__)) . '/':$this->currentUrl);
		$modules = new Model('modules');
		$modules->has_backend = 1;
		$modules = $modules->get();
		foreach($modules AS $module) {
			$class = 'Module\\' . $module->name . '\\Admin\\AdminPage';
			$class = new $class();
			$classMenu = $class->getMenu(false, $this->page_name);
			if($classMenu) $return[] = $classMenu;
		}
		return '<ul class="sidebar-menu">' . join('', $return) . '</ul>';
	}

	public function output() {
		$this->loadTemplate();
		echo $this->template;
	}
}