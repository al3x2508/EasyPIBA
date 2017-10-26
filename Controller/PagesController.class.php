<?php
namespace Controller;
use Model\Model;
class PagesController {
	/**
	 * @param array $menu
	 */
	public function actMenu($menu, $language) {
		$menuOrder = ' menu_order = CASE id' . PHP_EOL;
		$menuParent = 'menu_parent = CASE id' . PHP_EOL;
		foreach($menu AS $id => $value) {
			$menuOrder .= 'WHEN ' . $id . ' THEN ' . $value['order'] . PHP_EOL;
			$menuParent .= 'WHEN ' . $id . ' THEN ' . $value['parent'] . PHP_EOL;
		}
		$menuOrder .= ' ELSE 0' . PHP_EOL;
		$menuParent .= ' ELSE 0' . PHP_EOL;
		$sql = 'UPDATE pages SET' .$menuOrder . 'END, ' . $menuParent . 'END';
		$data = array();
		if(!empty($language)) {
			$paramType = 's';
			$sql .= ' WHERE language = ?';
			$data = array(&$paramType, &$language);
		}
		$pages = new Model('pages');
		$pages->runQuery($sql, $data, false);
	}
}