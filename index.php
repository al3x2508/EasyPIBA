<?php

//Start building the page
require_once(dirname(__FILE__).'/Utils/functions.php');

use Module\Users\Controller;
use Utils\Util;

setlocale(LC_CTYPE, "en_US.utf8");

//If url has '//' inside replace with one / and redirect to new url
//(eg: blog//my-day-was-awesome.html redirects to blog/my-day-was-awesome.html)
if (strpos($_SERVER['REQUEST_URI'], '//') !== false) {
    $new_url = str_replace('//', '/', $_SERVER['REQUEST_URI']);
    header("Location: ".$new_url);
}
$page_url = (isset($page_url)) ? $page_url : '';
$og_image = $_ENV['OG_IMAGE'] ?? $_ENV['LOGO'];
//Set $language as user language

$language = Util::getUserLanguage();
//Check if language exists in url; if exists, set $language from url
$page = new Module\Pages\Controller($page_url, $language);
if ($page->mustBeLoggedIn && !Controller::getCurrentUser()) {
    $_SESSION['ref'] = $_SERVER['REQUEST_URI'];
    header("Location: ".$_ENV['FOLDER_URL'].'login');
    exit();
}
if (!$page->title && $page->content && $page->visible) {
    echo $page->content;
    exit;
}
$template = new Utils\Template($page->template);
$page->ogimage = $_ENV['FOLDER_URL'].'img/'.(!empty($page->ogimage)
        ? $page->ogimage : $og_image);
foreach ($page as $key => $value) {
    if (!in_array($key, array('breadcrumbs', 'sidebar'))) {
        $template->$key = $value;
    }
}
if (count($page->breadcrumbs)) {
    $template->setBreadcrumbs($page->breadcrumbs);
}
$template->output();
