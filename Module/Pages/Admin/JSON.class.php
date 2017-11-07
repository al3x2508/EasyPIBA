<?php
namespace Module\Pages\Admin;
use Model\Model;
use Module\JSON\Admin;

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/Utils/functions.php');
class JSON extends Admin {
	public function __construct() {
		$this->permission = "Edit pages";
		parent::__construct();
	}
	public function get() {
		if(!array_key_exists('id', $_REQUEST)) {
			$pages = new Model('pages');
			if(!array_key_exists('menu', $_REQUEST)) {
				$itemsPerPage = (array_key_exists('start', $_REQUEST))?$_REQUEST['length']:10;
				$limit = ((array_key_exists('start', $_REQUEST))?$_REQUEST['start']:0) . ', ' . $itemsPerPage;
				$countTotal = $pages->countItems();
				if(array_key_exists('filters', $_REQUEST)) {
					foreach($_REQUEST['filters'] AS $key => $value) {
						if(in_array($key, array(
							'url',
							'title',
							'menu_text',
							'submenu_text'
						))) $pages->$key = array('%' . $value . '%', ' LIKE ');
						else $pages->$key = $value;
					}
				}
				$countFiltered = $pages->countItems();
				$pages->limit($limit);
				$pg = $pages->get('AND');
				$pages = array();
				foreach($pg AS $page) {
					unset($page->content);
					$pages[] = $page;
				}
				$arrayResponse = array('sEcho' => $_REQUEST['secho'], 'iTotalRecords' => $countTotal, 'iTotalDisplayRecords' => $countFiltered, 'aaData' => $pages);
			}
			else {
				if(!empty($_REQUEST['language'])) $pages->language = $_REQUEST['language'];
				$pages->order('menu_parent ASC , menu_order ASC');
				$pg = $pages->get();
				$pages = array();
				foreach($pg AS $page) {
					unset($page->content);
					$pages[] = $page;
				}
				$arrayResponse = $pages;
			}
			$response = json_encode($arrayResponse);
			if(array_key_exists('callback', $_GET)) $response = $_GET['callback'] . '(' . $response . ')';
			echo $response;
		}
		else {
			$page = new Model('pages');
			$page = $page->getOneResult('id', $_REQUEST['id']);
			echo json_encode($page);
		}
	}
}