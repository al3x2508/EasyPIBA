<?php
namespace Module\News;

use Model\Model;
use Utils\Template;
use Utils\Util;

class Page
{
    public $useCache = true;

    public function output()
    {
        $currentUrl = Util::getCurrentUrl();
        $page = new \stdClass();
        $page->title = __('News') . ' ' . _APP_NAME_;
        $page->description = __('News') . ' ' . _APP_NAME_;
        $page->h1 = __('News') . ' ' . _APP_NAME_;
        $page->content = '';
        $page->js = array();
        $page->css = array('Module/News/news.css');
        $page->useCache = true;
        $news = new Model('news');
        $content = '';
        if ($currentUrl == 'noutati' || preg_match('/noutati\/pag\-[\d+]/', $currentUrl)) {
            //Disable cache for generic news page
            $page->useCache = false;
            $pageno = preg_match('/noutati\/pag\-(\d+)/', $currentUrl, $matches)?$matches[1]:1;
            $limit = (($pageno - 1) * 6) . ', 6';
            $content = /** @lang text */
                '<div class="container-fluid" id="header-news">
			<div class="row">
				<div class="col-12">
					<div class="container">
						<div class="row">
							<div class="col-12">
								<h1><strong>' . $page->title . '</strong></h1>
								<span class="spacer spacer-green"></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="container-fluid" id="container-news">
		    <div class="container py-5" id="news">' . PHP_EOL;
            $news->status = 1;
            $news->order('date_published DESC');
            $totalNews = $news->countItems();
            $news->limit($limit);
            $news = $news->get();
            foreach ($news AS $index => $story) {
                if (empty(trim($story->image))) {
                    $img = _FOLDER_URL_ . 'img/' . _LOGO_;
                    $spanimgc = ' noimg';
                } else {
                    $img = _FOLDER_URL_ . 'img/news/' . str_replace('.jpg', '-360x220.jpg',
                            rawurlencode($story->image));
                    $spanimgc = '';
                }
                $href = _FOLDER_URL_ . 'noutati/' . Util::getUrlFromString($story->title) . '.html';
                $storyText = Template::shortText($story->content);
                if ($index % 3 == 0) {
                    $content .= '				<div class="row mb-3">';
                }
                $content .= '<article class="col-lg-4">
                        <div class="drop-shadow p-0">
                            <header>
                                <h2>
                                    <a href="' . $href . '" title="' . htmlentities($story->title) . '">
                                        <span class="imgstire noimg' . $spanimgc . '">
                                            <img src="' . $img . '" alt="' . htmlentities($story->title) . '" />
                                        </span>
                                        <span class="titlu">' . $story->title . '</span>
                                    </a>
                                </h2>
                                <p><time datetime="' . $story->date_published . '">' . date('d.m.Y',
                            strtotime($story->date_published)) . '</time></p>
                            </header>
                            <div class="content">
                                ' . $storyText . '
                            </div>
                        </div>
					</article>' . PHP_EOL;
                if ($index % 3 == 2) {
                    $content .= '				</div>' . PHP_EOL;
                }
            }
            if (count($news) % 3 != 0) {
                $content .= '</div>';
            }
            if ($totalNews > 6) {
                $butBack = ($pageno > 1)?'<li class="previous"><a href="' . _FOLDER_URL_ . 'noutati/pag-' . ($pageno - 1) . '">' . __('Newer') . ' <span aria-hidden="true">&larr;</span></a></li>' . PHP_EOL:'';
                $butForward = ($pageno < ceil($totalNews / 6))?'<li class="next"><a href="' . _FOLDER_URL_ . 'noutati/pag-' . ($pageno + 1) . '">' . __('Older') . ' <span aria-hidden="true">&rarr;</span></a></li>' . PHP_EOL:'';
                $content .= '<nav>
			<ul class="pager">
				' . $butBack . $butForward . '
			</ul>
		</nav>' . PHP_EOL;
            }
            $content .= '</div>
                </div>' . PHP_EOL;
        } else {
            $title = str_replace(array('noutati/', '.html'), '', $currentUrl);
            $news->title = array($title, 'LIKE');
            $news = $news->get();
            if (count($news)) {
                $story = $news[0];
                $page_title = $story->title;
                $page->title = $page_title;
                $page->description = $page_title;
                $page->h1 = $page_title;
                if (empty(trim($story->image))) {
                    $image = '';
                } else {
                    $image = "<img src='" . _FOLDER_URL_ . "img/news/" . rawurlencode($story->image) . "' id='imgstory' />";
                    $page->ogimage = 'news/' . str_replace('.jpg', '-720x220.jpg', rawurlencode($story->image));
                }
                $content = $image . '
		<article id="content" class="container marginbot40">
			<header>
				<h1 class="text-center">' . $story->title . '</h1>
				<time datetime="' . $story->date_published . '">' . date('d.m.Y', strtotime($story->date_published)) . '</time>
			</header><br />
			<div class="content">
				' . $story->content . '
			</div>
		</article>' . PHP_EOL;
            } else {
                header('Location: ' . _FOLDER_URL_);
            }
        }
        $page->content = $content;
        return $page;
    }
}