<?php
namespace Module\Pages\Admin;
use Controller\AdminAct;
use Model\Model;

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/Utils/functions.php');

class Act extends AdminAct {
	public function __construct() {
		$this->permission = 'Edit pages';
		$this->entity = new Model('pages');
		$this->fields = $_POST;
		return true;
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

	public function act() {
		$redis = \Utils\Redis::getInstance();
		if($redis) {
			if(array_key_exists('id', $this->fields)) {
				if($this->fields['id'] > 0) {
					$page = new Model('pages');
					$page = $page->getOneResult('id', $this->fields['id']);
					$url = $page->url;
					$language = $page->language;
					$redisKey = _APP_NAME_ . $url . '|' . $language;
					if($redis->exists($redisKey)) $redis->del($redisKey);
					$redisKey = _APP_NAME_ . 'output|' . $language . '|' . md5($url);
					if($redis->exists($redisKey)) $redis->del($redisKey);
				}
			}
			else {
				$page = new Model('pages');
				$page = $page->getOneResult('id', $this->fields['delete']);
				$url = $page->url;
				$language = $page->language;
				$redisKey = _APP_NAME_ . $url . '|' . $language;
				if($redis->exists($redisKey)) $redis->del($redisKey);
				$redisKey = _APP_NAME_ . 'output|' . $language . '|' . md5($url);
				if($redis->exists($redisKey)) $redis->del($redisKey);
			}
		}
		$act = parent::act();
		if($redis && property_exists($act, 'id')) {
			$url = $act->url;
			$language = $act->language;
			$redisKey = _APP_NAME_ . $url . '|' . $language;
			$redis->set($redisKey, json_encode($act));
		}
	}
}
$pages = new Act();
if($pages) {
	if(array_key_exists('id', $_REQUEST) || array_key_exists('delete', $_REQUEST)) return $pages->act();
	elseif(array_key_exists('menu', $_REQUEST)) return $pages->menu();
}