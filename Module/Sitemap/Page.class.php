<?php
namespace Module\Sitemap;
use Model\Model;
use Utils\Util;

class Page {
	public static function output() {
		header("Content-type: text/xml");
		$return = '';
		$siteUrl = _ADDRESS_ . _FOLDER_URL_;
		$return .= '<?xml version="1.0" encoding="UTF-8"?>
	<urlset
		xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
		xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
		xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
			http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
		<url>
			<loc>' . $siteUrl . '</loc>
			<changefreq>weekly</changefreq>
			<priority>1.00</priority>
		</url>
		<url>
			<loc>' . $siteUrl . 'login.html</loc>
			<changefreq>weekly</changefreq>
			<priority>1.00</priority>
		</url>
		<url>
			<loc>' . $siteUrl . 'password_reset.html</loc>
			<changefreq>weekly</changefreq>
			<priority>1.00</priority>
		</url>
		<url>
			<loc>' . $siteUrl . 'testimonials.html</loc>
			<changefreq>weekly</changefreq>
			<priority>1.00</priority>
		</url>
		<url>
			<loc>' . $siteUrl . 'news/</loc>
			<changefreq>daily</changefreq>
			<priority>1.00</priority>
		</url>' . PHP_EOL;
		$pages = new Model('pages');
		$pages->visible = 1;
		$pages->order('menu_parent ASC, menu_order ASC');
		$pages = $pages->get();
		foreach($pages AS $page) {
			if(!empty($page->url)) $return .= '		<url>
			<loc>' . $siteUrl . $page->url . '.html</loc>
			<changefreq>monthly</changefreq>
			<priority>0.90</priority>
		</url>' . PHP_EOL;
		}
		$news = new Model('news');
		$news->order('date_published DESC');
		$news = $news->get();
		foreach($news AS $n) {
			$url = 'news/' . Util::getUrlFromString($n->title) . '.html';
			$return .= '		<url>
			<loc>' . $siteUrl . $url . '</loc>
			<changefreq>monthly</changefreq>
			<priority>0.70</priority>
		</url>' . PHP_EOL;
		}
		$testimonials = new Model('testimonials');
		$testimonials->order('id DESC');
		$testimonials = $testimonials->get();
		foreach($testimonials AS $testimonial) {
			$return .= '		<url>
			<loc>' . $siteUrl . 'testimonial' . $testimonial['id'] . '.html</loc>
			<changefreq>monthly</changefreq>
			<priority>0.70</priority>
		</url>' . PHP_EOL;
		}
		$return .= '</urlset>';
		return $return;
	}
}