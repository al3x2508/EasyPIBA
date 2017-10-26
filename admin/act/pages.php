<?php
namespace Act;

use Controller\PagesController;
use Model\Model;

require_once(dirname(dirname(dirname(__FILE__))) . '/Utils/functions.php');
require_once(dirname(__FILE__) . '/act.class.php');

class Pages extends act {
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
		$pagesC = new PagesController();
		$pagesC->actMenu($menu, $_REQUEST['language']);
	}
}

$pages = new Pages();
if($pages) {
	if(array_key_exists('id', $_REQUEST) || array_key_exists('delete', $_REQUEST)) return $pages->act();
	elseif(array_key_exists('menu', $_REQUEST)) return $pages->menu();
}