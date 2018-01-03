<?php
namespace Module\Pages\Admin;
use Controller\AdminAct;
use Model\Model;
use Utils\Util;

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
}
$pages = new Act();
if($pages) {
	if(array_key_exists('id', $_REQUEST) || array_key_exists('delete', $_REQUEST)) return $pages->act();
	elseif(array_key_exists('menu', $_REQUEST)) return $pages->menu();
}