<?php
$configFile = dirname(__FILE__) . '/Utils/config.php';
if(!file_exists($configFile)) {
	header("Location: " . 'setup/setup.php');
	exit;
}
else require_once($configFile);
require_once(dirname(__FILE__) . '/Utils/functions.php');
use Utils\Util;
use Model\Model;
if(array_key_exists('logout', $_GET) || $page_url == 'logout') Util::logout();
//Check if file exists on disk
if(file_exists($page_url)) {
	echo file_get_contents($page_url);
	exit;
}
//If url has '//' inside replace with one / and redirect to new url (eg: blog//my-day-was-awesome.html redirects to blog/my-day-was-awesome.html)
if(strpos($_SERVER['REQUEST_URI'], '//') !== false) {
	$new_url = str_replace('//', '/', $_SERVER['REQUEST_URI']);
	header("Location: " . $new_url);
}
$template_file = 'template.html';
//Set to true if you can't browse to the page if you are not logged in (eg: If you are editing /pages/my-account.php you need to set this variable to true inside that file, so a guest cannot see the page /my-account.html)
$mustBeLoggedIn = false;
//Build the social links variable for footer in case you need it
$socialLinks = '';
if(!empty(_FB_LINK_)) $socialLinks .= '<a href="' . _FB_LINK_ . '" rel="nofollow" target="_blank">Facebook</a>';
if(!empty(_TWITTER_LINK_)) $socialLinks .= '<a href="' . _TWITTER_LINK_ . '" rel="nofollow" target="_blank">Twitter</a>';
if(!empty(_LINKEDIN_LINK_)) $socialLinks .= '<a href="' . _LINKEDIN_LINK_ . '" rel="nofollow" target="_blank">LinkedIn</a>';
if(!empty(_PINTEREST_LINK_)) $socialLinks .= '<a href="' . _PINTEREST_LINK_ . '" rel="nofollow" target="_blank">Pinterest</a>';
if(!empty(_GPLUS_LINK_)) $socialLinks .= '<a href="' . _GPLUS_LINK_ . '" rel="nofollow" target="_blank">Google+</a>';
if(!empty(_INSTAGRAM_LINK_)) $socialLinks .= '<a href="' . _INSTAGRAM_LINK_ . '" rel="nofollow" target="_blank">Instagram</a>';

//Build the page content data variables
$content_values = array(
	'LOGO' => _FOLDER_URL_ . 'img/' . _LOGO_,
	'APP_NAME' => _APP_NAME_,
	'COMPANY_NAME' => _COMPANY_NAME_,
	'COMPANY_ADDRESS' => _COMPANY_ADDRESS_,
	'ADDRESS1' => _COMPANY_ADDRESS_L1_,
	'ADDRESS2' => _COMPANY_ADDRESS_L2_,
	'PHONE' => _COMPANY_PHONE_,
	'GAID' => _GOOGLEANALYTICSID_,
	'SOCIAL_LINKS' => $socialLinks,
	'GOOGLE_ANALYTICS' => '',
	'FBAPPID' => ''
);
if(!empty(_GOOGLEANALYTICSID_)) /** @noinspection CommaExpressionJS */
	$content_values['GOOGLE_ANALYTICS'] = '<script>
			(function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function() {
						(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
					m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,\'script\',\'//www.google-analytics.com/analytics.js\',\'ga\');
			ga(\'create\', \'' . _GOOGLEANALYTICSID_ . '\', \'auto\');
			ga(\'send\', \'pageview\');
		</script>';
if(!empty(_FBAPPID_)) $content_values['FBAPPID'] = '<meta property="fb:app_id" content="' . _FBAPPID_ . '" />';

//Set default page title to APP NAME
$page_title = _APP_NAME_;

$found = false;
//Sanitize POST
if(count($_POST) > 0) {
	foreach($_POST AS $key => $value) {
		if(is_string($value)) $_POST[$key] = strip_tags(htmlspecialchars(stripslashes(trim($value))));
	}
}
$content = '';
$breadcrumbs = array();
$js = array();
$css = array();
$description = _APP_NAME_;
$og_image = defined('_OG_IMAGE_')?_OG_IMAGE_:_LOGO_;
$h1 = '<h1>' . _APP_NAME_ . '</h1>';
//Check if the equivalent php file exists as the url
$filename = str_replace('.html', '.php', trim($page_url, '/'));
if($filename == 'index.php') header("Location: " . _FOLDER_URL_);
elseif($filename == 'news' || preg_match('/^news\/(pag\-[\d+]|.*\.html)/', $page_url)) $filename = 'news.php';
elseif($filename == 'testimonials' || preg_match('/^testimonial(\d+)\.html/', $page_url)) $filename = 'testimonials.php';
elseif($filename == 'sitemap.xml') $filename = 'sitemap.php';
elseif(preg_match('/^download\/(.*)/', $page_url)) $filename = 'download.php';
$filenamePath = _APP_DIR_ . 'pages/' . $filename;
if(file_exists($filenamePath) && !is_dir($filenamePath)) {
	$found = true;
	/** @noinspection PhpIncludeInspection */
	include_once($filenamePath);
	//If the included file has the variable mustBeLoggedIn set to true but the current user is guest, redirect him to the login page and "keep in mind" the url he came from
	if($mustBeLoggedIn && !\Utils\Util::getCurrentUser()) {
		$_SESSION['ref'] = $_SERVER['REQUEST_URI'];
		header("Location: " . _FOLDER_URL_ . 'login.html');
		exit();
	}
}
//If url request is a dir and it has the index.html file inside then output it's contents
elseif(file_exists(_APP_DIR_ . $page_url) && is_dir(_APP_DIR_ . $page_url) && file_exists(_APP_DIR_ . $page_url . '/index.html')) {
	$found = true;
	echo file_get_contents(_APP_DIR_ . $page_url . '/index.html');
	exit();
}
//If it's a AJAX request, sanitize the request and check for CSRF guard
if(array_key_exists('HTTP_X_REQUESTED_WITH', $_SERVER) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
	if(strpos($filename, '.php') === false) $filename .= '.php';
	if(strpos($filename, 'json/') === 0) $filename = 'json.php';
	$filenamePath = 'pages/' . $filename;
	if(file_exists($filenamePath) && !is_dir($filenamePath)) {
		$found = true;
		/** @noinspection PhpIncludeInspection */
		require_once($filenamePath);
		exit();
	}
}
$template = new Utils\Template($template_file);
$filename = str_replace('.html', '', trim($page_url, '/'));
$lang = Util::getUserLanguage();
if(!isset($page)) {
	$pages = new Model('pages');
	$pages->language = $lang;
	$pages->url = $filename;
	$pages = $pages->get();
	if(count($pages)) $page = $pages[0];
	else {
		preg_match('/^([a-z]{2,3})$/', $page_url, $matches);
		if(count($matches)) {
			$lang = $matches[1];
			$pages = new Model('pages');
			$pages->language = $lang;
			$pages->url = '';
			$pages = $pages->get();
			if(count($pages)) $page = $pages[0];
		}
		preg_match('/^([a-z]{2,3})(?=\/)\/(.*)$/', $page_url, $matches);
		if(count($matches)) {
			$lang = $matches[1];
			$filename = str_replace('.html', '', trim($matches[2], '/'));
			$pages = new Model('pages');
			$pages->language = $lang;
			$pages->url = $filename;
			$pages = $pages->get();
			if(count($pages)) $page = $pages[0];
		}
	}
}
if(isset($page)) {
	if(property_exists($page, 'visible') && !$page->visible) header("HTTP/1.0 404 Not Found");
	$page_title = $page->title;
	$description = $page->description;
	if(property_exists($page, 'ogimage')) $og_image = $page->ogimage;
	$h1 = $page->h1;
	$content = ($page->url == '') ? $content . $page->content : $page->content;
	if(stripos($content, '#testimonials#')) {
		$testimonials = '';
		/* @property int $status */
		$testimonialsEnt = new Model('testimonials');
		$testimonialsEnt->status = 1;
		$testimonialsEnt->order('RAND()');
		$testimonialsEnt = $testimonialsEnt->get();
		foreach($testimonialsEnt AS $testimonial) {
			if(strlen($testimonial->short) > 200) {
				$pos = strpos($testimonial->short, ' ', 200);
				$testimonial_content = substr($testimonial->short, 0, $pos) . '...';
			}
			else $testimonial_content = $testimonial->short;
			$testimonials .= /** @lang text */
				'<div class="item">
							<div class="title testi-hone">' . $testimonial->name . '</div>
							<div class="subtitle testi-htwo">' . $testimonial->authority . '</div>
								<p class="testimonial-text">"' . nl2br($testimonial_content) . '"</p>
						<a href="/testimonial' . $testimonial->id . '.html" class="readmore"><img alt="" src="/img/beneficii.png" /><p class="testimonials-link">citește tot</p></a>
						</div>' . PHP_EOL;
		}
		$content = str_replace('#testimonials#', $testimonials, $content);
	}
	if(!empty($page->js)) {
		$explodedJs = explode(",", $page->js);
		foreach($explodedJs AS $expJs) $js[] = trim($expJs);
	}
	if(!empty($page->css)) {
		$explodedCss = explode(",", $page->css);
		foreach($explodedCss AS $expCss) $css[] = trim($expCss);
	}
}
elseif((!isset($page) || !$page) && !$found) {
	header('HTTP/1.0 404 Not Found');
	require_once('pages/404.php');
}
$language = Util::getUserLanguage();
$content_values['title'] = htmlentities($page_title);
$content_values['description'] = htmlentities($description);
$content_values['ogimage'] = _FOLDER_URL_ . 'img/' . $og_image;
$content_values['url'] = _ADDRESS_ . '/' . $page_url;
$content_values['js'] = $js;
$content_values['css'] = $css;
$content_values['content'] = $content;
$content_values['h1'] = $h1;
foreach($content_values AS $key => $value) $template->$key = $value;
if(count($breadcrumbs)) $template->setBreadcrumbs($breadcrumbs);
$template->output();