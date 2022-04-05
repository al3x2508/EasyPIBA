<?php
$stime = microtime();
$stime = explode(' ', $stime);
$stime = $stime[1] + $stime[0];
//Check if config file exists, if not then run setup
$configFile = sprintf("%s/Utils/config.php", dirname(__FILE__));
if(!file_exists($configFile)) {
	header("Location: setup/setup.php");
	exit;
}
else {
    require_once($configFile);
}
//Start building the page
require_once(sprintf("%s/Utils/functions.php", dirname(__FILE__)));

use Module\Users\Controller AS UsersController;
use Module\Pages\Controller AS PagesController;
use Utils\Util;
use Utils\Template;
//If url has '//' inside replace with one / and redirect to new url (eg: blog//my-day-was-awesome.html redirects to blog/my-day-was-awesome.html)
if(strpos($_SERVER['REQUEST_URI'], '//') !== false) {
	$new_url = str_replace('//', '/', $_SERVER['REQUEST_URI']);
	header("Location: " . $new_url);
}
$page_url = (isset($page_url))?$page_url:'';
$og_image = defined('_OG_IMAGE_')?_OG_IMAGE_:_LOGO_;
//Set $language as user language
$language = Util::getUserLanguage();
//Check if language exists in url; if exists, set $language from url
$page = new PagesController($page_url, $language);
if($page->mustBeLoggedIn && !UsersController::getCurrentUser()) {
	$_SESSION['ref'] = $_SERVER['REQUEST_URI'];
	header("Location: " . _FOLDER_URL_ . 'login');
	exit();
}
if(!$page->title && $page->content && $page->visible) {
	echo $page->content;
	exit;
}
$template = new Template($page->template);
$page->ogimage = _FOLDER_URL_ . 'img/' . (!empty($page->ogimage)?$page->ogimage:$og_image);
foreach($page AS $key => $value) if(!in_array($key, array('breadcrumbs', 'sidebar'))) $template->$key = $value;
if(count($page->breadcrumbs)) $template->setBreadcrumbs($page->breadcrumbs);
if(count($page->sidebar)) $template->setSidebar($page->sidebar);
$template->output();