<?php
namespace Utils;

use Model\Model;
use Module\Pages\Controller;

/**
 * @property string title
 * @property string description
 * @property float|int|null|string ogimage
 * @property string url
 * @property array js
 * @property array css
 * @property string header
 * @property float|int|mixed|null|string content
 * @property float|int|null|string h1
 * @property string HOME_LINK
 * @property string FOLDER_URL
 * @property mixed|string LANGUAGE
 * @property string menu_right
 * @property string MAIN_JAVASCRIPTS
 */
class Template {
	/**
	 * @var string
	 */
	protected $filename;
	/**
	 * @var string
	 */
	protected $page_url = '';
	/**
	 * @var string
	 */
	private $template = '';
	/**
	 * @var string
	 */
	private $breadcrumbs = '';
	/**
	 * @var string
	 */
	private $sidebar = '';
	/**
	 * @var bool
	 */
	private $menu = false;
	/**
	 * @var bool
	 */
	private $from_cache = true;
	/**
	 * @var array
	 */
	public $js = array();
	/**
	 * @var array
	 */
	public $css = array();

	private $rediscache = false;

	/**
	 * Template constructor.
	 * @param $filename
	 */
	public function __construct($filename) {
		$this->rediscache = \Utils\Redis::getInstance();
		$this->page_url .= $_SERVER['REQUEST_URI'];
		$this->filename = _TEMPLATE_DIR_ . $filename;
		//Build the social links variable for footer in case you need it
		$socialLinks = '';
		if(!empty(_FB_LINK_)) $socialLinks .= '<a href="' . _FB_LINK_ . '" rel="nofollow" target="_blank">Facebook</a>';
		if(!empty(_TWITTER_LINK_)) $socialLinks .= '<a href="' . _TWITTER_LINK_ . '" rel="nofollow" target="_blank">Twitter</a>';
		if(!empty(_LINKEDIN_LINK_)) $socialLinks .= '<a href="' . _LINKEDIN_LINK_ . '" rel="nofollow" target="_blank">LinkedIn</a>';
		if(!empty(_PINTEREST_LINK_)) $socialLinks .= '<a href="' . _PINTEREST_LINK_ . '" rel="nofollow" target="_blank">Pinterest</a>';
		if(!empty(_GPLUS_LINK_)) $socialLinks .= '<a href="' . _GPLUS_LINK_ . '" rel="nofollow" target="_blank">Google+</a>';
		if(!empty(_INSTAGRAM_LINK_)) $socialLinks .= '<a href="' . _INSTAGRAM_LINK_ . '" rel="nofollow" target="_blank">Instagram</a>';

		//Build the page content data variables
		$content_values = array('url' => _ADDRESS_ . '/' . $this->page_url, 'LOGO' => _FOLDER_URL_ . 'img/' . _LOGO_, 'APP_NAME' => _APP_NAME_, 'COMPANY_NAME' => _COMPANY_NAME_, 'COMPANY_ADDRESS' => _COMPANY_ADDRESS_, 'ADDRESS1' => _COMPANY_ADDRESS_L1_, 'ADDRESS2' => _COMPANY_ADDRESS_L2_, 'PHONE' => _COMPANY_PHONE_, 'GAID' => _GOOGLEANALYTICSID_, 'SOCIAL_LINKS' => $socialLinks, 'GOOGLE_ANALYTICS' => '', 'FBAPPID' => '');
		if(!empty(_GOOGLEANALYTICSID_)) /** @noinspection CommaExpressionJS */
			$content_values['GOOGLE_ANALYTICS'] = /** @lang text */
				'<script>
			(function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function() {
						(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
					m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,\'script\',\'//www.google-analytics.com/analytics.js\',\'ga\');
			ga(\'create\', \'' . _GOOGLEANALYTICSID_ . '\', \'auto\');
			ga(\'send\', \'pageview\');
		</script>';
		if(!empty(_FBAPPID_)) $content_values['FBAPPID'] = '<meta property="fb:app_id" content="' . _FBAPPID_ . '" />';
		foreach($content_values AS $key => $value) $this->$key = $value;
		$this->colsize = 12;
	}

	/**
	 * @param $key
	 * @param $value
	 */
	public function __set($key, $value) {
		$this->$key = $value;
	}

	/**
	 * @param $links
	 */
	public function setBreadcrumbs($links) {
		foreach($links as $key => $value) {
			if('/' . $key != $_SERVER['REQUEST_URI']) $this->breadcrumbs = ($this->breadcrumbs == '') ? /** @lang text */
				'<li itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a href="' . _FOLDER_URL_ . $key . '" itemprop="url" title="' . $value . '"><span itemprop="title">' . $value . '</span></a></li>' : $this->breadcrumbs . '<li itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a href="' . _FOLDER_URL_ . $key . '" itemprop="url" title="' . $value . '"><span itemprop="title">' . $value . '</span></a></li>';
			else $this->breadcrumbs = ($this->breadcrumbs == '') ? /** @lang text */
				'<li id="bselected" itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a href="' . _FOLDER_URL_ . $key . '" itemprop="url" title="' . $value . '"><span itemprop="title">' . $value . '</span></a></li>' : $this->breadcrumbs . '<li id="bselected" itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a href="' . _FOLDER_URL_ . $key . '" itemprop="url" title="' . $value . '"><span itemprop="title">' . $value . '</span></a></li>';
		}
	}

	/**
	 * @param $links
	 */
	public function setSidebar($links) {
		$this->colsize = 10;
		$this->sidebar = '<div class="sidebar col-2"><ul>';
		foreach($links as $key => $value) {
			if(!is_array($value)) {
				$selected = ('/' . $key == $_SERVER['REQUEST_URI']) ? ' class="selected"' : '';
				$this->sidebar .= '<li' . $selected . '><a href="' . _FOLDER_URL_ . $key . '" title="' . $value . '">' . $value . '</a></li>';
			}
			else {
				$selected = ('/' . $key == $_SERVER['REQUEST_URI']) ? ' class="selected"' : '';
				$this->sidebar .= '<li' . $selected . '><a href="' . _FOLDER_URL_ . $key . '" title="' . $value['title'] . '">' . $value['title'] . '</a><ul>';
				foreach($value['submenu'] as $key1 => $value1) {
					$selected = ('/' . $key1 == $_SERVER['REQUEST_URI']) ? ' class="selected"' : '';
					$this->sidebar .= '<li' . $selected . '><a href="' . _FOLDER_URL_ . $key1 . '" title="' . $value1 . '">' . $value1 . '</a></li>';
				}
				$this->sidebar .= '</ul></li>';
			}
		}
		$this->sidebar .= '</ul></div>';
	}

	/**
	 * @param $mArr
	 * @param int $level
	 * @return string
	 */
	private function menu($mArr, $level = 0) {
		$menu = '';
		foreach($mArr AS $parent => $pages) {
			if($parent !== 'menu_right' && ($parent === $level)) {
				foreach($pages AS $page) {
					//If it has submenu build a dropdown
					if(array_key_exists('id', $page) && array_key_exists($page['id'], $mArr)) {
						$cssClasses = (array_key_exists('classes', $page)) ? ' ' . $page['classes'] : '';
						$menu .= '<li class="nav-item dropdown' . $cssClasses . '">
							<a class="nav-link dropdown-toggle" href="#" id="menu' . $page['id'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' . $page['menu_text'] . '</a>
							<div class="dropdown-menu" aria-labelledby="menu' . $page['id'] . '">
								<a class="dropdown-item" href="' . $page['url'] . '">' . $page['submenu_text'] . '</a>' . $this->menu($mArr, $page['id']) . '
							</div>
						</li>';
					}
					else {
						$cssClasses = (array_key_exists('classes', $page)) ? ' ' . $page['classes'] : '';
						if($level === 0) $menu .= '<li class="nav-item' . $cssClasses . '"><a class="nav-link" href="' . $page['url'] . '">' . $page['menu_text'] . '</a></li>';
						else $menu .= '<a class="dropdown-item" href="' . $page['url'] . '">' . $page['menu_text'] . '</a>';
					}
				}
			}
		}
		return $menu;
	}

	/**
	 * Protect forms by CSRF
	 * @param $form_data_html
	 * @return mixed
	 */
	private function csrfguard_replace_forms($form_data_html) {
		preg_match_all("/<form(.*?)>(.*?)<\\/form>/is", $form_data_html, $matches, PREG_SET_ORDER);
		if(is_array($matches)) {
			foreach($matches as $m) {
				if(strpos($m[1], "nocsrf") !== false) continue;
				$name = "CSRFGuard_" . mt_rand(0, mt_getrandmax());
				$token = Util::csrfguard_generate_token($name);
				$form_data_html = str_replace($m[0], /** @lang text */
					"<form{$m[1]}>
<input type='hidden' name='CSRFName' value='{$name}' />
<input type='hidden' name='CSRFToken' value='{$token}' />{$m[2]}</form>", $form_data_html);
			}
		}
		return $form_data_html;
	}

	/**
	 * @return bool
	 */
	public function load_template() {
		if(!file_exists($this->filename) || is_dir($this->filename)) die("Error loading template ({$this->filename}).");
		//Load the html template
		$this->template = file_get_contents($this->filename);
		$this->HOME_LINK = _FOLDER_URL_;
		$this->FOLDER_URL = _FOLDER_URL_;
		if(!property_exists($this, 'css')) $this->css = array();
		$this->css[] = 'main.css';
		require_once(dirname(__FILE__) . '/scripts.php');
		$userLanguage = Util::getUserLanguage();
		//Set the javascript variable for language
		$this->LANGUAGE = $userLanguage;
		$langUrl = ($userLanguage == _DEFAULT_LANGUAGE_) ? '' : $userLanguage . '/';
		$redisKey = _APP_NAME_ . 'menu|' . $langUrl;
		if($this->rediscache && $this->rediscache->exists($redisKey)) $pagesArray = json_decode($this->rediscache->get($redisKey));
		else {
			$pages = new Model('pages');
			$pages->language = $userLanguage;
			$pages->visible = 1;
			$pagesArray = $pages->get();
			if($this->rediscache) $this->rediscache->set($redisKey, json_encode($pagesArray));
		}
		foreach($pagesArray AS $page) {
			if(($_SERVER['REQUEST_URI'] == _FOLDER_URL_ . $langUrl . $page->url || $_SERVER['REQUEST_URI'] == _FOLDER_URL_ . $langUrl . $page->url . '.html') && !empty($page->metaog)) {
				$metaog = json_decode($page->metaog, true);
				$this->template = preg_replace(/** @lang text */
					'/(\<meta property\=\"og\:title\" content\=\")(.*)(\" \/\>)/', "$1{$metaog['title']}$3", $this->template, 1);
				$this->template = preg_replace(/** @lang text */
					'/(\<meta property\=\"og\:description\" content\=\")(.*)(\" \/\>)/', "$1{$metaog['description']}$3", $this->template, 1);
				$replacementpic = '<meta property="og:image" content="http://' . _ADDRESS_ . '/img/' . $metaog['image'] . '" />';
				$this->template = preg_replace(/** @lang text */
					'/\<meta property\=\"og\:image\" content\=\"(.*)\" \/\>/', $replacementpic, $this->template, 1);
			}
		}
		/*
		 * Build the menu left
		*/
		$array_menu = Controller::getMenu();
		$this->menu = $this->menu($array_menu);
		//End of menu left
		/*
		 * Build the menu right: Dropdown for languages, login button
		 */
		$menuRight = '';
		$redisKey = _APP_NAME_ . 'menuLanguage|' . $langUrl;
		if($this->rediscache && $this->rediscache->exists($redisKey)) $pagesArray = json_decode($this->rediscache->get($redisKey));
		else {
			if(!isset($pages)) $pages = new Model('pages');
			$pages->clear();
			$pages->groupBy('language');
			$pages->order('language ASC');
			$pagesArray = $pages->get();
			if($this->rediscache) $this->rediscache->set($redisKey, json_encode($pagesArray));
		}
		if(count($pagesArray) > 1) {
			$currentLanguage = array('native' => 'English', 'flag' => 'us');
			foreach($pagesArray AS $page) {
				if($page->language == $userLanguage) $currentLanguage = array('native' => $page->languages->name, 'flag' => $page->languages->code);
			}
			if(!property_exists($this, 'css')) $this->css = array();
			$this->css[] = 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/css/bootstrap-select.min.css';
			$this->css[] = 'https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/0.8.2/css/flag-icon.min.css';
			$menuRight = '<div class="dropdown">
					<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="flag-icon flag-icon-' . ($currentLanguage['flag'] == 'en' ? 'us' : $currentLanguage['flag']) . '"></span> ' . $currentLanguage['native'] . '</button>
  					<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">' . PHP_EOL;
			foreach($pagesArray AS $page) {
				$flag = ($page->language == 'en') ? 'us' : $page->language;
				$href = ($page->language == _DEFAULT_LANGUAGE_) ? '' : $page->language;
				$menuRight .= "  						<a class=\"dropdown-item language\" href=\"" . _FOLDER_URL_ . $href . "\" data-language=\"{$page->language}\"><span class=\"flag-icon flag-icon-{$flag}\"></span> " . $page->languages->name . "</a>";
			}
			$menuRight .= '  					</div>
				</div>' . PHP_EOL;
		}
		$menuR = (array_key_exists('menu_right', $array_menu)) ? $this->menu($array_menu['menu_right']) : '';
		if(!empty($menuR)) $menuRight .= '<ul class="navbar-nav mr-auto">' . $menuR . '</ul>';
		$this->menu_right = $menuRight;
		//End of menu right

		//Set the main javascripts
		$mainJavascripts = 'jquery.min.js,bootstrap.min.js,main.js';
		loadJs($mainJavascripts, $this->from_cache, false);
		$this->MAIN_JAVASCRIPTS = '<script defer="defer" type="text/javascript" src="' . _FOLDER_URL_ . 'js/' . md5($mainJavascripts) . '.js" id="mainjs" data-appdir="' . _FOLDER_URL_ . '"></script>';

		//Set the content values to replace inside html template
		foreach(get_object_vars($this) AS $key => $value) {
			if(!is_object($value) && !is_array($value)) {
				$change = "{" . $key . "}";
				$this->template = str_replace($change, $value, $this->template);
			}
		}
		//Replace {breadcrumbs} string with actual breadcrumbs
		$this->template = str_replace('{breadcrumbs}', $this->breadcrumbs, $this->template);

		//Add javascripts in page
		if(count($this->js) > 0) {
			$js = array();
			$replacement = '';
			foreach($this->js AS $fjs) {
				if(strpos($fjs, '//') !== false) $replacement .= '		<script defer type="text/javascript" src="' . $fjs . '"></script>' . PHP_EOL;
			}
			foreach($this->js AS $fjs) {
				if(strpos($fjs, '//') === false) $js[] = $fjs;
			}
			if(count($js) > 0) {
				$scripts = implode(",", $js);
				loadJs($scripts, $this->from_cache);
				$replacement .= /** @lang text */
					'		<script defer="defer" type="text/javascript" src="' . _FOLDER_URL_ . 'js/' . md5($scripts) . '.js"></script>' . PHP_EOL;
			}
			$pos = strripos($this->template, "</script>");
			$this->template = substr_replace($this->template, "\n" . $replacement, $pos + 9, 0);
		}
		//Add styles in page
		$cssLR = '';
		if(count($this->css) > 0) {
			$scripts = '';
			$css = array();
			foreach($this->css AS $fcss) {
				if(strpos($fcss, '//') === false) $css[] = $fcss;
			}
			$replacement = '';
			if(count($css) > 0) $scripts .= implode(',', $css);
			foreach($this->css AS $fcss) {
				if(strpos($fcss, '//') !== false) $replacement .= '		<link rel="stylesheet" href="' . $fcss . '" />' . PHP_EOL;
			}
			$pos = strripos($this->template, "\t</body>");
			$this->template = substr_replace($this->template, $replacement, $pos, 0);
			if(!empty($scripts)) {
				loadCss($scripts, $this->from_cache, false);
				$cssLR = '<link rel="stylesheet" type="text/css" href="' . _FOLDER_URL_ . 'css/' . md5($scripts) . '.css" id="cssdeferred" />';
			}
		}
		$footer = /** @lang text */
			'		<noscript id="deferred-styles">
			<link rel="stylesheet" type="text/css" href="' . _FOLDER_URL_ . 'css/main.css" id="cssdeferredmain" />
			' . $cssLR . '
		</noscript>
		<script>
			var loadDeferredStyles = function() {
				var addStylesNode = document.getElementById("deferred-styles");
				var replacement = document.createElement("div");
				replacement.innerHTML = addStylesNode.textContent;
				document.body.appendChild(replacement);
				addStylesNode.parentElement.removeChild(addStylesNode);
			};
			var raf = requestAnimationFrame || mozRequestAnimationFrame || webkitRequestAnimationFrame || msRequestAnimationFrame;
			if (raf) raf(function() { window.setTimeout(loadDeferredStyles, 0); });
			else window.addEventListener(\'load\', loadDeferredStyles);
		</script>' . PHP_EOL;
		//Build header and content
		$this->header = substr($this->template, 0, strripos($this->template, "\t</head>") + 9);
		$this->content = substr($this->template, strlen($this->header), strlen($this->template) - strlen($this->header));
		if(stripos($this->content, '#testimonials#')) {
			$testimonials = '<div id="testimonialsControls" class="carousel slide" data-ride="carousel">
				<div class="carousel-inner">';
			$testimonialsEnt = new Model('testimonials');
			$testimonialsEnt->status = 1;
			$testimonialsEnt->order('RAND()');
			$testimonialsEnt = $testimonialsEnt->get();
			foreach($testimonialsEnt AS $index => $testimonial) {
				if(strlen($testimonial->short) > 200) {
					$pos = strpos($testimonial->short, ' ', 200);
					$testimonial_content = substr($testimonial->short, 0, $pos) . '...';
				}
				else $testimonial_content = $testimonial->short;
				$path_parts = pathinfo($testimonial->image);
				$fname = $path_parts['filename'];
				$extension = $path_parts['extension'];
				$thumbnail = $fname . '-160x160.' . $extension;

				$testimonials .= /** @lang text */
					'<div class="carousel-item' . ($index == 0 ? ' active' : '') . '">
						<img src="' . _FOLDER_URL_ . 'img/testimonials/' . $thumbnail . '" />
						<div>
							<div class="title testi-hone">' . $testimonial->name . '</div>
							<div class="subtitle testi-htwo">' . $testimonial->authority . '</div>
							<p class="testimonial-text">"' . nl2br($testimonial_content) . '"</p>
							<a href="' . _FOLDER_URL_ . 'testimonial' . $testimonial->id . '.html" class="readmore">' . __('read full text') . '</a>
						</div>
					</div>' . PHP_EOL;
			}
			$testimonials .= /** @lang text */
				'</div>
					<a class="carousel-control-prev" href="#testimonialsControls" role="button" data-slide="prev">
						<span class="carousel-control-prev-icon" aria-hidden="true"></span>
						<span class="sr-only">Previous</span>
					</a>
					<a class="carousel-control-next" href="#testimonialsControls" role="button" data-slide="next">
						<span class="carousel-control-next-icon" aria-hidden="true"></span>
						<span class="sr-only">Next</span>
					</a>
				</div>';
			$this->content = str_replace('#testimonials#', $testimonials, $this->content);
		}
		if(isset($footer)) $this->content = str_replace("\t</body>", $footer . "\t</body>", $this->content);
		if(strpos($this->content, '<option value="">' . __('Country') . '</option>') !== false) {
			$countriesOptions = '	<option value="">' . __('Country') . '</option>' . PHP_EOL;
			$countries = new Model('countries');
			$countries = $countries->get();
			foreach($countries AS $country) $countriesOptions .= '	<option value="' . $country->id . '">' . $country->name . '</option>' . PHP_EOL;
			$this->content = str_replace('<option value="">' . __('Country') . '</option>', $countriesOptions, $this->content);
		}
		return true;
	}

	/**
	 * Output html
	 */
	public function output() {
		$this->load_template();
		$md5url = md5(Util::getCurrentUrl());
		$redisKey = _APP_NAME_ . 'output|' . $md5url;
		if($this->rediscache && $this->rediscache->exists($redisKey)) $buffer = $this->rediscache->get($redisKey);
		else {
			$buffer = $this->header;
			$buffer .= $this->content;
			$buffer = self::sanitize_output($buffer);
			$buffer = self::csrfguard_replace_forms($buffer);
		}
		$cache = (extension_loaded('Memcached'))?\Utils\Memcached::getInstance():false;
		$inpageUrl = false;
		if($cache && !($inpageUrl = $cache->get(_APP_NAME_ . 'inpageurl' . $md5url))) {
			$redisKey = _APP_NAME_ . 'output|' . $md5url;
			if($this->rediscache && !$this->rediscache->exists($redisKey)) $this->rediscache->set($redisKey, $buffer);
			exec('php ' . _APP_DIR_ . 'cli.php buildcss ' . $md5url . ' > /dev/null 2>/dev/null &');
		}
		elseif($cache && $inpageUrl) $buffer = str_replace('</head>', '<style>' . $cache->get($inpageUrl) . '</style>', $buffer);
		echo $buffer;
	}

	/**
	 * @param $buffer
	 * @return mixed
	 */
	public static function sanitize_output($buffer) {
		$search = array('/\>[^\S ]+/s',  // strip whitespaces after tags, except space
			'/[^\S ]+\</s',  // strip whitespaces before tags, except space
			'/(\s)+/s'       // shorten multiple whitespace sequences
		);
		$replace = array('>', '<', '\\1');
		$buffer = preg_replace($search, $replace, $buffer);
		return $buffer;
	}
}