<?php
namespace Module\Administrators\Admin;
use Model\Model;
use Module\JSON\Admin;

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/Utils/functions.php');
class JSON extends Admin {
	public function __construct() {
		$this->permission = "Edit administrators";
		parent::__construct();
	}
	public function get() {
		$admins = new Model('admins');
		if(!array_key_exists('id', $_REQUEST)) {
			$itemsPerPage = (array_key_exists('start', $_REQUEST))?$_REQUEST['length']:10;
			$limit = ((array_key_exists('start', $_REQUEST))?$_REQUEST['start']:0) . ', ' . $itemsPerPage;
			$countTotal = $admins->countItems();
			if(array_key_exists('filters', $_REQUEST)) {
				foreach($_REQUEST['filters'] AS $key => $value) {
					if(in_array($key, array(
						'name', 'username'
					))) $admins->$key = array('%' . $value . '%', ' LIKE ');
					else $admins->$key = $value;
				}
			}
			$countFiltered = $admins->countItems();
			$admins->limit($limit);
			$admins = $admins->get('AND', true);
			if(array_key_exists('secho', $_REQUEST)) $response = json_encode(array('sEcho' => $_REQUEST['secho'], 'iTotalRecords' => $countTotal, 'iTotalDisplayRecords' => $countFiltered, 'aaData' => $admins));
			else {
				$adminsArray = array();
				foreach($admins AS $admin) $adminsArray['#' . $admin->id] = $admin->name;
				$response = json_encode($adminsArray);
			}
			if(array_key_exists('callback', $_GET)) $response = $_GET['callback'] . '(' . $response . ')';
			echo $response;
		}
		else {
			$admin = $admins->getOneResult('id', $_REQUEST['id']);
			unset($admin->password);
			$access = new Model('admins_permissions');
			$access->admin = $_REQUEST['id'];
			$admin->access = $access->get();
			echo json_encode($admin);
		}
	}
}