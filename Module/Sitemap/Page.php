<?php

namespace Module\Sitemap;

use Model\Model;
use Utils\Util;

class Page
{
    public static function output(): string
    {
        header("Content-type: text/xml");
        $return = '';
        $siteUrl = $_ENV['ADDRESS'].$_ENV['FOLDER_URL'];
        $priorityMap = [
            ''                                      => 1,
            'credite-firme-persoane-juridice'       => 1,
            'credit-cu-garantie-imobiliara-ipoteca' => 1,
            'amanet-imobiliare-terenuri'            => 3,
            'linie-de-credit-persoane-juridice'     => 1,
            'credit-pfa'                            => 0,
            'credit-punte-cu-perioada-de-gratie'    => 3,
            'sale-and-lease-back'                   => 2,
            'credite-agricultura'                   => 1,
            'scontari'                              => 3,
            'imm-factoring'                         => 3,
        ];
        $return .= '<?xml version="1.0" encoding="UTF-8"?>
    <urlset
        xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">'.PHP_EOL;
        $pages = new Model('pages');
        $pages->visible = 1;
        $pages->menu_parent = 0;
        $pages->menu_order = array('0', '!=');
        $pages->order('language ASC, menu_parent ASC, menu_order ASC');
        $pages = $pages->get();
        foreach ($pages as $page) {
            $langUrl = ($page->language == $_ENV['DEFAULT_LANGUAGE']) ? ''
                : $page->language.'/';
            $priority = array_key_exists($page->url, $priorityMap)
                ? $priorityMap[$page->url] : (empty($page->url) ? 1 : 0.90);
            $return .= <<<XML
        <url>
            <loc>$siteUrl$langUrl$page->url</loc>
            <changefreq>monthly</changefreq>
            <priority>$priority</priority>
        </url>
XML;
        }
        $pages = new Model('pages');
        $pages->visible = 1;
        $pages->menu_parent = 0;
        $pages->menu_order = 0;
        $pages->order('language ASC, menu_parent ASC, menu_order ASC');
        $pages = $pages->get();
        foreach ($pages as $page) {
            $langUrl = ($page->language == $_ENV['DEFAULT_LANGUAGE']) ? ''
                : $page->language.'/';
            $priority = array_key_exists($page->url, $priorityMap)
                ? $priorityMap[$page->url] : '0.90';
            $return .= <<<XML
        <url>
            <loc>$siteUrl$langUrl$page->url</loc>
            <changefreq>monthly</changefreq>
            <priority>$priority</priority>
        </url>
XML;
        }
        $pages = new Model('pages');
        $pages->visible = 1;
        $pages->menu_parent = array('0', '!=');
        $pages->order('language ASC, menu_parent ASC, menu_order ASC');
        $pages = $pages->get();
        foreach ($pages as $page) {
            $langUrl = ($page->language == $_ENV['DEFAULT_LANGUAGE']) ? ''
                : $page->language.'/';
            $priority = array_key_exists($page->url, $priorityMap)
                ? $priorityMap[$page->url] : '0.90';
            $return .= <<<XML
        <url>
            <loc>$siteUrl$langUrl$page->url</loc>
            <changefreq>monthly</changefreq>
            <priority>$priority</priority>
        </url>
XML;
        }
        $moduleRoutes = new Model('module_routes');
        $moduleRoutes->type = 0;
        $moduleRoutes->mustBeLoggedIn = 0;
        $moduleRoutes = $moduleRoutes->get();
        foreach ($moduleRoutes as $route) {
            $return .= <<<XML
        <url>
            <loc>$siteUrl$route->url</loc>
            <changefreq>monthly</changefreq>
            <priority>0.90</priority>
        </url>
XML;
        }
        $news = new Model('news');
        $news->status = 1;
        $news->order('date_published DESC');
        $news = $news->get();
        foreach ($news as $n) {
            $url = 'news/'.Util::getUrlFromString($n->title);
            $return .= <<<XML
        <url>
            <loc>$siteUrl$url</loc>
            <changefreq>monthly</changefreq>
            <priority>0.70</priority>
        </url>
XML;
        }
        $testimonials = new Model('testimonials');
        $testimonials->status = 1;
        $testimonials->order('id DESC');
        $testimonials = $testimonials->get();
        foreach ($testimonials as $testimonial) {
            $url = Util::getUrlFromString($testimonial->company);
            $return .= <<<XML
        <url>
            <loc>{$siteUrl}testimoniale/$url</loc>
            <changefreq>monthly</changefreq>
            <priority>0.70</priority>
        </url>
XML;
        }
        $return .= '</urlset>';
        return $return;
    }
}
