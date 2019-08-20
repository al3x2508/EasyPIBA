<?php
namespace Module\Sitemap;

use Model\Model;
use Utils\Util;

class Page
{
    public static function output()
    {
        header("Content-type: text/xml");
        $return = '';
        $siteUrl = _ADDRESS_ . _FOLDER_URL_;
        $return .= '<?xml version="1.0" encoding="UTF-8"?>
	<urlset
		xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
		xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
		xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
			http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . PHP_EOL;
        $pages = new Model('pages');
        $pages->visible = 1;
        $pages->order('language ASC, menu_parent ASC, menu_order ASC');
        $pages = $pages->get();
        foreach ($pages AS $page) {
            $langUrl = ($page->language == _DEFAULT_LANGUAGE_)?'':$page->language . '/';
            $return .= '		<url>
			<loc>' . $siteUrl . $langUrl . $page->url . '</loc>
			<changefreq>monthly</changefreq>
			<priority>0.90</priority>
		</url>' . PHP_EOL;
        }
        $module_routes = new Model('module_routes');
        $module_routes->type = 0;
        $module_routes->mustBeLoggedIn = 0;
        $module_routes = $module_routes->get();
        foreach ($module_routes AS $route) {
            $return .= '		<url>
			<loc>' . $siteUrl . $route->url . '</loc>
			<changefreq>monthly</changefreq>
			<priority>0.90</priority>
		</url>' . PHP_EOL;
        }
        $news = new Model('news');
        $news->status = 1;
        $news->order('date_published DESC');
        $news = $news->get();
        foreach ($news AS $n) {
            $url = 'news/' . Util::getUrlFromString($n->title);
            $return .= '		<url>
			<loc>' . $siteUrl . $url . '</loc>
			<changefreq>monthly</changefreq>
			<priority>0.70</priority>
		</url>' . PHP_EOL;
        }
        $testimonials = new Model('testimonials');
        $testimonials->status = 1;
        $testimonials->order('id DESC');
        $testimonials = $testimonials->get();
        foreach ($testimonials AS $testimonial) {
            $return .= '		<url>
			<loc>' . $siteUrl . 'testimonial' . $testimonial->id . '</loc>
			<changefreq>monthly</changefreq>
			<priority>0.70</priority>
		</url>' . PHP_EOL;
        }
        $return .= '</urlset>';
        return $return;
    }
}