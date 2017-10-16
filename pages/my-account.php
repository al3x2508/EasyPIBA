<?php
$mustBeLoggedIn = true;
$user = \Utils\Util::getCurrentUser();
if($user) {
	$page_title = __('My account');
	$description = __('My account');
	$h1 = '';
	$js = array('validate.min.js', 'my-account.js');
	$css = array('cinput.css');
	$content = '';
	$fields = array();
	$message = '';
	if(array_key_exists('firstname', $_POST) && array_key_exists('email', $_POST)) {
		$checkFields = array('firstname', 'lastname', 'email', 'country', 'CSRFToken');
		foreach($checkFields AS $field) {
			if(!array_key_exists($field, $_POST)) $fields[ $field ] = 1;
			else if(array_key_exists($field, $_POST) && !\Utils\Util::checkFieldValue($field, $_POST[ $field ])) $fields[ $field ] = 1;
		}
		if(count($fields) == 0) {
			$email = strtolower(strip_tags($_POST['email']));
			$userModel = new Model\Model('users');
			$userModel = $userModel->getOneResult('email', $email);
			if(!$userModel || $userModel->id == $user->id) {
				$password = strip_tags($_POST['password']);
				$cpassword = strip_tags($_POST['cpassword']);
				if($password == $cpassword) {
					$logout = false;
					if($lastname = @ucwords(strtolower(strip_tags(htmlspecialchars(stripslashes(trim($_POST['lastname']))))), " -")) $firstname = ucwords(strtolower(strip_tags(htmlspecialchars(stripslashes(trim($_POST['firstname']))))), " -");
					else {
						$lastname = \Utils\Util::ucname(strip_tags(htmlspecialchars(stripslashes(trim($_POST['lastname'])))));
						$firstname = \Utils\Util::ucname(strip_tags(htmlspecialchars(stripslashes(trim($_POST['firstname'])))));
					}
					$user->lastname = !empty($lastname)?$lastname:$_POST['lastname'];
					$user->firstname = !empty($firstname)?$firstname:$_POST['firstname'];
					$bcrypt = new Utils\Bcrypt(10);
					if($user->email != strip_tags($_POST['email'])) {
						$logout = true;
						$user->email = strip_tags($_POST['email']);
						$user->status = 0;
						\Utils\Util::sendActivationEmail($user);
					}
					if(array_key_exists('country', $_POST) && $_POST['country'] != 0) $user->country = $_POST['country'];
					if(!empty($password)) {
						$logout = true;
						$user->password = $bcrypt->hash($password);
					}
					$user->update();
					if($logout) \Utils\Util::logout();
				}
				else {
					$message = __('Confirm the password');
					$fields['cpassword'] = 1;
				}
			}
			else $message = __('There is another user registered with this email address');
		}
		else $message = __('You did not filled the following inputs') . "\n" . print_r($fields, true);
	}
	$values = array(
		'firstname' => $user->firstname,
		'lastname' => $user->lastname,
		'email' => $user->email,
		'country' => $user->country
	);
	$alert = (!empty($message))?'								<div class="col-lg-12 col-12"><div class="alert alert-danger">' . $message . '</div></div>' . PHP_EOL:'';
	$content .= '<div class="row justify-content-md-center mt-4">
			<div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3">
				<div class="panel panel-login">
					<div class="panel-heading"><div class="row"><div class="col-12 text-center"><h2><b>' . __('My account') . '</b></h2></div></div><hr /></div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-12">' . PHP_EOL . $alert . '
								<!-- Start viewinfo -->
								<div id="viewinfo"' . ((!empty($message))?' style="display:none;"':'') . '>
									<div class="col-lg-12 col-12 mt-5">
										<dl class="dl-horizontal dl-small">
											<dt>' . __('Firstname') . '</dt>
											<dd>' . htmlentities($values['firstname']) . '</dd>
											<dt>' . __('Lastname') . '</dt>
											<dd>' . htmlentities($values['lastname']) . '</dd>
											<dt>' . __('Email') . '</dt>
											<dd>' . htmlentities($values['email']) . '</dd>
											<dt>' . __('Country') . '</dt>
											<dd>' . ($user->country?htmlentities($user->countries->name):'&nbsp;') . '</dd>
										</dl>
									</div>
								</div>
								<!-- End viewinfo -->
								<!-- Start editinfo -->
								<div id="editinfo"' . ((empty($message))?' style="display:none;"':'') . '>
									' . \Utils\Util::getAccountForm($fields, $values, 'myaccount', false) . '
								</div>
								<!-- End editinfo -->
							</div>
							<div class="col-lg-12 col-12 field form-group">
								<div class="row justify-content-sm-center">
									<div class="col-sm-6">
										<button id="edit" class="form-control btn btn-login" data-action="edit">' . __('Edit') . '</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>';
}
else header("Location: /");