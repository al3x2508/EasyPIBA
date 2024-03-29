<?php
namespace Module\Testimonials\Admin;
use Model\Model;
use Module\JSON\Admin;

require_once(dirname(__FILE__, 4) . '/Utils/functions.php');
class JSON extends Admin {
	public function __construct() {
		$this->permission = "Edit testimonials";
		parent::__construct();
	}
	public function get() {
		if(!arrayKeyExists('id', $_REQUEST)) {
			$testimonials = new Model('testimonials');
			$itemsPerPage = (arrayKeyExists('start', $_REQUEST))?$_REQUEST['length']:10;
			$limit = ((arrayKeyExists('start', $_REQUEST))?$_REQUEST['start']:0) . ', ' . $itemsPerPage;
			$countTotal = $testimonials->countItems();
			if(arrayKeyExists('filters', $_REQUEST)) {
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
			if(arrayKeyExists('callback', $_GET)) $response = $_GET['callback'] . '(' . $response . ')';
			echo $response;
		}
		else {
			$testimonial = new Model('testimonials');
			$testimonial = $testimonial->getOneResult('id', $_REQUEST['id']);
			echo json_encode($testimonial);
		}
	}
}