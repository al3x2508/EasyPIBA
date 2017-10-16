<?php
require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'Utils' . DIRECTORY_SEPARATOR . 'functions.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'json.class.php');
class Users extends \json\json {
	public function __construct() {
		self::$permission = "View users";
		self::$columnsMap = array('id' => 'ID',
			'firstname' => __('Firstname'),
			'lastname' => __('Lastname'),
			'email' => __('Email'),
			'phone' => __('Phone'),
			'address' => __('Address'),
			'city' => __('City'),
			'state' => __('State'),
			'country' => __('Country ID'),
			'countries.name' => __('Country'),
			'status' => __('Status ID')
		);
		self::$instanceName = __('Users');
		parent::__construct();
	}
	public static function get() {
		if(!array_key_exists('id', $_REQUEST)) {
			$itemsPerPage = (array_key_exists('start', $_REQUEST))?$_REQUEST['lungime']:10;
			$limit = ((array_key_exists('start', $_REQUEST))?$_REQUEST['start']:0) . ', ' . $itemsPerPage;
			$users = new Model\Model('users');
			if(!array_key_exists('cRecords', $_REQUEST)) self::$countTotal = $users->countItems();
			if(array_key_exists('filters', $_REQUEST)) {
				$where = array();
				foreach($_REQUEST['filters'] AS $key => $value) {
					if(in_array($key, array(
						'firstname',
						'lastname',
						'email'
					))) $users->$key = array('%' . $value . '%', ' LIKE ');
					elseif($key == 'name') {
						$where['CONCAT(firstname, " ", lastname)'] = array(
								'%' . $value . '%',
								'LIKE',
								's'
							);
					}
					else $users->$key = $value;
				}
				if(count($where) > 0) $users->where($where);
			}
			if(!array_key_exists('cRecords', $_REQUEST)) self::$countFiltered = $users->countItems();
			else {
				$users->addCustomField('SUM(IF(status = 1, 1, 0)) AS confirmed');
				$users->addCustomField('COUNT(*) AS totalRecords');
				$ops = $users->get('AND', true);
				self::$data = $ops;
				parent::output();
				exit;
			}
			if(!array_key_exists('export', $_REQUEST)) $users->limit($limit);
			$ops = $users->get('AND', true);
			self::$data = $ops;
			$users = array();
			if(!array_key_exists('secho', $_REQUEST)) {
				foreach($ops AS $user) $users['#' . $user->id] = $user->firstname . ' ' . $user->lastname;
				self::$data = $users;
			}
			parent::output();
		}
		else {
			$user = new Model\Model('users');
			$user = $user->getOneResult('id', $_REQUEST['id']);
			unset($user->password);
			echo json_encode($user);
		}
	}
}
$users = new Users();
$users::get();