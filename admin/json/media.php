<?php
require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'Utils' . DIRECTORY_SEPARATOR . 'functions.php');
$adminController = new Controller\AdminController();
if($adminController->checkPermission("Editează pages")) {
	if(!array_key_exists('id', $_REQUEST)) {
		$media = new \Model\Model('media');
		$pagina = (array_key_exists('start', $_REQUEST))?($_REQUEST['start'] / $_REQUEST['length']) + 1:1;
		$itemsPerPage = (array_key_exists('start', $_REQUEST))?$_REQUEST['length']:10;
		$limit = ((array_key_exists('start', $_REQUEST))?$_REQUEST['start']:0) . ', ' . $itemsPerPage;
		$countTotal = $media->countItems();
		if(array_key_exists('filters', $_REQUEST)) {
			foreach($_REQUEST['filters'] AS $cheie => $valoare) {
				if(in_array($cheie, array(
					'fisier'
				))) $media->$cheie = array('%' . $valoare . '%', ' LIKE ');
				else $media->$cheie = $valoare;
			}
		}
		$countFiltered = $media->countItems();
		$media->limit($limit);
		$media = $media->get('AND');
		$arrayRaspuns = array('sEcho'                => $_REQUEST['secho'],
		                      'iTotalRecords'        => $countTotal,
		                      'iTotalDisplayRecords' => $countFiltered,
		                      'aaData'               => $media
		);
		$raspuns = json_encode($arrayRaspuns);
		if(array_key_exists('callback', $_GET)) $raspuns = $_GET['callback'] . '(' . $raspuns . ')';
		echo $raspuns;
	}
	else {
		$media = new \Model\Model('media');
		$media = $media->getOneResult('id', $_REQUEST['id']);
		echo json_encode($media);
	}
}
?>