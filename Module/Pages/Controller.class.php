<?php
namespace Module\Pages;
use Model\Model;
use Module\JSON\Page;
use Utils\Util;
use \Module\Sitemap\Page AS Sitemap;

class Controller {
	public $mustBeLoggedIn = false;
	public $url = '';
	public $language = '';
	public $title = false;
	public $content = false;
	public $description = '';
	public $ogimage = '';
	public $h1 = '';
	public $breadcrumbs = array();
	public $js = array();
	public $css = array();
	public $visible = true;

	/**
	 * Pages constructor.
	 * @param $url
	 * @param string $language
	 */
	public function __construct($url, $language = _DEFAULT_LANGUAGE_) {
		$this->url = $url;
		$this->language = $language;
		$page = new Model('pages');
		$page->language = $language;
		$page->url = $url;
		$page->visible = 1;
		$page = $page->get();
		if(count($page)) {
			$page = $page[0];
			$explodedJs = explode(",", $page->js);
			$page->js = array();
			foreach($explodedJs AS $expJs) $page->js[] = trim($expJs);
			$explodedCss = explode(",", $page->css);
			$page->css = array();
			foreach($explodedCss AS $expCss) $page->css[] = trim($expCss);
		}
		else {
			if(strpos($url, 'json/') === 0) {
				echo Page::output();
				exit;
			}
			elseif($url == 'sitemap.xml') {
				echo Sitemap::output();
				exit;
			}
			else {
				$modules = new Model('modules');
				$modules->has_frontend = 1;
				$modules = $modules->get();
				foreach($modules AS $module) {
					$class = 'Module\\' . $module->name . '\\Page';
					$class = new $class($url);
					if($class->isOwnURL()) {
						$page = $class->output();
						break;
					}
				}
			}
		}
		if($page) foreach(get_object_vars($page) AS $key => $value) $this->$key = $value;
		else {
			header('HTTP/1.0 404 Not Found');
			$this->content = '<div class="text-center">
					<h1>' . __('Page not found') . ' <span class="text-danger"><small>' . __('Error 404') . '</small></span></h1>
					<br />
					<p>' . __('The page you requested could not be found, either contact your webmaster or try again. Use your browsers <strong>Back</strong> button to navigate to the page you have previously come from.') . '</p>
					<p><strong>' . __('Or you could just press this neat little button') . ':</strong></p>
					<a href="' . _ADDRESS_ . '" class="btn btn-large btn-info"><i class="icon-home icon-white"></i> ' . __('Take Me Home') . '</a>
				</div>';
			$this->title = __('Page not found');
			$this->description = __('Page not found');
		}
	}
	public static function getMenu() {
		$userLanguage = Util::getUserLanguage();
		$langUrl = ($userLanguage == _DEFAULT_LANGUAGE_)?'':$userLanguage . '/';
		$pages = new Model('pages');
		$pages->language = $userLanguage;
		$pages->visible = 1;
		$pages->menu_order = array('0', '!=');
		$pages->order('menu_parent ASC, menu_order ASC');
		$array_pages = $pages->get();
		$array_menu = array();
		foreach($array_pages AS $page) {
			if(!array_key_exists($page->menu_parent, $array_menu)) $array_menu[$page->menu_parent] = array();
			$pag = array('id' => $page->id, 'url' => _FOLDER_URL_ . $langUrl . (empty(trim($page->url, '/'))?$page->url:$page->url . '.html'), 'menu_text' => $page->menu_text, 'submenu_text' => $page->submenu_text, 'menu_parent' => $page->menu_parent);
			//If page url is the same as the current url set link class as active
			if($_SERVER['REQUEST_URI'] == _FOLDER_URL_ . $langUrl . $page->url || $_SERVER['REQUEST_URI'] == _FOLDER_URL_ . $langUrl . $page->url . '.html') $pag['classes'] = 'active';
			$array_menu[$page->menu_parent][] = $pag;
		}
		$modules = new Model('modules');
		$modules->has_frontend = 1;
		$modules = $modules->get();
		foreach($modules AS $module) {
			if(!in_array($module->name, array('JSON', 'Sitemap'))) {
				$class = 'Module\\' . $module->name . '\\Page';
				$class = new $class($_SERVER['REQUEST_URI']);
				$classMenu = $class->getMenu();
				if($classMenu) foreach($classMenu AS $index => $page) {
					if($index !== 'menu_right') {
						if(!array_key_exists($page['menu_parent'], $array_menu)) $array_menu[$page['menu_parent']] = array();
						$pag = array('url' => _FOLDER_URL_ . $langUrl . $page['url'], 'menu_text' => $page['menu_text'], 'submenu_text' => $page['submenu_text'], 'menu_parent' => $page['menu_parent']);
						//If page url is the same as the current url set link class as active
						if($_SERVER['REQUEST_URI'] == _FOLDER_URL_ . $langUrl . $page['url'] || $_SERVER['REQUEST_URI'] == _FOLDER_URL_ . $langUrl . $page['url']) $pag['classes'] = 'active';
						$array_menu[$page['menu_parent']][$page['menu_order']] = $pag;
						ksort($array_menu[$page['menu_parent']]);
					}
					else {
						foreach($page AS $indexr => $pager) {
							if(!array_key_exists('menu_parent', $pager)) $pager['menu_parent'] = 0;
							if(!array_key_exists('menu_right', $array_menu)) $array_menu['menu_right'] = array();
							if(!array_key_exists($pager['menu_parent'], $array_menu['menu_right'])) $array_menu['menu_right'][$pager['menu_parent']] = array();
							$pag = array('url' => _FOLDER_URL_ . $langUrl . $pager['url'], 'menu_text' => $pager['menu_text'], 'submenu_text' => $pager['submenu_text'], 'menu_parent' => $pager['menu_parent']);
							if(array_key_exists('id', $pager)) $pag['id'] = $pager['id'];
							//If page url is the same as the current url set link class as active
							if($_SERVER['REQUEST_URI'] == _FOLDER_URL_ . $langUrl . $pager['url'] || $_SERVER['REQUEST_URI'] == _FOLDER_URL_ . $langUrl . $pager['url']) $pag['classes'] = 'active';
							$array_menu['menu_right'][$pager['menu_parent']][] = $pag;
						}
					}
				}
			}
		}
		return $array_menu;
	}
}