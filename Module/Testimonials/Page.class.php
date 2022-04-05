<?php
namespace Module\Testimonials;
use Model\Model;
use Utils\Template;
use Utils\Util;

class Page {
	public function output() {
		$currentUrl = Util::getCurrentUrl();
		$page = new \stdClass();
		$page->title = __('Testimonials');
		$page->description = __('Testimonials');
		$h1 = __('Testimonials');
		$page->content = '';
		$page->js = array();
		$page->css = array();
		$testimonials = new Model('testimonials');
		if(strpos($currentUrl, 'testimonials') === 0) {
			$testimonials->status = 1;
			$testimonials->order('id DESC');
			$testimonials = $testimonials->get();
			foreach($testimonials AS $index => $testimonial) {
				$testimonialText = Template::shortText($testimonial->short, 400);
				if($index % 2 == 0) $page->content .= '				<div class="row">';
				$page->content .= '					<article class="col-12 col-md-6">
						<header>
							<h2><a href="/testimonial' . $testimonial->id . '.html" title="' . $testimonial->name . '">' . $testimonial->name . '</a></h2>
						</header>
						<div class="content">
							<p>' . $testimonialText . '</p>
							<a href="/testimonial' . $testimonial->id . '.html" class="readmore">' . __('Read more') . '</a>
						</div>
					</article>' . PHP_EOL;
				if($index % 2 == 1) $page->content .= '				</div>' . PHP_EOL;
			}
		}
		else {
			preg_match('/^testimonial(\d+)\.html/', $currentUrl, $matches);
			$id = $matches[1];
			$testimonial = $testimonials->getOneResult('id', $id);
			if($testimonial) {
				$title = __('Testimonial') . ' ' . $testimonial->name;
				$page->description = $title;
				$h1 = $title;
				$page->content = '<p class="text-justify indent10">' . $testimonial->content . '</p>' . PHP_EOL;
			}
		}
		$page->content = '<h1>' . $h1 . '</h1>
		<div class="row">
			<div class="col-12">
				' . $page->content;
		$page->content .= '			</div>
		</div>';
		return $page;
	}
}