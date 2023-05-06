<?php
namespace Module\JSON;
use Model\Model;
use Module\Users\Controller;

class Page {
	public static function output() {
		if(arrayKeyExists('action', $_REQUEST)) {
			if($_REQUEST['action'] == 'resend') {
				$user = new Model('users');
				$user = $user->getOneResult('email', $_REQUEST['email']);
				if($user) {
					$send = Controller::sendActivationEmail($user, false);
					if($send) $response = array('message' => __('Confirmation code was sent to your email address'), 'redirect' => $_ENV['ADDRESS'] . $_ENV['FOLDER_URL'] . 'email_confirm');
					else $response = array('message' => __('Your account is already confirmed'));
				}
				else $response = array('message' => __('No account registered with this email address'));
				$response = json_encode($response);
				if(arrayKeyExists('callback', $_GET)) $response = $_GET['callback'] . '(' . $response . ')';
				return $response;
			}
			elseif($_REQUEST['action'] == 'getTari') {
				$countries = new Model('countries');
				$countries = $countries->get();
				$response = array();
				foreach($countries AS $country) $response[$country->id] = $country->name;
				$response = json_encode($response);
				if(arrayKeyExists('callback', $_GET)) $response = $_GET['callback'] . '(' . $response . ')';
				return $response;
			}
			elseif($_REQUEST['action'] == 'getJudete') {
				$judete = new Model('judete');
				$judete = $judete->get();
				$response = array();
				foreach($judete AS $judet) $response[$judet->id] = $judet->nume;
				$response = json_encode($response);
				if(arrayKeyExists('callback', $_GET)) $response = $_GET['callback'] . '(' . $response . ')';
				return $response;
			}
			elseif($_REQUEST['action'] == 'getOras') {
				$orase = new Model('orase');
				$orase->judet = $_REQUEST['judet'];
				$orase->nume = array('%' . $_REQUEST['nume'] . '%', ' LIKE ');
				$orase = $orase->get();
				$response = array();
				foreach($orase AS $oras) $response[$oras->id] = $oras->nume;
				$response = json_encode($response);
				if(arrayKeyExists('callback', $_GET)) $response = $_GET['callback'] . '(' . $response . ')';
				return $response;
			}
		}
		return '';
	}
}