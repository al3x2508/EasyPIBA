<?php
class AdminMenu {
	private static $page_name = '';
	public function __construct() {
		$query_position = ($_SERVER['QUERY_STRING'] != '')?strpos($_SERVER['REQUEST_URI'], $_SERVER['QUERY_STRING']):false;
		$page_url = ($query_position !== false)?trim(substr($_SERVER['REQUEST_URI'], 0, $query_position - 1), '/'):trim($_SERVER['REQUEST_URI'], '/');
		self::$page_name = str_replace(array(basename(dirname(__FILE__)) . DIRECTORY_SEPARATOR),'',trim($page_url,'/'));
	}
	public static function getLinks($returnPermissions = false) {
		$return = array();
		$admin = new Controller\AdminController();
		$permissions = $admin->getPermissions();
		if(!$returnPermissions) $return[] = self::createLink(array('href' => '/' . basename(dirname(__FILE__)) . '/', 'text' => __('Statistics'), 'class' => 'dashboard'));
		if(in_array("View users", $permissions) || in_array("Edit users", $permissions)) {
			if($returnPermissions) $return[] = 'users';
			else $return[] = self::createLink(array('href' => 'users', 'text' => __('Users'), 'class' => 'users'));
		}
		if(in_array("Edit pages", $permissions)) {
			if($returnPermissions) {
				$return[] = 'pages';
				$return[] = 'menu';
				$return[] = 'media';
			}
			else $return[] = self::createLink(array('href' => 'pages', 'text' => __('Pages'), 'class' => 'files-o', 'submenu' => array(array('href' => 'menu', 'text' => __('Menu'), 'class' => 'bars'), array('href' => 'media', 'text' => __('Media'), 'class' => 'picture-o'))));
		}
		if(in_array("Edit news", $permissions)) {
			if($returnPermissions) $return[] = 'news';
			else $return[] = self::createLink(array('href' => 'news', 'text' => __('News'), 'class' => 'newspaper-o'));
		}
		if(in_array("Edit testimonials", $permissions)) {
			if($returnPermissions) $return[] = 'testimonials';
			else $return[] = self::createLink(array('href' => 'testimonials', 'text' => __('Testimonials'), 'class' => 'comment-o'));
		}
		if(in_array("Edit administrators", $permissions)) {
			if($returnPermissions) $return[] = 'administrators';
			else $return[] = self::createLink(array('href' => 'administrators', 'text' => __('Administrators'), 'class' => 'user'));
		}
		if(!$returnPermissions) return '<ul class="sidebar-menu">' . join('', $return) . '</ul>';
		return $return;
	}
	private static function createLink($link) {
		$arrclass = array();
		$href = $link['href'];
		if($link['href'] == self::$page_name || array_key_exists('submenu', $link)) {
			if($link['href'] == self::$page_name) {
				$arrclass[] = "active";
				$href = '#';
			}
			if(array_key_exists('submenu', $link)) {
				foreach($link['submenu'] AS $submenu) {
					if($submenu['href'] == self::$page_name) $arrclass[] = "active";
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
			if($link['href'] == self::$page_name) $submenuClass = ' class="active"';
			$ret .= '<ul class="treeview-menu">' . PHP_EOL;
			$ret .= '<li' . $submenuClass . '><a href="' . $link['href'] . '"><i class="fa fa-' . $hclass . '"></i>' . $link['text'] . '</a>' . PHP_EOL;
			foreach($link['submenu'] AS $submenu) {
				$submenuClass = '';
				$hclass = array_key_exists('class', $submenu)?$submenu['class']:$link['class'];
				if($submenu['href'] == self::$page_name) $submenuClass = ' class="active"';
				$ret .= '<li' . $submenuClass . '><a href="' . $submenu['href'] . '"><i class="fa fa-' . $hclass . '"></i>' . $submenu['text'] . '</a>' . PHP_EOL;
			}
			$ret .= '</ul>' . PHP_EOL;
		}
		$ret .= '</li>' . PHP_EOL;
		return $ret;
	}
}