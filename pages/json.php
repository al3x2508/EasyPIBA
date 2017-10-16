<?php
if(array_key_exists('action', $_REQUEST)) {
	if($_REQUEST['action'] == 'resend') {
		$response = array();
		$user = new Model\Model('users');
		$user = $user->getOneResult('email', $_REQUEST['email']);
		if($user) {
			$send = \Utils\Util::sendActivationEmail($user, false);
			if($send) $response = array('message' => __('Confirmation code was sent to your email address'), 'redirect' => _ADDRESS_ . _FOLDER_URL_ . 'email_confirm.html');
			else $response = array('message' => __('Your account is already confirmed'));
		}
		else $response = array('message' => __('No account registered with this email address'));
		$response = json_encode($response);
		if(array_key_exists('callback', $_GET)) $response = $_GET['callback'] . '(' . $response . ')';
		echo $response;
	}
}