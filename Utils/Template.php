<?php

namespace Utils;

use Model\Countries;
use Model\Pages;
use Model\Testimonials;
use Module\Pages\Controller;

class Template
{
    private string $breadcrumbs = '';
    private $cache;
    private bool $from_cache = true;
    private bool $isAmp = false;
    private string $sidebar = '';

    protected string $filename;
    protected string $page_url = '';

    public string $js = '';
    public string $css = '';
    private string $colsize;

    /**
     * Template constructor.
     */
    public function __construct(string $filename)
    {
        $this->cache = Util::getCache();
        $this->page_url = Util::getCurrentUrl();
        $this->filename = $_ENV['TEMPLATE_DIR'].$filename;
        $this->colsize = '12';
    }

    private function buildSocialLinks()
    {
        //Build the social links variable for footer in case you need it
        $socialLinks = '';
        if (!empty($_ENV['YT_LINK'])) {
            $socialLinks .= '<li><a href="'.$_ENV['YT_LINK']
                .'" rel="nofollow" class="socialIcon tips" title="Youtube '
                .$_ENV['APP_NAME'].'" target="_blank"><i
                                    class="fab fa-youtube"></i></a></li>';
        }
        if (!empty($_ENV['FB_LINK'])) {
            $socialLinks .= '<li><a href="'.$_ENV['FB_LINK']
                .'" rel="nofollow" class="socialIcon tips" title="Facebook '
                .$_ENV['APP_NAME'].'" target="_blank"><i
                                    class="fab fa-facebook-f"></i></a></li>';
        }
        if (!empty($_ENV['TWITTER_LINK'])) {
            $socialLinks .= '<li><a href="'.$_ENV['TWITTER_LINK']
                .'" rel="nofollow" class="socialIcon tips" title="Twitter '
                .$_ENV['APP_NAME'].'" target="_blank"><i
                                    class="fab fa-twitter"></i></a></li>';
        }
        if (!empty($_ENV['LINKEDIN_LINK'])) {
            $socialLinks .= '<li><a href="'.$_ENV['LINKEDIN_LINK']
                .'" rel="nofollow" class="socialIcon tips" title="LinkedIn '
                .$_ENV['APP_NAME'].'" target="_blank"><i
                                    class="fab fa-linkedin"></i></a></li>';
        }
        if (!empty($_ENV['INSTAGRAM_LINK'])) {
            $socialLinks .= '<li><a href="'.$_ENV['INSTAGRAM_LINK']
                .'" rel="nofollow" class="socialIcon tips" title="Instagram '
                .$_ENV['APP_NAME'].'" target="_blank"><i
                                    class="fab fa-instagram"></i></a></li>';
        }
        if (!empty($_ENV['PINTEREST_LINK'])) {
            $socialLinks .= '<li><a href="'.$_ENV['PINTEREST_LINK']
                .'" rel="nofollow" class="socialIcon tips" title="Pinterest '
                .$_ENV['APP_NAME'].'" target="_blank"><i
                                    class="fab fa-pinterest"></i></a></li>';
        }
        if (!empty($_ENV['TUMBLR_LINK'])) {
            $socialLinks .= '<li><a href="'.$_ENV['TUMBLR_LINK']
                .'" rel="nofollow" class="socialIcon tips" title="Tumblr '
                .$_ENV['APP_NAME'].'" target="_blank"><i
                                    class="fab fa-tumblr"></i></a></li>';
        }
        if (!empty($_ENV['BLOG_LINK'])) {
            $socialLinks .= '<li><a href="'.$_ENV['BLOG_LINK']
                .'" rel="nofollow" class="socialIcon tips" title="Blog '
                .$_ENV['APP_NAME'].'" target="_blank"><i
                                    class="fal fa-blog"></i></a></li>';
        }
        if (!empty($_ENV['GPLUS_LINK'])) {
            $socialLinks .= '<li><a href="'.$_ENV['GPLUS_LINK']
                .'" rel="nofollow" class="socialIcon tips" title="Google+ '
                .$_ENV['APP_NAME'].'" target="_blank"><i
                                    class="fab fa-google-plus"></i></a></li>';
        }
        return $socialLinks;
    }

    public function __get($key)
    {
        return $this->$key;
    }

    public function __set($key, $value)
    {
        $this->$key = $value;
    }

    public function setBreadcrumbs($links)
    {
        foreach ($links as $key => $value) {
            if ('/'.$key != $_SERVER['REQUEST_URI']) {
                $this->breadcrumbs = ($this->breadcrumbs == '')
                    ? /** @lang text */
                    '<li itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a href="'
                    .$_ENV['FOLDER_URL'].$key.'" itemprop="url" title="'.$value
                    .'"><span itemprop="title">'.$value.'</span></a></li>'
                    : $this->breadcrumbs
                    .'<li itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a href="'
                    .$_ENV['FOLDER_URL'].$key.'" itemprop="url" title="'.$value
                    .'"><span itemprop="title">'.$value.'</span></a></li>';
            } else {
                $this->breadcrumbs = ($this->breadcrumbs == '')
                    ? /** @lang text */
                    '<li id="bselected" itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a href="'
                    .$_ENV['FOLDER_URL'].$key.'" itemprop="url" title="'.$value
                    .'"><span itemprop="title">'.$value.'</span></a></li>'
                    : $this->breadcrumbs
                    .'<li id="bselected" itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a href="'
                    .$_ENV['FOLDER_URL'].$key.'" itemprop="url" title="'.$value
                    .'"><span itemprop="title">'.$value.'</span></a></li>';
            }
        }
    }

    public function setSidebar($links)
    {
        $this->container = '<div class="container py-5">';
        $this->eocontainer = '</div>';
        $this->colsize = 'sm-12 col-lg-9';
        $this->sidebar = '<div class="sidebar col-sm-12 col-lg-3 px-lg-3 mt-5">
                <button type="button" class="navbar-toggler d-block d-md-none">
                    <i class="fal fa-chevron-right"></i>
                </button>
            <ul>';
        foreach ($links as $key => $value) {
            $selected = ('/'.$key == $_SERVER['REQUEST_URI'])
                ? ' class="selected"' : '';
            if (!is_array($value)) {
                $this->sidebar .= '<li'.$selected.'><a href="'
                    .$_ENV['FOLDER_URL'].$key.'" title="'.$value.'">'.$value
                    .'</a></li>';
            } else {
                $this->sidebar .= '<li'.$selected.'><a href="'
                    .$_ENV['FOLDER_URL'].$key.'" title="'.$value['title'].'">'
                    .$value['title'].'</a><ul>';
                foreach ($value['submenu'] as $key1 => $value1) {
                    $selected = ('/'.$key1 == $_SERVER['REQUEST_URI'])
                        ? ' class="selected"' : '';
                    if (!is_array($value1)) {
                        $this->sidebar .= '<li'.$selected.'><a href="'
                            .$_ENV['FOLDER_URL'].$key1.'" title="'.$value1.'">'
                            .$value1.'</a></li>';
                    } else {
                        $sidebarId = preg_replace('/[^a-zA-Z0-9]/', '', $key1);
                        $this->sidebar .= '<li'.$selected.'>
                            <a href="#" title="'.$value1['title']
                            .'" class="dropdown-toggle collapsed" data-bs-toggle="collapse" data-bs-target="#sidebar'
                            .$sidebarId.'" aria-controls="sidebar'.$sidebarId
                            .'" aria-expanded="false" aria-label="Deschide meniul">'
                            .$value1['title'].'</a>
                            <ul class="collapse" id="sidebar'.$sidebarId.'">';
                        foreach ($value1['submenu'] as $key2 => $value2) {
                            $selected = ('/'.$key2 == $_SERVER['REQUEST_URI'])
                                ? ' class="selected"' : '';
                            $this->sidebar .= '<li'.$selected.'><a href="'
                                .$_ENV['FOLDER_URL'].$key2.'" title="'.$value2
                                .'">'.$value2.'</a></li>';
                        }
                        $this->sidebar .= '</ul></li>';
                    }
                }
                $this->sidebar .= '</ul></li>';
            }
        }
        $this->sidebar .= '</ul></div>';
    }

    public function setRSidebar()
    {
        $this->rsidebar = file_get_contents($_ENV['TEMPLATE_DIR']
            .'investitori_dreapta.html');
        $this->colsize = 'sm-12 col-lg-6';
    }

    private function menu($mArr, int $level = 0): string
    {
        $menu = '';
        foreach ($mArr as $parent => $pages) {
            if ($parent !== 'menu_right' && ($parent === $level)) {
                foreach ($pages as $page) {
                    //If it has submenu build a dropdown
                    $cssClasses = (arrayKeyExists('classes', $page)) ? ' '
                        .$page['classes'] : '';
                    if (arrayKeyExists('id', $page)
                        && arrayKeyExists($page['id'], $mArr)
                    ) {
                        $menu .= '<li class="nav-item dropdown'.$cssClasses.'">
                            <a class="nav-link dropdown-toggle menu" href="#" id="menu'
                            .$page['id']
                            .'" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'
                            .$page['menu_text'].' <i class="fal fa-chevron-right"></i></a>
                            <ul class="dropdown-menu" aria-labelledby="menu'
                            .$page['id'].'">
                                <li><a href="#" class="meniu-back"><i class="fal fa-chevron-left"></i> Înapoi</a></li>
                                <li><a href="'.$page['url'].'">'
                            .$page['submenu_text'].'</a></li>
                                '.$this->menu($mArr, $page['id']).'
                            </ul>
                        </li>';
                    } else {
                        if ($page['url'] == '/login') {
                            $cssClasses .= ' login-link';
                        }
                        if ($level === 0) {
                            $menu .= '<li class="nav-item'.$cssClasses
                                .'"><a class="nav-link" href="'.$page['url']
                                .'">'.$page['menu_text'].'</a></li>';
                        } else {
                            $menu .= '<li><a href="'.$page['url'].'">'
                                .$page['menu_text'].'</a></li>';
                        }
                    }
                }
            }
        }
        return $menu;
    }

    /**
     * Protect forms by CSRF
     *
     * @param $form_data_html
     *
     * @return mixed
     */
    private function csrfguard_replace_forms($form_data_html)
    {
        preg_match_all("/<form(.*?)>(.*?)<\\/form>/is", $form_data_html,
            $matches, PREG_SET_ORDER);
        if (is_array($matches)) {
            foreach ($matches as $m) {
                if (strpos($m[1], "nocsrf") !== false) {
                    continue;
                }
                $name = "CSRFGuard_".mt_rand(0, mt_getrandmax());
                $token = Util::csrfguard_generate_token($name);
                $form_data_html = str_replace($m[0], /** @lang text */
                    "<form$m[1]>
<input type='hidden' name='CSRFName' value='$name' />
<input type='hidden' name='CSRFToken' value='$token' />$m[2]</form>",
                    $form_data_html);
            }
        }
        return $form_data_html;
    }

    public function load_template(): bool
    {
        if (!file_exists($this->filename) || is_dir($this->filename)) {
            die("Error loading template ($this->filename).");
        }
        $this->isAmp = (strpos($this->page_url, 'amp/') !== false);
        //Load the html template
        $template = '';
        $template = file_get_contents($this->filename);
        $jsArray = explode(",", $this->js);
        $cssArray = explode(",", $this->css);
        $cssArray[] = 'main.css';
        require_once(dirname(__FILE__).'/scripts.php');
        $userLanguage = Util::getUserLanguage();
        //Set the javascript variable for language
        $_ENV['LANGUAGE'] = $userLanguage;
        $langUrl = ($userLanguage == $_ENV['DEFAULT_LANGUAGE']) ? ''
            : $userLanguage.'/';
        $cacheKey = $_ENV['CACHE_PREFIX'].'menu|'.$langUrl;
        if ($this->cache && $this->cache->exists($cacheKey)) {
            $pagesArray = json_decode($this->cache->get($cacheKey));
        } else {
            $pages = new Pages();
            $pages->language = $userLanguage;
            $pages->visible = 1;
            $pagesArray = $pages->get();
            foreach (array_keys($pagesArray) as $key) {
                unset($pagesArray[$key]->content);
            }
            if ($this->cache) {
                $this->cache->set($cacheKey, json_encode($pagesArray));
            }
        }
        foreach ($pagesArray as $page) {
            if ($_SERVER['REQUEST_URI'] == $_ENV['FOLDER_URL'].$langUrl
                .$page->url
                && !empty($page->metaog)
            ) {
                $metaog = json_decode($page->metaog, true);
                $template = preg_replace(/** @lang text */
                    '/(\<meta property\=\"og\:title\" content\=\")(.*)(\" \/\>)/',
                    "$1{$metaog['title']}$3", $template, 1);
                $template = preg_replace(/** @lang text */
                    '/(\<meta property\=\"og\:description\" content\=\")(.*)(\" \/\>)/',
                    "$1{$metaog['description']}$3", $template, 1);
                $replacementpic = '<meta property="og:image" content="'
                    .$_ENV['ADDRESS'].'/img/'.$metaog['image'].'" />';
                $template = preg_replace(/** @lang text */
                    '/\<meta property\=\"og\:image\" content\=\"(.*)\" \/\>/',
                    $replacementpic, $template, 1);
            }
        }
        /*
         * Build the menu left
        */
        $array_menu = Controller::getMenu();
        //End of menu left
        /*
         * Build the menu right: Dropdown for languages, login button
         */
        $menuRight = '';
        $cacheKey = $_ENV['CACHE_PREFIX'].'menuLanguage|'.$langUrl;
        if ($this->cache && $this->cache->exists($cacheKey)) {
            $pagesArray = json_decode($this->cache->get($cacheKey));
        } else {
            if (!isset($pages)) {
                $pages = new Pages();
            }
            $pages->clear();
            $pages->groupBy('language');
            $pages->order('language ASC');
            $pagesArray = $pages->get();
            foreach (array_keys($pagesArray) as $key) {
                unset($pagesArray[$key]->content);
            }
            if ($this->cache) {
                $this->cache->set($cacheKey, json_encode($pagesArray));
            }
        }
        if (count($pagesArray) > 1) {
            $currentLanguage = array('native' => 'English', 'flag' => 'us');
            foreach ($pagesArray as $page) {
                if ($page->language == $userLanguage) {
                    $currentLanguage = array(
                        'native' => $page->languages->name,
                        'flag'   => $page->languages->code
                    );
                }
            }
            if (!$this->isAmp) {
                $cssArray[]
                    = 'https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/0.8.2/css/flag-icon.min.css';
                $menuRight = '<div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="flag-icon flag-icon-'
                    .($currentLanguage['flag'] == 'en' ? 'us'
                        : $currentLanguage['flag']).'"></span> '
                    .$currentLanguage['native'].'</button>
                      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">'
                    .PHP_EOL;
                foreach ($pagesArray as $page) {
                    $flag = ($page->language == 'en') ? 'us' : $page->language;
                    $href = ($page->language == $_ENV['DEFAULT_LANGUAGE']) ? ''
                        : $page->language;
                    $menuRight .= "                          <a class=\"dropdown-item language\" href=\""
                        .$_ENV['FOLDER_URL'].$href
                        ."\" data-language=\"$page->language\"><span class=\"flag-icon flag-icon-$flag\"></span> "
                        .$page->languages->name."</a>";
                }
                $menuRight .= '                      </div>
                </div>'.PHP_EOL;
            }
        }
        $menuR = (!$this->isAmp && arrayKeyExists('menu_right', $array_menu))
            ? $this->menu($array_menu['menu_right']) : '';
        if (!empty($menuR)) {
            $menuRight .= '<ul class="navbar-nav mr-auto">'.$menuR.'</ul>';
        }
        $this->menu_right = $menuRight;
        //End of menu right

        //Set the main javascripts
        if (!$this->isAmp) {
            /*$mainJavascripts
                = 'jquery.min.js,bootstrap.bundle.min.js,fancybox.umd.js,main.js';
            loadJs($mainJavascripts, $this->from_cache, false);
            $this->MAIN_JAVASCRIPTS = '<script defer="defer" src="'
                .$_ENV['FOLDER_URL'].'cache/js/'.md5($mainJavascripts)
                .'.js" id="mainjs" data-appdir="'.$_ENV['FOLDER_URL']
                .'"></script>';*/

            $this->MAIN_JAVASCRIPTS = '<script defer="defer" src="'
                .$_ENV['FOLDER_URL']
                .'js/jquery.min.js" id="mainjs" data-appdir="'
                .$_ENV['FOLDER_URL']
                .'"></script>
                <script defer="defer" src="'
                .$_ENV['FOLDER_URL'].'js/bootstrap.min.js"></script>
                <script defer="defer" src="'
                .$_ENV['FOLDER_URL'].'js/fancybox.umd.js"></script>
                <script defer="defer" src="'
                .$_ENV['FOLDER_URL'].'js/main.js"></script>';
            /*$this->MAIN_JAVASCRIPTS = '<script defer="defer" src="'
                .$_ENV['FOLDER_URL']
                .'js/dist/main.js" id="mainjs" data-appdir="'
                .$_ENV['FOLDER_URL']
                .'"></script>';*/
        }

        if (!empty($this->sidebar)) {
            $template = str_replace("bd-content", "bd-content p-lg-3",
                $template);
        }

        $template = str_replace("{content}", $this->content,
            $template);

        //Build the page content data variables
        $content_values = array(
            'title'                 => $this->title,
            'description'           => $this->description,
            'keywords'              => $this->keywords,
            'ogimage'               => $this->ogimage,
            'page_url'              => $this->page_url,
            'url'                   => $_ENV['ADDRESS'].$this->page_url,
            'menu'                  => $this->menu($array_menu),
            'LOGO'                  => $_ENV['FOLDER_URL'].'img/'.$_ENV['LOGO'],
            'SOCIAL_LINKS'          => $this->buildSocialLinks(),
            'GOOGLE_ANALYTICS_META' => '',
            'FBAPP_META'            => '',
            'AMP_META'              => $this->AMP_META,
            'MAIN_JAVASCRIPTS'      => $this->MAIN_JAVASCRIPTS,
        );

        if (!empty($_ENV['GOOGLEANALYTICSID'])) {
            $content_values['GOOGLE_ANALYTICS_META'] =
                '<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=' . $_ENV['GOOGLEANALYTICSID'] . '"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag(\'js\', new Date());

  gtag(\'config\', \'' . $_ENV['GOOGLEANALYTICSID'] . '\');
</script>';
        }
        if (!empty($_ENV['FBAPPID'])) {
            $content_values['FBAPP_META']
                = '<meta property="fb:app_id" content="'
                .$_ENV['FBAPPID'].'" />';
        }
        foreach ($content_values as $key => $value) {
            if (!is_object($value) && !is_array($value)) {
                $change = "{".$key."}";
                $template = str_replace($change, $value, $template);
            }
        }
        foreach ($_ENV as $key => $value) {
            if (!is_object($value) && !is_array($value)) {
                $change = "{".$key."}";
                $template = str_replace($change, $value, $template);
            }
        }


        //Replace {breadcrumbs} string with actual breadcrumbs
        $template = str_replace('{breadcrumbs}', $this->breadcrumbs,
            $template);

        //Add javascripts in page
        if (count($jsArray)) {
            $js = array();
            $replacement = '';
            foreach ($jsArray as $fjs) {
                if (strpos($fjs, '//') !== false) {
                    $replacement .= '        <script src="'.$fjs.'"></script>'
                        .PHP_EOL;
                }
            }
            foreach ($jsArray as $fjs) {
                if (strpos($fjs, '//') === false) {
                    $js[] = $fjs;
                }
            }
            if (count($js) > 0) {
                $scripts = implode(",", $js);
                loadJs($scripts, $this->from_cache);
                $replacement
                    .= /** @lang text */
                    '        <script defer="defer" src="'.$_ENV['FOLDER_URL']
                    .'cache/js/'.md5($scripts).'.js" data-custom="1"></script>'
                    .PHP_EOL;
            }
            $pos = strripos($template, "</script>");
            $template = substr_replace($template, "\n".$replacement,
                $pos + 9, 0);
        }
        //Add styles in page
        $cssLR = '';
        if (($key = array_search('main.css', $cssArray)) !== false) {
            unset($cssArray[$key]);
        }
        if (count($cssArray)) {
            $scripts = '';
            $css = array();
            foreach ($cssArray as $fcss) {
                if (strpos($fcss, '//') === false) {
                    $css[] = $fcss;
                }
            }
            $replacement = '';
            if (count($css) > 0) {
                $scripts .= implode(',', $css);
            }
            foreach ($cssArray as $fcss) {
                if (strpos($fcss, '//') !== false) {
                    $replacement .= '        <link rel="stylesheet" href="'
                        .$fcss.'" />'.PHP_EOL;
                }
            }
            $pos = strripos($template, "</body>");
            $template = substr_replace($template, $replacement,
                $pos, 0);
            if (!empty($scripts)) {
                loadCss($scripts, $this->from_cache, false);
                $cssLR = '<link rel="stylesheet" type="text/css" href="'
                    .$_ENV['FOLDER_URL'].'cache/css/'.md5($scripts)
                    .'.css" id="cssdeferred" />';
            }
        }
        if (!$this->isAmp) {
            $scanForMainCss = glob($_ENV['APP_DIR'].'/cache/css/main*.css');
            $cssFiles = '';
            $mediaMax = [];
            $mediaMin = [];
            foreach ($scanForMainCss as $file) {
                preg_match('/.*(min|max)(.*)px\.css/', $file, $output_array);
                if (count($output_array)) {
                    if ($output_array[1] === 'min') {
                        $mediaMin[$output_array[2]] = basename($file);
                    } else {
                        $mediaMax[$output_array[2]] = basename($file);
                    }
                }
            }
            ksort($mediaMin);
            krsort($mediaMax);
            foreach ($mediaMin as $min => $filename) {
                $cssFiles .= '<link rel="stylesheet" media="screen and (min-width: '
                    .$min.'px)" href="'.$_ENV['FOLDER_URL'].'cache/css/'
                    .$filename.'">';
            }
            foreach ($mediaMax as $max => $filename) {
                $cssFiles .= '<link rel="stylesheet" media="screen and (max-width: '
                    .$max.'px)" href="'.$_ENV['FOLDER_URL'].'cache/css/'
                    .$filename.'">';
            }
            $footer
                = /** @lang text */
                '        <noscript id="deferred-styles">
            '.$cssLR.'
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
        </script>
        <link rel="stylesheet" type="text/css" href="'.$_ENV['FOLDER_URL'].'css/main.css" id="deferred-styles" />
        '.$cssFiles.PHP_EOL;
        }
        //Build header and content
        $this->header = substr($template, 0,
            strripos($template, "</head>") + 9);
        $this->content = substr($template, strlen($this->header),
            strlen($template) - strlen($this->header));
        if (stripos($this->content, '#calculator#')) {
            $calculatorTemplate = file_get_contents($_ENV['TEMPLATE_DIR']
                .'calculator.html');
            $this->content = str_replace('#calculator#', $calculatorTemplate,
                $this->content);
        }
        if (stripos($this->content, '#calculator_general#')) {
            $calculatorGeneralTemplate = file_get_contents($_ENV['TEMPLATE_DIR']
                .'calculator_general.html');
            $this->content = str_replace('#calculator_general#',
                $calculatorGeneralTemplate, $this->content);
        }
        if (stripos($this->content, '#calculator_acreditiv#')) {
            $calculatorAcreditivTemplate
                = file_get_contents($_ENV['TEMPLATE_DIR']
                .'calculator_acreditiv.html');
            $this->content = str_replace('#calculator_acreditiv#',
                $calculatorAcreditivTemplate, $this->content);
        }
        if (stripos($this->content, '#calculator_lcim#')) {
            $calculatorLcimTemplate = file_get_contents($_ENV['TEMPLATE_DIR']
                .'calculator_lcim.html');
            if (!in_array($this->page_url,
                ['', 'credite-firme-persoane-juridice'])
            ) {
                $calculatorLcimTemplate
                    = str_replace('<div class="tab-pane active" id="lcim">',
                    '<div class="col-lg-6 col-xl-5 col-xxl-4"><div id="calculator" class="calculator">',
                    $calculatorLcimTemplate);
                $calculatorLcimTemplate .= '</div>';
            }
            $this->content = str_replace('#calculator_lcim#',
                $calculatorLcimTemplate, $this->content);
        }
        if (stripos($this->content, '#calculator_gimob#')) {
            $calculatorGimobTemplate = file_get_contents($_ENV['TEMPLATE_DIR']
                .'calculator_gimob.html');
            if (!in_array($this->page_url,
                ['', 'credite-firme-persoane-juridice'])
            ) {
                $calculatorGimobTemplate
                    = str_replace('<div class="tab-pane" id="gimob">',
                    '<div class="col-lg-6 col-xl-5 col-xxl-4"><div id="calculator" class="calculator">',
                    $calculatorGimobTemplate);
                $calculatorGimobTemplate .= '</div>';
            }
            $this->content = str_replace('#calculator_gimob#',
                $calculatorGimobTemplate, $this->content);
        }
        if (stripos($this->content, '#calculator_pergr#')) {
            $calculatorPergrTemplate = file_get_contents($_ENV['TEMPLATE_DIR']
                .'calculator_pergr.html');
            if (!in_array($this->page_url,
                ['', 'credite-firme-persoane-juridice'])
            ) {
                $calculatorPergrTemplate
                    = str_replace('<div class="tab-pane" id="pergr">',
                    '<div class="col-lg-6 col-xl-5 col-xxl-4"><div id="calculator" class="calculator">',
                    $calculatorPergrTemplate);
                $calculatorPergrTemplate .= '</div>';
            }
            $this->content = str_replace('#calculator_pergr#',
                $calculatorPergrTemplate, $this->content);
        }
        if (stripos($this->content, '#calculator_scont#')) {
            $calculatorScontTemplate = file_get_contents($_ENV['TEMPLATE_DIR']
                .'calculator_scont.html');
            if (!in_array($this->page_url,
                ['', 'credite-firme-persoane-juridice'])
            ) {
                $calculatorScontTemplate
                    = str_replace('<div class="tab-pane" id="scont">',
                    '<div class="col-lg-6 col-xl-5 col-xxl-4"><div id="calculator" class="calculator">',
                    $calculatorScontTemplate);
                $calculatorScontTemplate .= '</div>';
            }
            $this->content = str_replace('#calculator_scont#',
                $calculatorScontTemplate, $this->content);
        }
        if (stripos($this->content, '#calculator_fctrg#')) {
            $calculatorFctrgTemplate = file_get_contents($_ENV['TEMPLATE_DIR']
                .'calculator_fctrg.html');
            if (!in_array($this->page_url,
                ['', 'credite-firme-persoane-juridice'])
            ) {
                $calculatorFctrgTemplate
                    = str_replace('<div class="tab-pane" id="fctrg">',
                    '<div class="col-lg-6 col-xl-5 col-xxl-4"><div id="calculator" class="calculator">',
                    $calculatorFctrgTemplate);
                $calculatorFctrgTemplate .= '</div>';
            }
            $this->content = str_replace('#calculator_fctrg#',
                $calculatorFctrgTemplate, $this->content);
        }
        if (stripos($this->content, '#calculator_sgb#')) {
            $calculatorSgbTemplate = file_get_contents($_ENV['TEMPLATE_DIR']
                .'calculator_sgb.html');
            if (!in_array($this->page_url,
                ['', 'credite-firme-persoane-juridice'])
            ) {
                $calculatorSgbTemplate
                    = str_replace('<div class="tab-pane" id="sgb">',
                    '<div class="col-lg-6 col-xl-5 col-xxl-4"><div id="calculator" class="calculator">',
                    $calculatorSgbTemplate);
                $calculatorSgbTemplate .= '</div>';
            }
            $this->content = str_replace('#calculator_sgb#',
                $calculatorSgbTemplate, $this->content);
        }
        if (stripos($this->content, '#calculator_creditepjfaragarantie#')) {
            $calculatorCreditFaraGarantieTemplate
                = file_get_contents($_ENV['TEMPLATE_DIR']
                .'calculator_creditepjfaragarantie.html');
            if (!in_array($this->page_url,
                ['', 'credite-firme-persoane-juridice'])
            ) {
                $calculatorCreditFaraGarantieTemplate
                    = str_replace('<div class="tab-pane" id="creditepjfaragarantie">',
                    '<div class="col-lg-6 col-xl-5 col-xxl-4"><div id="calculator" class="calculator">',
                    $calculatorCreditFaraGarantieTemplate);
                $calculatorCreditFaraGarantieTemplate .= '</div>';
            }
            $this->content = str_replace('#calculator_creditepjfaragarantie#',
                $calculatorCreditFaraGarantieTemplate, $this->content);
        }
        if (stripos($this->content, '#calculator_agricultura#')) {
            $calculatorAgriculturaTemplate
                = file_get_contents($_ENV['TEMPLATE_DIR']
                .'calculator_agricultura.html');
            if ($this->page_url != '') {
                $calculatorAgriculturaTemplate
                    = str_replace('<div class="tab-pane" id="crediteagricultura">',
                    '<div class="col-lg-6 col-xl-5 col-xxl-4"><div id="calculator" class="calculator">',
                    $calculatorAgriculturaTemplate);
                $calculatorAgriculturaTemplate .= '</div>';
            }
            $this->content = str_replace('#calculator_agricultura#',
                $calculatorAgriculturaTemplate, $this->content);
        }
        if (stripos($this->content, '#calculator_pfa#')) {
            $calculatorPFATemplate
                = file_get_contents($_ENV['TEMPLATE_DIR']
                .'calculator_pfa.html');
            if ($this->page_url != '') {
                $calculatorPFATemplate
                    = str_replace('<div class="tab-pane" id="creditpfa">',
                    '<div class="col-lg-6 col-xl-5 col-xxl-4"><div id="calculator" class="calculator">',
                    $calculatorPFATemplate);
                $calculatorPFATemplate .= '</div>';
            }
            $this->content = str_replace('#calculator_pfa#',
                $calculatorPFATemplate, $this->content);
        }

        if (stripos($this->content, '#calculator_dezimob#')) {
            $calculatorDezImobTemplate
                = file_get_contents($_ENV['TEMPLATE_DIR']
                .'calculator_dezimob.html');
            if ($this->page_url != '') {
                $calculatorDezImobTemplate
                    = str_replace('<div class="tab-pane" id="creditdezimob">',
                    '<div class="col-lg-6 col-xl-5 col-xxl-4"><div id="calculator" class="calculator">',
                    $calculatorDezImobTemplate);
                $calculatorDezImobTemplate .= '</div>';
            }
            $this->content = str_replace('#calculator_dezimob#',
                $calculatorDezImobTemplate, $this->content);
        }

        if (stripos($this->content, '#calculator_punte#')) {
            $calculatorPunteTemplate
                = file_get_contents($_ENV['TEMPLATE_DIR']
                .'calculator_punte.html');
            if ($this->page_url != '') {
                $calculatorPunteTemplate
                    = str_replace('<div class="tab-pane" id="creditpunte">',
                    '<div class="col-lg-6 col-xl-5 col-xxl-4"><div id="calculator" class="calculator">',
                    $calculatorPunteTemplate);
                $calculatorPunteTemplate .= '</div>';
            }
            $this->content = str_replace('#calculator_punte#',
                $calculatorPunteTemplate, $this->content);
        }

        if (stripos($this->content, '#calculator_leaseback#')) {
            $calculatorLeasebackTemplate
                = file_get_contents($_ENV['TEMPLATE_DIR']
                .'calculator_leaseback.html');
            if ($this->page_url != '') {
                $calculatorLeasebackTemplate
                    = str_replace('<div class="tab-pane" id="creditleaseback">',
                    '<div class="col-lg-6 col-xl-5 col-xxl-4"><div id="calculator" class="calculator">',
                    $calculatorLeasebackTemplate);
                $calculatorLeasebackTemplate .= '</div>';
            }
            $this->content = str_replace('#calculator_leaseback#',
                $calculatorLeasebackTemplate, $this->content);
        }

        if (stripos($this->content, '#calculator_service#')) {
            $calculatorServiceTemplate
                = file_get_contents($_ENV['TEMPLATE_DIR']
                .'calculator_service.html');
            if ($this->page_url != '') {
                $calculatorServiceTemplate
                    = str_replace('<div class="tab-pane" id="creditservice">',
                    '<div class="col-lg-6 col-xl-5 col-xxl-4"><div id="calculator" class="calculator">',
                    $calculatorServiceTemplate);
                $calculatorServiceTemplate .= '</div>';
            }
            $this->content = str_replace('#calculator_service#',
                $calculatorServiceTemplate, $this->content);
        }

        if (stripos($this->content, '#calculator_combustibil#')) {
            $calculatorCombustibilTemplate
                = file_get_contents($_ENV['TEMPLATE_DIR']
                .'calculator_combustibil.html');
            if ($this->page_url != '') {
                $calculatorCombustibilTemplate
                    = str_replace('<div class="tab-pane" id="creditcombustibil">',
                    '<div class="col-lg-6 col-xl-5 col-xxl-4"><div id="calculator" class="calculator">',
                    $calculatorCombustibilTemplate);
                $calculatorCombustibilTemplate .= '</div>';
            }
            $this->content = str_replace('#calculator_combustibil#',
                $calculatorCombustibilTemplate, $this->content);
        }

        if (isset($footer)) {
            $this->content = str_replace("</body>", $footer."</body>",
                $this->content);
        }
        if (strpos($this->content,
                '<option value="">'.__('Country').'</option></select>')
            !== false
        ) {
            $countriesOptions = '    <option value="">'.__('Country')
                .'</option>'.PHP_EOL;
            $countries = new Countries();
            $countries = $countries->get();
            foreach ($countries as $country) {
                $countriesOptions .= '    <option value="'.$country->id.'">'
                    .$country->name.'</option>'.PHP_EOL;
            }
            $this->content = str_replace('<option value="">'.__('Country')
                .'</option>', $countriesOptions.'</select>', $this->content);
        }
        if (stripos($this->content, '#testimonials#')) {
            $testimonialsEnt = new Testimonials();
            $testimonialsEnt->status = 1;
            $testimonialsEnt->where(['id' => [1, '>']]);
            $testimonialsEnt->order('RAND()');
            $testimonialsEnt = $testimonialsEnt->get();
            $testimonials = '<div id="testimonialsControls" class="carousel slide" data-bs-ride="carousel">
                <ol class="carousel-indicators">';
            for ($i = 0; $i < ceil(count($testimonialsEnt) / 2); $i++) {
                $testimonials .= '<li data-bs-target="#testimonialsControls" data-bs-slide-to="'
                    .$i.'"'.($i == 0 ? ' class="active"' : '').'></li>';
            }
            $testimonials .= '</ol>
                <div class="carousel-inner">';
            foreach ($testimonialsEnt as $index => $testimonial) {
                if ($index % 2 == 0) {
                    if ($index > 0) {
                        $testimonials .= '</div></div>';
                    }
                    $testimonials
                        .= /** @lang text */
                        '<div class="carousel-item'.($index == 0 ? ' active'
                            : '').'"><div class="row">';
                }
                if (strlen($testimonial->short) > 200) {
                    $pos = strpos($testimonial->short, ' ', 200);
                    $testimonial_content = substr($testimonial->short, 0, $pos)
                        .'...';
                } else {
                    $testimonial_content = $testimonial->short;
                }
                $path_parts = pathinfo($testimonial->image);
                $fname = $path_parts['filename'];
                $extension = $path_parts['extension'];
                $thumbnail = $fname.'-160x160.'.$extension;
                $testimonials
                    .= /** @lang text */
                    '<div class="col-lg-6 col-12 testimonial my-lg-0 my-3">
                            <div class="article mb-3 mb-md-0 px-1">
                                <div class="small">
                                    <div class="row">
                                        <a href="/testimoniale/'
                    .Util::getUrlFromString($testimonial->company).'" class="col-7 video-link">
                                            <img data-src="https://i.ytimg.com/vi/'
                    .$testimonial->video
                    .'/maxresdefault.jpg" width="297" height="167" class="lazyload" alt="'
                    .$testimonial->company.'" title="'.$testimonial->company.'" />
                                        </a>
                                        <div class="col-5 text-center mt-2 mt-md-4">
                                            <span class="nume">'
                    .$testimonial->name.'</span>
                                            <span class="company_function">'
                    .$testimonial->function.'</span>
                                            <span class="company_name">'
                    .$testimonial->company.'</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>'.PHP_EOL;
            }
            $testimonials .= '</div></div>';
            $testimonials
                .= /** @lang text */
                '    </div>
                </div>';
            $this->content = str_replace('#testimonials#', $testimonials,
                $this->content);
        }
        define("_BBC_PAGE_NAME", $this->title);
        define("_BBCLONE_DIR", $_ENV['APP_DIR']."bbclone/");
        define("COUNTER", _BBCLONE_DIR."mark_page.php");
        if (is_readable(COUNTER)) {
            //include_once(COUNTER);
        }
        return true;
    }

    /**
     * Output html
     */
    public function output()
    {
        $language = Util::getUserLanguage();
        $md5url = md5(Util::getCurrentUrl());
        $cacheKey = $_ENV['CACHE_PREFIX'].'output|'.$language.'|'
            .$md5url;
        if ($this->cache && $this->cache->exists($cacheKey)) {
            $buffer = $this->cache->get($cacheKey);
            echo self::csrfguard_replace_forms($buffer);

            return true;
        }
        $this->load_template();
        $buffer = $this->header;
        $buffer .= $this->content;
        $buffer = self::sanitize_output($buffer);
        if ($this->isAmp) {
            $buffer = str_ireplace(
                ['<img', '<video', '/video>', '<audio', '/audio>'],
                [
                    '<amp-img',
                    '<amp-video',
                    '/amp-video>',
                    '<amp-audio',
                    '/amp-audio>'
                ],
                $buffer
            );
            $buffer = preg_replace_callback('/<amp-img(.*?)( ?\/)?>/',
                array($this, 'ampsize'), $buffer);
        }
        if ($this->cache && !$this->cache->exists($cacheKey)) {
            $this->cache->set($cacheKey, $buffer);
        }
        $buffer = self::csrfguard_replace_forms($buffer);
        echo $buffer;
        return true;
    }

    public function ampsize($matches)
    {
        preg_match('/src=\"([^\"]*)/', $matches[1], $src);
        $size = Util::getImageSize($src[1]);
        return $size ? '<amp-img'.$matches[1].' width="'.$size[0].'" height="'
            .$size[1]
            .'"></amp-img>' : '';
    }

    /**
     * @param $buffer
     *
     * @return array|string|string[]|null
     */
    public static function sanitize_output($buffer)
    {
        $search = array(
            '/>[^\S ]+/',  // strip whitespaces after tags, except space
            '/[^\S ]+</',  // strip whitespaces before tags, except space
            '/(\s)+/'       // shorten multiple whitespace sequences
        );
        $replace = array('>', '<', '\\1');
        return preg_replace($search, $replace, $buffer);
    }
}
