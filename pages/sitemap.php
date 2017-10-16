<?php
header("Content-type: text/xml");
$siteUrl = _ADDRESS_ . _FOLDER_URL_;
echo '<?xml version="1.0" encoding="UTF-8"?>
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
$pages = new \Model\Model('pages');
$pages->visible = 1;
$pages->order('menu_parent ASC, menu_order ASC');
$pages = $pages->get();
foreach($pages AS $page) {
	if(!empty($page->url)) echo '		<url>
			<loc>' . $siteUrl . $page->url . '.html</loc>
			<changefreq>monthly</changefreq>
			<priority>0.90</priority>
		</url>' . PHP_EOL;
}
$news = new Model\Model('news');
$news->order('date_published DESC');
$news = $news->get();
foreach($news AS $n) {
	$url = 'news/' . \Utils\Util::getUrlFromString($n->title) . '.html';
	echo '		<url>
			<loc>' . $siteUrl . $url . '</loc>
			<changefreq>monthly</changefreq>
			<priority>0.70</priority>
		</url>' . PHP_EOL;
}
$testimonials = new Model\Model('testimonials');
$testimonials->order('id DESC');
$testimonials = $testimonials->get();
foreach($testimonials AS $testimonial) {
	echo '		<url>
			<loc>' . $siteUrl . 'testimonial' . $testimonial['id'] . '.html</loc>
			<changefreq>monthly</changefreq>
			<priority>0.70</priority>
		</url>' . PHP_EOL;
}
echo '</urlset>';
exit;