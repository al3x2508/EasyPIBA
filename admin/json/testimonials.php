<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/Utils/functions.php');
$adminController = new Controller\AdminController();
if($adminController->checkPermission("Edit testimonials")) {
	if(!array_key_exists('id', $_REQUEST)) {
		$testimonials = new \Model\Model('testimonials');
		$pageNo = (array_key_exists('start', $_REQUEST))?($_REQUEST['start'] / $_REQUEST['length']) + 1:1;
		$itemsPerPage = (array_key_exists('start', $_REQUEST))?$_REQUEST['length']:10;
		$limit = ((array_key_exists('start', $_REQUEST))?$_REQUEST['start']:0) . ', ' . $itemsPerPage;
		$countTotal = $testimonials->countItems();
		if(array_key_exists('filters', $_REQUEST)) {
			foreach($_REQUEST['filters'] AS $key => $value) {
				if(in_array($key, array(
					'name'
				))) $testimonials->$key = array('%' . $value . '%', ' LIKE ');
				else $testimonials->$key = $value;
			}
		}
		$countFiltered = $testimonials->countItems();
		$testimonials->limit($limit);
		$testimonials = $testimonials->get('AND');
		$testimonialsArray = array();
		foreach($testimonials AS $testimonial) {
			unset($testimonial->content);
			$testimonialsArray[] = $testimonial;
		}
		$responseArray = array('sEcho'                => $_REQUEST['secho'],
		                      'iTotalRecords'        => $countTotal,
		                      'iTotalDisplayRecords' => $countFiltered,
		                      'aaData'               => $testimonialsArray
		);
		$response = json_encode($responseArray);
		if(array_key_exists('callback', $_GET)) $response = $_GET['callback'] . '(' . $response . ')';
		echo $response;
	}
	else {
		$testimonial = new \Model\Model('testimonials');
		$testimonial = $testimonial->getOneResult('id', $_REQUEST['id']);
		echo json_encode($testimonial);
	}
}