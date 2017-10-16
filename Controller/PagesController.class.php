<?php
namespace Controller;
use Model\Model;
class pagesController {
	/**
	 * @param array $meniu
	 */
	public function actualizeazaMeniu($meniu) {
		$menuOrder = ' menu_order = CASE id' . PHP_EOL;
		$menuParent = 'menu_parent = CASE id' . PHP_EOL;
		foreach($meniu AS $id => $val) {
			$menuOrder .= 'WHEN ' . $id . ' THEN ' . $val['order'] . PHP_EOL;
			$menuParent .= 'WHEN ' . $id . ' THEN ' . $val['parent'] . PHP_EOL;
		}
		$menuOrder .= ' ELSE 0' . PHP_EOL;
		$menuParent .= ' ELSE 0' . PHP_EOL;
		$sql = 'UPDATE pages SET' .$menuOrder . 'END, ' . $menuParent . 'END';
		$pages = new Model('pages');
		$pages->runQuery($sql);
	}
}
?>