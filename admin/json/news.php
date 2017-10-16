<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/Utils/functions.php');
$adminController = new Controller\AdminController();
if($adminController->checkPermission("Edit news")) {
	if(!array_key_exists('id', $_REQUEST)) {
		$news = new \Model\Model('news');
		$pageNo = (array_key_exists('start', $_REQUEST))?($_REQUEST['start'] / $_REQUEST['length']) + 1:1;
		$itemsPerPage = (array_key_exists('start', $_REQUEST))?$_REQUEST['length']:10;
		$limit = ((array_key_exists('start', $_REQUEST))?$_REQUEST['start']:0) . ', ' . $itemsPerPage;
		$countTotal = $news->countItems();
		if(array_key_exists('filters', $_REQUEST)) {
			foreach($_REQUEST['filters'] AS $key => $value) {
				if(in_array($key, array(
					'title'
				))) $news->$key = array('%' . $value . '%', ' LIKE ');
				else $news->$key = $value;
			}
		}
		$countFiltered = $news->countItems();
		$news->limit($limit);
		$news->order('date_published DESC');
		$news = $news->get('AND');
		$newsArray = array();
		foreach($news AS $n) {
			unset($n->content);
			$n->image = str_replace('.jpg', '-360x220.jpg', $n->image);
			$newsArray[] = $n;
		}
		$responseArray = array('sEcho'                => $_REQUEST['secho'],
		                      'iTotalRecords'        => $countTotal,
		                      'iTotalDisplayRecords' => $countFiltered,
		                      'aaData'               => $newsArray
		);
		$response = json_encode($responseArray);
		if(array_key_exists('callback', $_GET)) $response = $_GET['callback'] . '(' . $response . ')';
		echo $response;
	}
	else {
		$news = new \Model\Model('news');
		$news = $news->getOneResult('id', $_REQUEST['id']);
		$news->image = str_replace('.jpg', '-360x220.jpg', $news->image);
		echo json_encode($news);
	}
}