<?php
$content = '';
$login_errors = array();
$register_errors = array();
//Check login
if(array_key_exists('username', $_POST) && array_key_exists('password', $_POST) && array_key_exists('CSRFName', $_POST) && array_key_exists('CSRFToken', $_POST)) {
	if(\Utils\Util::csrfguard_validate_token($_POST['CSRFName'], $_POST['CSRFToken'])) {
		$bcrypt = new Utils\Bcrypt(10);
		$user = new Model\Model('users');
		$user = $user->getOneResult('email', strip_tags($_POST['username']));
		if($user) {
			$userId = $user->id;
			$status = $user->status;
			if($status != 1) {
				header('HTTP/1.0 401 Unauthorized');
				$login_errors[] = ($status == 0) ? __('You did not confirmed your account') : __('Your account is blocked');
			}
			else {
				$isGood = $bcrypt->verify(strip_tags($_POST['password']), $user->password);
				//Successfully logged in
				if($isGood) {
					$_SESSION['user'] = $userId;
					if(array_key_exists('keepLoggedIn', $_POST)) Utils\Util::storeCookie($userId);
					$redirectUrl = _DEFAULT_REDIRECT_;
					$location = (array_key_exists('ref', $_SESSION['site'])) ? $_SESSION['site']['ref'] : $redirectUrl;
					header("Location: {$location}");
					exit;
				}
				else {
					header('HTTP/1.0 401 Unauthorized');
					$login_errors[] = __('Incorrect password');
				}
			}
		}
		else {
			header('HTTP/1.0 401 Unauthorized');
			$login_errors[] = __('No user registered with these info');
		}
	}
	else {
		header('HTTP/1.0 401 Unauthorized');
		$login_errors[] = __('Invalid token');
	}
}
//Display login / register page
$fields = array();
//Check if register form was submited
if(array_key_exists('lastname', $_POST) && array_key_exists('email', $_POST)) {
	//Check register fields
	$checkFields = array('lastname', 'firstname', 'email', 'password', 'cpassword', 'termsConditions', 'CSRFToken');
	foreach($checkFields AS $field) {
		if(!array_key_exists($field, $_POST)) $fields[$field] = 1;
		else if(array_key_exists($field, $_POST) && !\Utils\Util::checkFieldValue($field, $_POST[$field])) $fields[$field] = 1;
	}
	if(count($fields) == 0) {
		$email = strtolower(strip_tags($_POST['email']));
		$user = new Model\Model('users');
		$user = $user->getOneResult('email', $email);
		if($user) $register_errors[] = __('There is another user registered with this email address');
		else {
			$password = strip_tags($_POST['password']);
			if($password == strip_tags($_POST['cpassword'])) {
				$lastname = ucwords(strtolower(strip_tags(htmlspecialchars(stripslashes(trim($_POST['lastname']))))), " -");
				$firstname = ucwords(strtolower(strip_tags(htmlspecialchars(stripslashes(trim($_POST['firstname']))))), " -");
				$bcrypt = new Utils\Bcrypt(10);
				$user->lastname = $lastname;
				$user->firstname = $firstname;
				$user->email = $email;
				$user->status = 0;
				$user->password = $bcrypt->hash($password);
				$user->newsletter = (array_key_exists('subscribe', $_POST)) ? 1 : 0;
				$id = $user->create();
				\Utils\Util::sendActivationEmail($user);
				$content .= /** @lang text */
					'<script type="text/javascript">
						setTimeout("location.href = \'/email_confirm.html\';",1000);
					</script>
					' . __('You will be redirected to confirm your email in 1 second');
			}
			else {
				$register_errors[] = __('Password not confirmed');
				$fields['cpassword'] = 1;
			}
		}
	}
	else {
		$register_errors = "Nu ați completat/bifat câmpurile:\n";
		foreach($fields AS $cheie => $val) {
			switch($cheie) {
				case 'lastname':
					$register_errors[] = '<br />' . __('lastname');
					break;
				case 'firstname':
					$register_errors[] = '<br />' . __('firstname');
					break;
				case 'email':
					$register_errors[] = '<br />' . __('email address');
					break;
				case 'password':
					$register_errors[] = '<br />' . __('password');
					break;
				case 'cpassword':
					$register_errors[] = '<br />' . __('password confirm');
					break;
				case 'termsConditions':
					$register_errors[] = '<br />' . __('terms and conditions');
					break;
			}
		}
	}
}
$usernameValue = (array_key_exists('username', $_POST))?' value="' . $_POST['username'] . '"':'';
$content .= '<div class="row justify-content-md-center mt-4">			
			<div class="col-md-6">
			<div class="panel panel-login">
					<div class="panel-heading">
						<div class="row">
							<div class="col-6">
								<a href="#" class="active" id="login-form-link">' . __('Login') . '</a>
							</div>
							<div class="col-6">
								<a href="#" id="register-form-link">' . __('Register') . '</a>
							</div>
						</div>
						<hr>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-12">
								<form id="login-form" action="#" method="post" style="display: block;" class="validateform">';
if(count($login_errors)) $content .= '									<div class="col-lg-12 col-12"><div class="alert alert-danger">' . implode("\n", $login_errors) . '</div></div>' . PHP_EOL;
$content .= '									<div class="col-lg-12 col-12 mt-5 field form-group">
										<div class="input input-hoshi">
											<input type="text" name="username" id="username" class="input__field input__field-hoshi form-control"' . $usernameValue . ' data-rule="maxlen:4" required />
											<label class="input__label input__label-hoshi input__label-hoshi-color-1" for="username" data-ex="eg: john.smith@yahoo.com">
												<span class="input__label-content input__label-content-hoshi">' . __('E-mail') . '</span>
											</label>
										</div>
									</div>
									<div class="col-lg-12 col-12 mt-5 field form-group">
										<div class="input input-hoshi">
											<input type="password" name="password" id="password" class="input__field input__field-hoshi form-control" data-rule="maxlen:4" required />
											<label class="input__label input__label-hoshi input__label-hoshi-color-1" for="password">
												<span class="input__label-content input__label-content-hoshi"><i class="fa fa-eye-slash"></i> * ' . __('Password') . '</span>
											</label>
										</div>
									</div>
									<div class="col-lg-12 col-12 field form-group text-center">
										<input type="checkbox" tabindex="3" class="" name="remember" id="remember" />
										<label for="remember"> ' . __('Remember me') . '</label>
									</div>
									<div class="col-lg-12 col-12 field form-group">
										<div class="row justify-content-sm-center">
											<div class="col-sm-6">
												<input type="submit" name="login-submit" id="login-submit" tabindex="4" class="form-control btn btn-login" value="' . __('Log in') . '" />
											</div>
										</div>
									</div>
									<div class="col-lg-12 col-12 field form-group">
										<div class="row">
											<div class="col-lg-12">
												<div class="text-center">
													<a href="password_reset.html" tabindex="5" class="forgot-password">' . __('Forgot Password?') . '</a>
												</div>
											</div>
										</div>
									</div>
								</form>';
$content .= \Utils\Util::getAccountForm($fields, array(), 'register-form', true, __('Register'), $register_errors) . '
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>';
$page_title = __('Login | ' . _APP_NAME_);
$description = __('Login | ' . _APP_NAME_);
$h1 = '';
$js = array('login.js', 'validate.min.js', 'svgcheckbx.js');
$css = array('login.css', 'cinput.css');
$bara = array('login.html' => 'Autentificare');
