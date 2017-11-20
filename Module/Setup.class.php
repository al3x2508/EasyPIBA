<?php
namespace Module;
use Model\Model;

abstract class Setup {
	public $moduleId;
	public function __construct() {
		$moduleName = preg_replace('/Module\\\\(.*)\\\\Setup/', '$1', get_class($this));
		$module = new Model('modules');
		$module = $module->getOneResult('name', $moduleName);
		$this->moduleId = $module->id;
	}

	/**
	 * @param $url
	 */
	public function registerFrontendUrl($url) {
		$mR = new Model('module_routes');
		$mR->module = $this->moduleId;
		//URL for the page
		$mR->url = $url['url'];
		//Type: 0 - exact match for url; 1 - regex match for url
		$mR->type = array_key_exists('type', $url)?$url['type']:0;
		//Must be logged in: 0 - Visible for guest users; 1 - Visible for logged in users
		$mR->mustBeLoggedIn = array_key_exists('mustBeLoggedIn', $url)?$url['mustBeLoggedIn']:0;
		//Menu position: 0 - Not shown in menu; 1 - Top menu (default); 2 - Right menu (eg: Login, My account)
		$mR->menu_position = array_key_exists('menu_position', $url)?$url['menu_position']:0;
		//Menu text: the text used for the link in menu
		$mR->menu_text = array_key_exists('menu_text', $url)?$url['menu_text']:'';
		//Submenu text: the text used for the link if is a dropdown menu
		$mR->submenu_text = array_key_exists('submenu_text', $url)?$url['submenu_text']:'';
		//Menu order: the order of the link inside menu
		$mR->menu_order = array_key_exists('menu_order', $url)?$url['menu_order']:0;
		//Menu parent: the URL for the parent menu (eg: news, if we want to show the link under the News menu)
		$mR->menu_parent = array_key_exists('menu_parent', $url)?$url['menu_parent']:'';
		$mR->create();
	}
	public function registerBackendUrl($url) {
		//Get permission by name
		$permission = new Model('permissions');
		$permission = $permission->getOneResult('name', $url['permission']);

		$mAR = new Model('module_admin_routes');
		//Set module id that was found by the constructor function
		$mAR->module = $this->moduleId;
		//Set permission for the page
		$mAR->permission = $permission->id;
		//URL for administration page (this will be translated into admin/{URL})
		$mAR->url = $url['url'];
		//Menu text for administration nav
		$mAR->menu_text = $url['menu_text'];
		//Icon name (from FontAwesome; check http://fontawesome.io/icons/)
		$mAR->menu_class = array_key_exists('menu_class', $url)?$url['menu_class']:'';
		//Menu parent: the URL for the parent menu (eg: pages, if we want to show the current link under the Pages menu)
		$mAR->menu_parent = array_key_exists('menu_parent', $url)?$url['menu_parent']:'';
		$mAR->create();
	}
}