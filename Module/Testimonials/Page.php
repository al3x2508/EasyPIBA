<?php
namespace Module\Testimonials;
use Model\Model;
use Utils\Util;

class Page {
	public function output() {
		$currentUrl = Util::getCurrentUrl();
		$page = new \stdClass();
		$page->title = __('Testimonials');
		$page->description = __('Testimonials');
		$page->h1 = __('Testimonials');
		$page->content = '';
		$page->js = 'Module/Testimonials/testimonials.js';
		$page->css = 'testimonials.css,Module/Testimonials/testimonials.css';
		$idPrincipal = false;
		$testimonials = new Model('testimonials');
		$testimonials->status = 1;
		preg_match('/^testimoniale\/(.*)/', $currentUrl, $matches);
		if(count($matches) > 1) {
			$companyName = str_replace('-', '_', $matches[1]);
			$testimonials->company = array($companyName, ' LIKE ');
			$result = $testimonials->get();
			if(count($result)) {
				$idPrincipal = $result[0]->id;
				$page->h1 = __('Testimonial') . ' ' . $result[0]->name;
				$page->title = __('Testimonial') . ' ' . $result[0]->name;
			}
			$testimonials->clear();
		}
		$testimonials->status = 1;
		$testimonials->order('id ASC');
		$testimonials = $testimonials->get();
		if($idPrincipal) {
			foreach($testimonials AS $index => $testimonial) {
				if($testimonial->id == $idPrincipal) {
					$moveToFirst = $testimonial;
					unset($testimonials[$index]);
					array_unshift($testimonials, $moveToFirst);
				}
			}
		}
		$count = 0;
		foreach($testimonials AS $testimonial) {
			/*if(strlen($testimonial->short) > 400) {
				$pos = strpos($testimonial->short, ' ', 400);
				$testimonialText = substr($testimonial->short, 0, $pos) . '...';
			}
			else $testimonialText = $testimonial->short;*/
			$path_parts = pathinfo($testimonial->image);
			$fname = $path_parts['filename'];
			$extension = $path_parts['extension'];
			$thumbnail = $fname . '-160x160.' . $extension;
			if($count == 0) {
				$page->content .= '<article class="my-3 my-md-0">
							<div class="principal">
								<div class="row">
									<div data-video="' . $testimonial->video . '" id="ytv" class="video col-md-7">
										<img src="https://i.ytimg.com/vi/' . $testimonial->video . '/maxresdefault.jpg" class="img-fluid" alt="' . $testimonial->company . '" title="' . $testimonial->company . '" />
									</div>
									<div class="col-md-5">
										<div>
											<div class="align-middle mt-md-0 mt-3">
												<div class="d-inline-block align-middle">
													<img src="' . $_ENV['FOLDER_URL'] . 'img/testimonials/' . $thumbnail . '" class="company_logo d-inline-block" alt="' . $testimonial->company . '" title="' . $testimonial->company . '" />
												</div>
												<div class="d-inline-block align-middle ml-2">
													<span class="nume">' . $testimonial->name .'</span>
													<span class="company_function">' . $testimonial->function .'</span>
													<span class="company_name">' . $testimonial->company .'</span>
												</div>
											</div>
										</div>
										<div class="mt-3">
											<div class="text-ellipsis">' . $testimonial->content . '</div>
										</div>
									</div>
								</div>
							</div>
						</article>';
				$count++;
			}
			else {
				if($testimonial->id != 1) {
					if ($count % 3 == 1) $page->content .= '				<div class="row my-0 my-md-3">';
					$page->content .= '					<div class="article testimonial col-lg-4 mb-3 mb-md-0">
						<div class="small">
							<div class="row">
								<a href="/testimoniale/' . Util::getUrlFromString($testimonial->company) . '" class="col-7 video-link">
									<img src="https://i.ytimg.com/vi/' . $testimonial->video . '/maxresdefault.jpg" class="img-fluid" alt="' . $testimonial->company . '" title="' . $testimonial->company . '" />
								</a>
								<div class="col-5">
									<span class="nume">' . $testimonial->name . '</span>
									<span class="company_function">' . $testimonial->function . '</span>
									<span class="company_name">' . $testimonial->company . '</span>
								</div>
							</div>
						</div>
					</div>' . PHP_EOL;
					if ($count % 3 == 0) $page->content .= '				</div>' . PHP_EOL;
					$count++;
				}
			}
		}
		if ($count % 3 !== 0) $page->content .= '				</div>' . PHP_EOL;
		$page->content = '<section id="testimoniale" class="mt-4 blueh">
		<div class="container container-max-xl2">
			<div class="titlu"><h1><span>' . $page->h1 . '</span></h1></div>
			<div class="row">
				<div class="col-lg-12">
					' . $page->content .'
				</div>
			</div>
		</div>
	</section>';
		return $page;
	}
}