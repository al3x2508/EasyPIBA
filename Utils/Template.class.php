<?php
namespace Utils;

use Model\Model;
use Module\Users\Controller as UserController;

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
 * @property string menu_right
 * @property string MAIN_JAVASCRIPTS
 */
class Template
{
    /**
     * @var array
     */
    public $js = array();
    /**
     * @var array
     */
    public $css = array();
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
     * @var string
     */
    private $gradient = '';
    /**
     * @var bool
     */
    private $menu = false;
    /**
     * @var string
     */
    private $LANGUAGE = _DEFAULT_LANGUAGE_;
    /**
     * @var bool
     */
    private $from_cache = true;
    private $cache = false;

    private $isAmp = false;

    /**
     * Template constructor.
     * @param $filename
     */
    public function __construct($filename)
    {
        $this->cache = Util::getCache();
        $this->page_url .= $_SERVER['REQUEST_URI'];
        $this->filename = _TEMPLATE_DIR_ . $filename;
        //Build the social links variable for footer in case you need it
        $socialLinks = '';
        if (!empty(_FB_LINK_)) {
            $socialLinks .= '<a href="' . _FB_LINK_ . '" rel="nofollow" target="_blank">Facebook</a>';
        }
        if (!empty(_TWITTER_LINK_)) {
            $socialLinks .= '<a href="' . _TWITTER_LINK_ . '" rel="nofollow" target="_blank">Twitter</a>';
        }
        if (!empty(_LINKEDIN_LINK_)) {
            $socialLinks .= '<a href="' . _LINKEDIN_LINK_ . '" rel="nofollow" target="_blank">LinkedIn</a>';
        }
        if (!empty(_PINTEREST_LINK_)) {
            $socialLinks .= '<a href="' . _PINTEREST_LINK_ . '" rel="nofollow" target="_blank">Pinterest</a>';
        }
        if (!empty(_GPLUS_LINK_)) {
            $socialLinks .= '<a href="' . _GPLUS_LINK_ . '" rel="nofollow" target="_blank">Google+</a>';
        }
        if (!empty(_INSTAGRAM_LINK_)) {
            $socialLinks .= '<a href="' . _INSTAGRAM_LINK_ . '" rel="nofollow" target="_blank">Instagram</a>';
        }

        //Build the page content data variables
        $content_values = array(
            'url' => _ADDRESS_ . '/' . $this->page_url,
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
        if (!empty(_GOOGLEANALYTICSID_)) /** @noinspection CommaExpressionJS */ {
            $content_values['GOOGLE_ANALYTICS'] = /** @lang text */
                '<script>
			(function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function() {
						(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
					m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,\'script\',\'//www.google-analytics.com/analytics.js\',\'ga\');
			ga(\'create\', \'' . _GOOGLEANALYTICSID_ . '\', \'auto\');
			ga(\'send\', \'pageview\');
		</script>';
        }
        if (!empty(_FBAPPID_)) {
            $content_values['FBAPPID'] = '<meta property="fb:app_id" content="' . _FBAPPID_ . '" />';
        }
        foreach ($content_values AS $key => $value) {
            $this->$key = $value;
        }
        $this->colsize = '12';
    }

    /**
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $this->$key = $value;
    }

    /**
     * @param $links
     */
    public function setBreadcrumbs($links)
    {
        foreach ($links as $key => $value) {
            if ('/' . $key != $_SERVER['REQUEST_URI']) {
                $this->breadcrumbs = ($this->breadcrumbs == '')?/** @lang text */
                    '<li itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a href="' . _FOLDER_URL_ . $key . '" itemprop="url" title="' . $value . '"><span itemprop="title">' . $value . '</span></a></li>':$this->breadcrumbs . '<li itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a href="' . _FOLDER_URL_ . $key . '" itemprop="url" title="' . $value . '"><span itemprop="title">' . $value . '</span></a></li>';
            } else {
                $this->breadcrumbs = ($this->breadcrumbs == '')?/** @lang text */
                    '<li id="bselected" itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a href="' . _FOLDER_URL_ . $key . '" itemprop="url" title="' . $value . '"><span itemprop="title">' . $value . '</span></a></li>':$this->breadcrumbs . '<li id="bselected" itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a href="' . _FOLDER_URL_ . $key . '" itemprop="url" title="' . $value . '"><span itemprop="title">' . $value . '</span></a></li>';
            }
        }
    }

    /**
     * @param $links
     */
    public function setSidebar($links)
    {
        $this->colsize = 'sm-12 col-md-10';
        $this->sidebar = '<div class="sidebar sidebar col-sm-12 col-md-2"><ul>';
        foreach ($links as $key => $value) {
            if (!is_array($value)) {
                $selected = ('/' . $key == $_SERVER['REQUEST_URI'])?' class="selected"':'';
                $this->sidebar .= '<li' . $selected . '><a href="' . _FOLDER_URL_ . $key . '" title="' . $value . '">' . $value . '</a></li>';
            } else {
                $selected = ('/' . $key == $_SERVER['REQUEST_URI'])?' class="selected"':'';
                $this->sidebar .= '<li' . $selected . '><a href="' . _FOLDER_URL_ . $key . '" title="' . $value['title'] . '">' . $value['title'] . '</a><ul>';
                foreach ($value['submenu'] as $key1 => $value1) {
                    $selected = ('/' . $key1 == $_SERVER['REQUEST_URI'])?' class="selected"':'';
                    $this->sidebar .= '<li' . $selected . '><a href="' . _FOLDER_URL_ . $key1 . '" title="' . $value1 . '">' . $value1 . '</a></li>';
                }
                $this->sidebar .= '</ul></li>';
            }
        }
        $this->sidebar .= '</ul></div>';
    }

    /**
     * Output html
     */
    public function output($scripts = array(), $styles = array(), $stime = '')
    {
        $this->load_template($scripts, $styles);
        $md5url = md5(Util::getCurrentUrl());
        $buffer = $this->header;
        $buffer .= $this->content;
//        $buffer = self::sanitize_output($buffer);
        $buffer = self::csrfguard_replace_forms($buffer);
        $inpageUrl = false;
        if (!$this->isAmp) {
            if ($this->cache && !($inpageUrl = $this->cache->get(_CACHE_PREFIX_ . 'inpageurl|' . $this->LANGUAGE . '|' . $md5url))) {
                $cacheKey = _CACHE_PREFIX_ . 'output|' . $this->LANGUAGE . '|' . $md5url;
                if ($this->cache && !$this->cache->exists($cacheKey)) {
                    $this->cache->set($cacheKey, $buffer);
                }
                exec('php ' . _APP_DIR_ . 'cli.php buildcss "' . $this->LANGUAGE . '|' . $md5url . '" > /dev/null 2>/dev/null &');
            } elseif ($this->cache && $inpageUrl) {
                $buffer = str_replace('</head>', '<style>' . $this->cache->get($inpageUrl) . '</style></head>', $buffer);
            }
        } else {
            $buffer = str_ireplace(['<img', '<video', '/video>', '<audio', '/audio>'],
                ['<amp-img', '<amp-video', '/amp-video>', '<amp-audio', '/amp-audio>'], $buffer);
            $buffer = preg_replace_callback('/<amp-img(.*?)( ?\/)?>/', array($this, 'ampsize'), $buffer);
        }

        if(strpos($buffer, '{loading_time}') !== false) {
            $ftime = microtime();
            $ftime = explode(' ', $ftime);
            $ftime = $ftime[1] + $ftime[0];
            $total_time = round(($ftime - $stime), 4);
            $buffer = str_replace('{loading_time}', $total_time . __(' seconds'), $buffer);
        }

        echo $buffer;
    }

    /**
     * @return bool
     */
    public function load_template($scripts, $styles)
    {
        if (!file_exists($this->filename) || is_dir($this->filename)) {
            die("Error loading template ({$this->filename}).");
        }
        $this->isAmp = (strpos($this->page_url, 'amp/') !== false);
        //Load the html template
        $this->template = file_get_contents($this->filename);
        $this->HOME_LINK = _FOLDER_URL_;
        $this->FOLDER_URL = _FOLDER_URL_;
        if (!property_exists($this, 'css')) {
            $this->css = array();
        }
        if(!property_exists($this, 'noMainCss')) $this->css[] = 'main.css';
        require_once(dirname(__FILE__) . '/scripts.php');
        //Set the javascript variable for language
        $this->LANGUAGE = Util::getUserLanguage();
        $langUrl = ($this->LANGUAGE == _DEFAULT_LANGUAGE_)?'':$this->LANGUAGE . '/';

        if(UserController::getCurrentUser()) {
            /*if (!in_array('../../admin/js/combobox.js', $this->js)) {
                $this->js[] = '../../admin/js/combobox.js';
                $this->js[] = '../../admin/js/jsall.js';
            }*/
            $cacheKey = _CACHE_PREFIX_ . 'menuAuth|' . $langUrl . md5($_SERVER['REQUEST_URI']);
            if ($this->cache && $this->cache->exists($cacheKey)) {
                $this->template = str_replace('<nav id="mainMenu">', $this->cache->get($cacheKey), $this->template);
            } else {
                $module_routes = new Model('module_routes');
                $module_routes->type = 0;
                $module_routes->mustBeLoggedIn = 1;
                $module_routes->menu_position = 3;
                $module_routes->order('menu_position ASC, menu_parent ASC, menu_order ASC');
                $module_routes = $module_routes->get();
                $array_menu = array();
                foreach ($module_routes AS $module_route) {
                    $menuParent = (empty($module_route->menu_parent))?0:$module_route->menu_parent;
                    if (!arrayKeyExists($menuParent, $array_menu)) {
                        $array_menu[$menuParent] = array();
                    }
                    $pag = array(
                        'url' => _FOLDER_URL_ . $module_route->url,
                        'menu_text' => __($module_route->menu_text),
                        'submenu_text' => __($module_route->submenu_text),
                        'menu_parent' => $menuParent,
                        'menu_class' => $module_route->menu_class
                    );
                    //If page url is the same as the current url set link class as active
                    if ($_SERVER['REQUEST_URI'] == _FOLDER_URL_ . $module_route->url || $_SERVER['REQUEST_URI'] == _FOLDER_URL_ . $module_route->url) {
                        $pag['classes'] = 'active';
                    }
                    $array_menu[$menuParent][$module_route->menu_order] = $pag;
                    ksort($array_menu[$menuParent]);
                }
                $module_route = new Model('module_routes');
                $module_route->where(array('`j_modules`.name' => 'News'));
                $module_route = $module_route->get()[0];
                $menuParent = (empty($module_route->menu_parent))?0:$module_route->menu_parent;
                $array_menu[$menuParent][7] = array(
                    'url' => _FOLDER_URL_ . $module_route->url,
                    'menu_text' => __($module_route->menu_text),
                    'submenu_text' => __($module_route->submenu_text),
                    'menu_parent' => $menuParent,
                    'menu_class' => $module_route->menu_class
                );
                ksort($array_menu[$menuParent]);
                $this->authMenu($array_menu, 0, $cacheKey);

            }
            $this->template = str_replace('<div class="user-panel"><div class="pull-left info"><p>{adminName}</p></div></div>', '', $this->template);
            $this->loading_time_string = __('Loading time');
            $this->copyright = '2019 - ' . date('Y');
        }

                $cacheKey = _CACHE_PREFIX_ . 'menu|' . $langUrl;
        if ($this->cache && $this->cache->exists($cacheKey)) {
            $pagesArray = json_decode($this->cache->get($cacheKey));
        } else {
            $pages = new Model('pages');
            $pages->language = $this->LANGUAGE;
            $pages->visible = 1;
            $pagesArray = $pages->get();
            foreach (array_keys($pagesArray) AS $key) {
                unset($pagesArray[$key]->content);
            }
            if ($this->cache) {
                $this->cache->set($cacheKey, json_encode($pagesArray));
            }
        }
        foreach ($pagesArray AS $page) {
            if ($_SERVER['REQUEST_URI'] == _FOLDER_URL_ . $langUrl . $page->url && !empty($page->metaog)) {
                $metaog = json_decode($page->metaog, true);
                $this->template = preg_replace(/** @lang text */
                    '/(\<meta property\=\"og\:title\" content\=\")(.*)(\" \/\>)/', "$1{$metaog['title']}$3",
                    $this->template, 1);
                $this->template = preg_replace(/** @lang text */
                    '/(\<meta property\=\"og\:description\" content\=\")(.*)(\" \/\>)/', "$1{$metaog['description']}$3",
                    $this->template, 1);
                $replacementpic = '<meta property="og:image" content="http://' . _ADDRESS_ . '/img/' . $metaog['image'] . '" />';
                $this->template = preg_replace(/** @lang text */
                    '/\<meta property\=\"og\:image\" content\=\"(.*)\" \/\>/', $replacementpic, $this->template, 1);
            }
        }
        /*
         * Build the menu left
        */
        $array_menu = $this->getMenu();
        $this->menu = $this->menu($array_menu);
        //End of menu left
        /*
         * Build the menu right: Dropdown for languages, login button
         */
        $menuRight = '';
        $cacheKey = _CACHE_PREFIX_ . 'menuLanguage|' . $langUrl;
        if ($this->cache && $this->cache->exists($cacheKey)) {
            $pagesArray = json_decode($this->cache->get($cacheKey));
        } else {
            if (!isset($pages)) {
                $pages = new Model('pages');
            }
            $pages->clear();
            $pages->groupBy('language');
            $pages->order('language ASC');
            $pagesArray = $pages->get();
            foreach (array_keys($pagesArray) AS $key) {
                unset($pagesArray[$key]->content);
            }
            if ($this->cache) {
                $this->cache->set($cacheKey, json_encode($pagesArray));
            }
        }
        if (count($pagesArray) > 1) {
            $currentLanguage = array('native' => 'English', 'flag' => 'us');
            foreach ($pagesArray AS $page) {
                if ($page->language == $this->LANGUAGE) {
                    $currentLanguage = array('native' => $page->languages->name, 'flag' => $page->languages->code);
                }
            }
            if (!property_exists($this, 'css')) {
                $this->css = array();
            }
            if (!$this->isAmp) {
                $this->css[] = 'https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/0.8.2/css/flag-icon.min.css';
                $menuRight = '<div class="dropdown">
					<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="flag-icon flag-icon-' . ($currentLanguage['flag'] == 'en'?'us':$currentLanguage['flag']) . '"></span> ' . $currentLanguage['native'] . '</button>
  					<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">' . PHP_EOL;
                foreach ($pagesArray AS $page) {
                    $flag = ($page->language == 'en')?'us':$page->language;
                    $href = ($page->language == _DEFAULT_LANGUAGE_)?'':$page->language;
                    $menuRight .= "  						<a class=\"dropdown-item language\" href=\"" . _FOLDER_URL_ . $href . "\" data-language=\"{$page->language}\"><span class=\"flag-icon flag-icon-{$flag}\"></span> " . $page->languages->name . "</a>";
                }
                $menuRight .= '  					</div>
				</div>' . PHP_EOL;
            }
        }
        $menuR = (!$this->isAmp && arrayKeyExists('menu_right', $array_menu))?$this->menu($array_menu['menu_right']):'';
        if (!empty($menuR)) {
            $menuRight .= '<ul class="navbar-nav mr-auto">' . $menuR . '</ul>';
        }
        $this->menu_right = $menuRight;
        //End of menu right

        //Set the main javascripts
        if (!$this->isAmp) {
            $mainJavascripts = 'jquery.min.js,tether.min.js,bootstrap.min.js,jquery.easing.min.js,grayscale.min.js,main.js';
            loadJs($mainJavascripts, $this->from_cache, false);
            $this->MAIN_JAVASCRIPTS = '<script src="' . _FOLDER_URL_ . 'cache/js/' . md5($mainJavascripts) . '.js" id="mainjs" data-appdir="' . _FOLDER_URL_ . '"></script>';
        }

        //Set the content values to replace inside html template
        foreach (get_object_vars($this) AS $key => $value) {
            if (!is_object($value) && !is_array($value)) {
                $change = "{" . $key . "}";
                $this->template = str_replace($change, $value, $this->template);
            }
        }
        //Replace {breadcrumbs} string with actual breadcrumbs
        $this->template = str_replace('{breadcrumbs}', $this->breadcrumbs, $this->template);

        //Add javascripts in page
        $this->js = array_unique(array_merge($this->js, $scripts));
        if (count($this->js) > 0 && !$this->isAmp) {
            $js = array();
            $replacement = '';
            foreach ($this->js AS $fjs) {
                if (strpos($fjs, '//') !== false) {
                    $replacement .= '		<script src="' . $fjs . '"></script>' . PHP_EOL;
                }
            }
            foreach ($this->js AS $fjs) {
                if (strpos($fjs, '//') === false) {
                    $js[] = $fjs;
                }
            }
            if (count($js) > 0) {
                //$scripts = implode(",", $js);
                //loadJs($scripts, $this->from_cache);
                foreach ($js AS $script) {
                    $replacement .= /** @lang text */
                        '		<script src="' . ((strpos($script,
                                '/') === 0)?_FOLDER_URL_ . 'uploads/h5p' . $script:_FOLDER_URL_ . (strpos($script,
                                'Module/') === 0?'':'js/') . $script) . '"></script>' . PHP_EOL;
                }
            }
            $pos = strripos($this->template, "</script>");
            $this->template = substr_replace($this->template, "\n" . $replacement, $pos + 9, 0);
        }
        //Add styles in page
        $cssLR = '';
        if (($key = array_search('main.css', $this->css)) !== false) {
            unset($this->css[$key]);
        }
        $this->css = array_merge($this->css, $styles);
        if (count($this->css) > 0) {
            $scripts = '';
            $css = array();
            foreach ($this->css AS $fcss) {
                if (strpos($fcss, '//') === false) {
                    $css[] = $fcss;
                }
            }
            $replacement = '';
            if (count($css) > 0) {
                //$scripts .= implode(',', $css);
                foreach ($css AS $script) {
                    $replacement .= /** @lang text */
                        '		<link rel="stylesheet" type="text/css" href="' . ((strpos($script,
                                '/') === 0)?_FOLDER_URL_ . 'uploads/h5p' . $script:_FOLDER_URL_ . (strpos($script,
                                'Module/') === 0?'':'css/') . $script) . '" />' . PHP_EOL;
                }
            }
            foreach ($this->css AS $fcss) {
                if (strpos($fcss, '//') !== false) {
                    $replacement .= '		<link rel="stylesheet" href="' . $fcss . '" />' . PHP_EOL;
                }
            }
            $pos = strripos($this->template, "</body>");
            $this->template = substr_replace($this->template, $replacement, $pos, 0);
            if (!empty($scripts)) {
                //loadCss($scripts, $this->from_cache, false);
                //$cssLR = '<link rel="stylesheet" type="text/css" href="' . _FOLDER_URL_ . 'cache/css/' . md5($scripts) . '.css" id="cssdeferred" />';
            }
        }
        if (!$this->isAmp && !property_exists($this, 'noMainCss')) {
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
        }
        //Build header and content
        $this->header = substr($this->template, 0, strripos($this->template, "\t</head>") + 9);
        $this->content = substr($this->template, strlen($this->header),
            strlen($this->template) - strlen($this->header));
        if (stripos($this->content, '#testimonials#')) {
            $testimonials = '<div id="testimonialsControls" class="carousel slide" data-ride="carousel">
				<div class="carousel-inner">';
            $testimonialsEnt = new Model('testimonials');
            $testimonialsEnt->status = 1;
            $testimonialsEnt->order('RAND()');
            $testimonialsEnt = $testimonialsEnt->get();
            foreach ($testimonialsEnt AS $index => $testimonial) {
                if (strlen($testimonial->short) > 200) {
                    $pos = strpos($testimonial->short, ' ', 200);
                    $testimonial_content = substr($testimonial->short, 0, $pos) . '...';
                } else {
                    $testimonial_content = $testimonial->short;
                }
                $path_parts = pathinfo($testimonial->image);
                $fname = $path_parts['filename'];
                $extension = $path_parts['extension'];
                $thumbnail = $fname . '-160x160.' . $extension;

                $testimonials .= /** @lang text */
                    '<div class="carousel-item' . ($index == 0?' active':'') . '">
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
        if (isset($footer)) {
            $this->content = str_replace("</body>", $footer . "</body>", $this->content);
        }
        if (strpos($this->content, '<option value="">' . __('Country') . '</option></select>') !== false) {
            $countriesOptions = '	<option value="">' . __('Country') . '</option>' . PHP_EOL;
            $countries = new Model('countries');
            $countries = $countries->get();
            foreach ($countries AS $country) {
                $countriesOptions .= '	<option value="' . $country->id . '">' . $country->name . '</option>' . PHP_EOL;
            }
            $this->content = str_replace('<option value="">' . __('Country') . '</option>',
                $countriesOptions . '</select>', $this->content);
        }
        return true;
    }

    /**
     * @param $mArr
     * @param int $level
     * @return string
     */
    private function menu($mArr, $level = 0)
    {
        $menu = '';
        foreach ($mArr AS $parent => $pages) {
            if ($parent !== 'menu_right' && ($parent === $level)) {
                foreach ($pages AS $page) {
                    //If it has submenu build a dropdown
                    if (arrayKeyExists('id', $page) && arrayKeyExists($page['id'], $mArr)) {
                        $cssClasses = (arrayKeyExists('classes', $page))?' ' . $page['classes']:'';
                        $menu .= '<li class="nav-item dropdown' . $cssClasses . '">
							<a class="nav-link js-scroll-trigger dropdown-toggle" href="#" id="menu' . $page['id'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' . $page['menu_text'] . '</a>
							<div class="dropdown-menu" aria-labelledby="menu' . $page['id'] . '">
								<a class="dropdown-item" href="' . $page['url'] . '">' . $page['submenu_text'] . '</a>' . $this->menu($mArr,
                                $page['id']) . '
							</div>
						</li>';
                    } else {
                        $cssClasses = (arrayKeyExists('classes', $page))?' ' . $page['classes']:'';
                        if ($level === 0) {
                            $menu .= '<li class="nav-item' . $cssClasses . '"><a class="nav-link js-scroll-trigger" href="' . $page['url'] . '">' . $page['menu_text'] . '</a></li>';
                        } else {
                            $menu .= '<a class="dropdown-item" href="' . $page['url'] . '">' . $page['menu_text'] . '</a>';
                        }
                    }
                }
            }
        }
        return $menu;
    }

    public function authMenu($mArr, $level = 0, $cacheKey = '') {
        $replace='<nav id="mainMenu">';
        $menu = '';
        foreach($mArr AS $parent => $pages) {
            if($parent == $level) {
                foreach($pages AS $page) {
                    if(arrayKeyExists('id', $page) && arrayKeyExists($page['id'], $mArr)) {
                        $hrefHtml = $page['menu_text'];
                        $hrefHtmlSub = $page['submenu_text'];
                        if(!empty($page['menu_class'])) {
                            $hrefHtml = '<i class="' . $page['menu_class'] . '"></i> <span>' . $hrefHtml . '</span>';
                            $hrefHtmlSub = '<i class="' . $page['menu_class'] . '"></i> <span>' . $hrefHtmlSub . '</span>';
                        }
                        $menu .= '<li class="treeview';
                        if($_SERVER['REQUEST_URI'] == '/' . $page['url']) {
                            if(!arrayKeyExists('classes', $page)) $page['classes'] = 'active';
                            else $page['classes'] .= ' active';
                        }
                        if(arrayKeyExists('classes', $page)) $menu .= ' ' . $page['classes'];
                        $menu .= '">
                                                <a href="#"><span>' . $hrefHtml . '</span> <i class="fa fa-angle-left pull-right"></i></a>
                                                <ul class="treeview-menu" style="display: none;">
                                                        <li><a href="' . $page['url'] . '">' . $hrefHtmlSub . '</a></li>' . $this->authMenu($mArr, $page['id']) . '
                                                </ul>
                                        </li>';
                    }
                    else {
                        if($page['url'] == '/help') $menu .= '<li><a href="#" data-toggle="modal" data-target="#wizard" data-backdrop="static" data-keyboard="false"><i class="fal fa-magic"></i> ' . _('Wizard') . '</a></li>';
                        $hrefHtml = $page['menu_text'];
                        if(!empty($page['menu_class'])) $hrefHtml = '<i class="' . $page['menu_class'] . '"></i> <span>' . $hrefHtml . '</span>';
                        $menu .= '<li';
                        if($_SERVER['REQUEST_URI'] == $page['url'] || strpos($_SERVER['REQUEST_URI'], $page['url'] . '/') === 0) {
                            if(!arrayKeyExists('classes', $page)) $page['classes'] = 'active';
                            else $page['classes'] .= ' active';
                        }
                        if(arrayKeyExists('classes', $page)) $menu .= ' class="' . $page['classes'] . '"';
                        $menu .= '><a href="' . $page['url'] . '">' . $hrefHtml . '</a></li>';
                    }
                }
            }
        }
        $replacement='<nav id="mainMenu"><ul class="sidebar-menu">' . $menu. '</ul>';
        if($this->cache) $this->cache->set($cacheKey, $replacement);
        $this->template = str_replace($replace, $replacement, $this->template);
    }

    /**
     * @param $buffer
     * @return mixed
     */
    public static function sanitize_output($buffer)
    {
        $search = array(
            '/\>[^\S ]+/s',  // strip whitespaces after tags, except space
            '/[^\S ]+\</s',  // strip whitespaces before tags, except space
            '/(\s)+/s'       // shorten multiple whitespace sequences
        );
        $replace = array('>', '<', '\\1');
        $buffer = preg_replace($search, $replace, $buffer);
        return $buffer;
    }

    /**
     * Protect forms by CSRF
     * @param $form_data_html
     * @return mixed
     */
    private function csrfguard_replace_forms($form_data_html)
    {
        preg_match_all("/<form(.*?)>(.*?)<\\/form>/is", $form_data_html, $matches, PREG_SET_ORDER);
        if (is_array($matches)) {
            foreach ($matches as $m) {
                if (strpos($m[1], "nocsrf") !== false) {
                    continue;
                }
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

    public function ampsize($matches)
    {
        preg_match('/src\=\"([^\"]*)/', $matches[1], $src);
        $size = Util::getImageSize($src[1]);
        return '<amp-img' . $matches[1] . ' width="' . $size[0] . '" height="' . $size[1] . '"></amp-img>';
    }

    public function getMenu()
    {
        $langUrl = ($this->LANGUAGE == _DEFAULT_LANGUAGE_)?'':$this->LANGUAGE . '/';
        $pages = new Model('pages');
        $pages->language = $this->LANGUAGE;
        $pages->visible = 1;
        $pages->menu_order = array('0', '!=');
        $pages->order('menu_parent ASC, menu_order ASC');
        $array_pages = $pages->get();
        $array_menu = array();
        foreach ($array_pages AS $page) {
            if (!arrayKeyExists($page->menu_parent, $array_menu)) {
                $array_menu[$page->menu_parent] = array();
            }
            $pag = array(
                'id' => $page->id,
                'url' => _FOLDER_URL_ . $langUrl . $page->url,
                'menu_text' => $page->menu_text,
                'submenu_text' => $page->submenu_text,
                'menu_parent' => $page->menu_parent
            );
            //If page url is the same as the current url set link class as active
            if ($_SERVER['REQUEST_URI'] == _FOLDER_URL_ . $langUrl . $page->url) {
                $pag['classes'] = 'active';
            }
            $array_menu[$page->menu_parent][] = $pag;
        }
        $module_routes = new Model('module_routes');
        $module_routes->type = 0;
        if (!UserController::getCurrentUser()) {
            $module_routes->mustBeLoggedIn = 0;
        } else {
            $module_routes->hiddenForLoggedIn = 0;
        }
        $module_routes->menu_position = array(0, '>');
        $module_routes->order('menu_position ASC, menu_parent ASC, menu_order ASC');
        $module_routes = $module_routes->get();
        foreach ($module_routes AS $module_route) {
            if ($module_route->menu_position !== 2) {
                if ($this->LANGUAGE == _DEFAULT_LANGUAGE_) {
                    $menuParent = (empty($module_route->menu_parent))?0:$module_route->menu_parent;
                    if ($menuParent === 0) {
                        $module_route->menu_order += count($array_pages);
                    }
                    if (!arrayKeyExists($menuParent, $array_menu)) {
                        $array_menu[$menuParent] = array();
                    }
                    $pag = array(
                        'url' => _FOLDER_URL_ . $langUrl . $module_route->url,
                        'menu_text' => $module_route->menu_text,
                        'submenu_text' => $module_route->submenu_text,
                        'menu_parent' => $menuParent
                    );
                    //If page url is the same as the current url set link class as active
                    if ($_SERVER['REQUEST_URI'] == _FOLDER_URL_ . $langUrl . $module_route->url || $_SERVER['REQUEST_URI'] == _FOLDER_URL_ . $langUrl . $module_route->url) {
                        $pag['classes'] = 'active';
                    }
                    $array_menu[$menuParent][$module_route->menu_order] = $pag;
                    ksort($array_menu[$menuParent]);
                }
            } else {
                $menuParent = (empty($module_route->menu_parent))?0:$module_route->menu_parent;
                if (!arrayKeyExists('menu_right', $array_menu)) {
                    $array_menu['menu_right'] = array();
                }
                if (!arrayKeyExists($menuParent, $array_menu['menu_right'])) {
                    $array_menu['menu_right'][$menuParent] = array();
                }
                $pag = array(
                    'url' => _FOLDER_URL_ . $langUrl . $module_route->url,
                    'menu_text' => $module_route->menu_text,
                    'submenu_text' => $module_route->submenu_text,
                    'menu_parent' => $menuParent
                );
                $pag['id'] = $module_route->url;
                //If page url is the same as the current url set link class as active
                if ($_SERVER['REQUEST_URI'] == _FOLDER_URL_ . $langUrl . $module_route->url || $_SERVER['REQUEST_URI'] == _FOLDER_URL_ . $langUrl . $module_route->url) {
                    $pag['classes'] = 'active';
                }
                $array_menu['menu_right'][$menuParent][] = $pag;
            }
        }
        return $array_menu;
    }
}