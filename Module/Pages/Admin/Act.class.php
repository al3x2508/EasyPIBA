<?php
namespace Module\Pages\Admin;
use Controller\AdminAct;
use Model\Model;
use Utils\Util;

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/Utils/functions.php');

class Act extends AdminAct {
	public function __construct($id) {
		$this->permission = 'Edit pages';
		$this->entity = new Model('pages');
		$act = false;
		if ($this->hasAccess()) {
			if(!arrayKeyExists('menu', $_REQUEST)) {
				if ($id) $this->fields['id'] = $id;
				if (strtolower($_SERVER['REQUEST_METHOD']) == 'delete') {
					if ($id) {
						$this->deleteFromCache();
						$act = $this->delete();
					}
					else $this->sendStatus(false, __('No ID set'));
				}
				else {
					$method = 'patch';
					if (strtolower($_SERVER['REQUEST_METHOD']) == 'patch') {
						if ($id) parse_str(file_get_contents('php://input'), $_PATCH);
						else $this->sendStatus(false, __('No ID set'));
					}
					else $method = 'create';
					foreach ($method == 'patch' ? $_PATCH : $_POST AS $key => $value) $this->fields[$key] = $value;
					try {
						$act = call_user_func_array(array($this, $method), array());
					}
					catch (\Exception $e) {
						$this->sendStatus(false, $e->getMessage());
					}
				}
			}
			else {
				$act = $this->menu();
			}
		}
		$this->sendStatus($act);
	}

	public function create() {
		foreach ($this->fields AS $key => $value) $this->entity->$key = $value;
		$this->addToCache();
		return $this->entity->create();
	}

	public function patch() {
		$this->deleteFromCache();
		foreach ($this->fields AS $key => $value) $this->entity->$key = $value;
		$this->addToCache();
		return $this->entity->update();
	}

	public function delete() {
		$this->entity->id = $this->fields['id'];
		return $this->entity->delete();
	}

	private function deleteFromCache() {
		$cache = Util::getCache();
		if($cache) {
			$page = new Model('pages');
			$page = $page->getOneResult('id', $this->fields['id']);
			$url = $page->url;
			$language = $page->language;
			$cacheKey = _CACHE_PREFIX_ . $url . '|' . $language;
			if($cache->exists($cacheKey)) $cache->del($cacheKey);
			$cacheKey = _CACHE_PREFIX_ . 'output|' . $language . '|' . md5($url);
			if($cache->exists($cacheKey)) $cache->del($cacheKey);
		}
	}

	private function addToCache() {
		$cache = Util::getCache();
		if($cache) {
			$url = $this->fields['url'];
			$language = $this->fields['language'];
			$cacheKey = _CACHE_PREFIX_ . $url . '|' . $language;
			$cache->set($cacheKey, json_encode($this->fields));
		}
	}

	public function menu() {
		$order = 0;
		$lastParent = 0;
		$menu = array();
		foreach($_REQUEST['menu'] AS $item) {
			if(!empty($item['item_id'])) {
				$currentParent = (!empty($item['parent_id'])) ? $item['parent_id'] : 0;
				if($currentParent == $lastParent) $order++;
				else {
					$order = 1;
					$lastParent = $currentParent;
				}
				$menu[$item['item_id']] = array('order' => $order, 'parent' => $currentParent);
			}
		}
		if(count($menu)) $this->actMenu($menu, $_REQUEST['language']);
		else $this->actMenu(false, $_REQUEST['language']);
	}

	/**
	 * @param bool|array $menu
	 * @param $language
	 */
	public function actMenu($menu, $language) {
		$data = array();
		if(!$menu) $sql = 'UPDATE pages SET menu_order = 0';
		else {
			$menuOrder = ' menu_order = CASE id' . PHP_EOL;
			$menuParent = 'menu_parent = CASE id' . PHP_EOL;
			foreach($menu AS $id => $value) {
				$menuOrder .= 'WHEN ' . $id . ' THEN ' . $value['order'] . PHP_EOL;
				$menuParent .= 'WHEN ' . $id . ' THEN ' . $value['parent'] . PHP_EOL;
			}
			$menuOrder .= ' ELSE 0' . PHP_EOL;
			$menuParent .= ' ELSE 0' . PHP_EOL;
			$sql = 'UPDATE pages SET' . $menuOrder . 'END, ' . $menuParent . 'END';
		}
		if(!empty($language)) {
			$paramType = 's';
			$sql .= ' WHERE language = ?';
			$data = array(&$paramType, &$language);
		}
		$pages = new Model('pages');
		$pages->runQuery($sql, $data, false);
	}
}