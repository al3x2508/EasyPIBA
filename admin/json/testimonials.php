<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/Utils/functions.php');
$adminController = new Controller\AdminController();
if($adminController->checkPermission("Edit testimonials")) {
	if(!array_key_exists('id', $_REQUEST)) {
		$testimoniale = new \Model\Model('testimoniale');
		$pagina = (array_key_exists('start', $_REQUEST))?($_REQUEST['start'] / $_REQUEST['length']) + 1:1;
		$itemsPerPage = (array_key_exists('start', $_REQUEST))?$_REQUEST['length']:10;
		$limit = ((array_key_exists('start', $_REQUEST))?$_REQUEST['start']:0) . ', ' . $itemsPerPage;
		$countTotal = $testimoniale->countItems();
		if(array_key_exists('filters', $_REQUEST)) {
			foreach($_REQUEST['filters'] AS $cheie => $valoare) {
				if(in_array($cheie, array(
					'nume'
				))) $testimoniale->$cheie = array('%' . $valoare . '%', ' LIKE ');
				else $testimoniale->$cheie = $valoare;
			}
		}
		$countFiltered = $testimoniale->countItems();
		$testimoniale->limit($limit);
		$tst = $testimoniale->get('AND');
		$testimoniale = array();
		foreach($tst AS $testimonial) {
			unset($testimonial['content']);
			$testimoniale[] = $testimonial;
		}
		$arrayRaspuns = array('sEcho'                => $_REQUEST['secho'],
		                      'iTotalRecords'        => $countTotal,
		                      'iTotalDisplayRecords' => $countFiltered,
		                      'aaData'               => $testimoniale
		);
		$raspuns = json_encode($arrayRaspuns);
		if(array_key_exists('callback', $_GET)) $raspuns = $_GET['callback'] . '(' . $raspuns . ')';
		echo $raspuns;
	}
	else {
		$testimonial = new \Model\Model('testimoniale');
		$testimonial = $testimonial->getOneResult('id', $_REQUEST['id']);
		echo json_encode($testimonial);
	}
}
?>