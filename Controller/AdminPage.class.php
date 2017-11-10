<?php
namespace Controller;
use Model\Model;
/**
 * Class AdminPage
 * @package Controller
 */
abstract class AdminPage {
	/**
	 * @param $key
	 * @param $value
	 */
	public function __set($key, $value) {
		$this->$key = $value;
	}

	/**
	 * @param $pagename
	 * @return bool|string
	 */
	public static function getCurrentModule($pagename) {
		if(!empty($pagename)) {
			$mAR = new Model('module_admin_routes');
			$mAR = $mAR->getOneResult('url', $pagename);
			$admins_permissions = new Model('admins_permissions');
			$admins_permissions->admin = AdminController::getCurrentUser()->id;
			$admins_permissions->permission = $mAR->permission;
			if(count($admins_permissions->get())) {
				$class = 'Module\\' . $mAR->modules->name . '\\Admin\\AdminPage';
				return new $class();
			}
		}
		return false;
	}

	/**
	 * @param $link
	 * @param $page_name
	 * @return string
	 */
	public static function createLink($link, $page_name) {
		$arrclass = array();
		$href = $link['href'];
		if($link['href'] == $page_name || array_key_exists('submenu', $link)) {
			if($link['href'] == $page_name) {
				$arrclass[] = "active";
				$href = '#';
			}
			if(array_key_exists('submenu', $link)) {
				foreach($link['submenu'] AS $submenu) {
					if($submenu['href'] == $page_name) $arrclass[] = "active";
				}
				$arrclass[] = "treeview";
			}
		}
		$class = (count($arrclass)>0)?' class="' . join(" ", $arrclass) . '"':"";
		$ret = '<li'.$class.'>
			<a href="' . $href . '"><i class="fa fa-' . $link['class'] . '"></i> <span>' . $link['text'] . '</span>';
		if(array_key_exists('submenu', $link)) $ret .= '<i class="fa fa-angle-left pull-right"></i>';
		$ret .= '</a>' . PHP_EOL;
		if(array_key_exists('submenu', $link)) {
			$submenuClass = '';
			$hclass = $link['class'];
			if($link['href'] == $page_name) $submenuClass = ' class="active"';
			$ret .= '<ul class="treeview-menu">' . PHP_EOL;
			$ret .= '<li' . $submenuClass . '><a href="' . $link['href'] . '"><i class="fa fa-' . $hclass . '"></i>' . $link['text'] . '</a>' . PHP_EOL;
			foreach($link['submenu'] AS $submenu) {
				$submenuClass = '';
				$hclass = array_key_exists('class', $submenu)?$submenu['class']:$link['class'];
				if($submenu['href'] == $page_name) $submenuClass = ' class="active"';
				$ret .= '<li' . $submenuClass . '><a href="' . $submenu['href'] . '"><i class="fa fa-' . $hclass . '"></i>' . $submenu['text'] . '</a>' . PHP_EOL;
			}
			$ret .= '</ul>' . PHP_EOL;
		}
		$ret .= '</li>' . PHP_EOL;
		return $ret;
	}
}