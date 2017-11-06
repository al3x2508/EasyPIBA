<?php
namespace Module\News;
use Model\Model;
use Utils\Util;

class Page {
	public $url = '';
	public function __construct($url) {
		$this->url = $url;
	}
	public function isOwnUrl() {
		if($this->url == 'news' || preg_match('/news\/pag\-[\d+]/', $this->url)) return true;
		else {
			$title = str_replace(array('news/', '.html'), '', $this->url);
			$news = new Model('news');
			$news->title = array($title, 'LIKE');
			$news = $news->get();
			return count($news)?true:false;
		}
	}
	public function getMenu() {
		$menu = array('url' => 'news/', 'menu_text' => __('News'), 'submenu_text' => '', 'menu_parent' => 0);
		if(preg_match('/news\/.*/', $this->url)) $menu['class'] = 'active';
		return array($menu);
	}
	public function output() {
		$page = new \stdClass();
		$page->title = _APP_NAME_ . ' ' . __('news');
		$page->description = _APP_NAME_ . ' ' . __('news');
		$page->h1 = _APP_NAME_ . ' ' . __('news');
		$page->content = '';
		$page->js = array();
		$page->css = array();
		$news = new Model('news');
		$content = '';
		if($this->url == 'news' || preg_match('/news\/pag\-[\d+]/', $this->url)) {
			$pageno = preg_match('/news\/pag\-(\d+)/', $this->url, $matches)?$matches[1]:1;
			$limit = (($pageno - 1) * 6) . ', 6';
			$content = /** @lang text */
				'<div class="container-fluid" id="news">
			<div class="row">
				<div class="col-lg-12 text-center">
					<h1 class="title">' . __('News') . '</h1>
				</div>
			</div>
		</div>
		<div class="container" id="news">' . PHP_EOL;
			$news->status = 1;
			$news->order('date_published DESC');
			$totalNews = $news->countItems();
			$news->limit($limit);
			$news = $news->get();
			foreach($news AS $index => $story) {
				if(empty(trim($story->image))) {
					$img = _LOGO_;
					$spanimgc = ' noimg';
				}
				else {
					$img = '/img/news/' . str_replace('.jpg', '-360x220.jpg', rawurlencode($story->image));
					$spanimgc = '';
				}
				$href = '/news/' . Util::getUrlFromString($story->title) . '.html';
				if(strlen($story->content) > 200) {
					$pos = strpos($story->content, ' ', 200);
					$storyText = substr(strip_tags($story->content), 0, $pos) . '...';
				}
				else $storyText = $story->content;
				if($index % 3 == 0) $content .= '				<div class="row">';
				$content .= '<article class="col-lg-4">
						<header>
							<h2>
								<a href="' . $href. '" title="' . htmlentities($story->title) . '">
									<span class="imgstory' . $spanimgc . '">
										<img src="' . $img . '" alt="' . htmlentities($story->title) . '" />
									</span>
									' . $story->title . '
								</a>
							</h2>
							<p><time datetime="' . $story->date_published . '">' . date('d.m.Y', strtotime($story->date_published)) . '</time></p><br />
						</header>
						<div class="content">
							' . $storyText . '
						</div>
						<a href="' . $href. '" class="readmore">&gt;&gt; ' . __('read more') . '</a>
					</article>' . PHP_EOL;
				if($index % 3 == 2) $content .= '				</div>' . PHP_EOL;
			}
			if(count($news) % 3 != 0) $content .= '</div>';
			if($totalNews > 6) {
				$butBack = ($pageno > 1)?'<li class="previous"><a href="/news/pag-' . ($pageno - 1) . '">' . __('Newer') . ' <span aria-hidden="true">&larr;</span></a></li>' . PHP_EOL:'';
				$butForward = ($pageno < ceil($totalNews / 6))?'<li class="next"><a href="/news/pag-' . ($pageno + 1) . '">' . __('Older') . ' <span aria-hidden="true">&rarr;</span></a></li>' . PHP_EOL:'';
				$content .= '<nav>
			<ul class="pager">
				' . $butBack . $butForward . '
			</ul>
		</nav>' . PHP_EOL;
			}
			$content .= '</div>' . PHP_EOL;
		}
		else {
			$title = str_replace(array('news/', '.html'), '', $this->url);
			$news->title = array($title, 'LIKE');
			$news = $news->get();
			if(count($news)) {
				$story = $news[0];
				$page_title = $story->title;
				$page->title = $page_title;
				$page->description = $page_title;
				$page->h1 = $page_title;
				if(empty(trim($story->image))) $image = '';
				else {
					$image = "<img src='/img/news/" . rawurlencode($story->image) . "' id='imgstory' />";
					$page->ogimage = _ADDRESS_ . '/img/news/' . str_replace('.jpg', '-720x220.jpg', rawurlencode($story->image));
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
			}
			else header('Location: ' . _FOLDER_URL_);
		}
		$page->content = $content;
		return $page;
	}
}