<?php
$page_title = __('Testimonials');
$description = __('Testimonials');
$h1 = __('Testimonials');
$testimonials = new Model\Model('testimonials');
$content = '';
if(strpos($page_url, 'testimonials') === 0) {
	$testimonials->status = 1;
	$testimonials->order('id DESC');
	$testimonials = $testimonials->get();
	foreach($testimonials AS $index => $testimonial) {
		if(strlen($testimonial->short) > 400) {
			$pos = strpos($testimonial->short, ' ', 400);
			$testimonialText = substr($testimonial->short, 0, $pos) . '...';
		}
		else $testimonialText = $testimonial->short;
		if($index % 2 == 0) $content .= '				<div class="row">';
		$content .= '					<article class="col-lg-6">
						<header>
							<h2><a href="/testimonial' . $testimonial->id . '.html" title="' . $testimonial->name . '">' . $testimonial->name . '</a></h2>
						</header>
						<div class="content">
							<p>' . $testimonialText . '</p>
							<a href="/testimonial' . $testimonial->id . '.html" class="readmore">' . __('Read more') . '</a>
						</div>
					</article>' . PHP_EOL;
		if($index % 2 == 1) $content .= '				</div>' . PHP_EOL;
	}
}
else {
	preg_match('/^testimonial(\d+)\.html/', $page_url, $matches);
	$id = $matches[1];
	$testimonial = $testimonials->getOneResult('id', $id);
	if($testimonial) {
		$page_title = __('Testimonial') . ' ' . $testimonial->name;
		$description = $page_title;
		$h1 = $page_title;
		$h1 = $testimonial->name;
		$content = '<p class="text-justify indent10">' . $testimonial->content . '</p>' . PHP_EOL;
	}
}
$content = '<h1>' . $h1 . '</h1>
		<div class="row">
			<div class="col-lg-12">
				' . $content;
$content .= '			</div>
		</div>';
