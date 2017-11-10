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
	public function registerFrontendUrl($url) {
		$mR = new Model('module_routes');
		$mR->module = $this->moduleId;
		$mR->url = $url['url'];
		$mR->type = array_key_exists('type', $url)?$url['type']:0;
		$mR->mustBeLoggedIn = array_key_exists('mustBeLoggedIn', $url)?$url['mustBeLoggedIn']:0;
		$mR->menu_position = array_key_exists('menu_position', $url)?$url['menu_position']:0;
		$mR->menu_text = array_key_exists('menu_text', $url)?$url['menu_text']:'';
		$mR->submenu_text = array_key_exists('submenu_text', $url)?$url['submenu_text']:'';
		$mR->menu_order = array_key_exists('menu_order', $url)?$url['menu_order']:0;
		$mR->menu_parent = array_key_exists('menu_parent', $url)?$url['menu_parent']:'';
		$mR->create();
	}
	public function registerBackendUrl($url) {
		$permission = new Model('permissions');
		$permission = $permission->getOneResult('name', $url['permission']);
		$mAR = new Model('module_admin_routes');
		$mAR->module = $this->moduleId;
		$mAR->permission = $permission->id;
		$mAR->url = $url['url'];
		$mAR->menu_text = $url['menu_text'];
		$mAR->menu_class = array_key_exists('menu_class', $url)?$url['menu_class']:'';
		$mAR->menu_parent = array_key_exists('menu_parent', $url)?$url['menu_parent']:'';
		$mAR->create();
	}
}