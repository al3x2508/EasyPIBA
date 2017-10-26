<?php
use Model\Model;
$content = '';
$page_title = __('Password reset');
$description = __('Password reset');
$h1 = '';
$fields = array();
$js=array();
$css=array('cinput.css');
$errors = '';
if(array_key_exists('email',$_POST)) {
	$email = strip_tags(htmlspecialchars(stripslashes(trim($_POST['email']))));
	if(!\Utils\Util::checkFieldValue('email', $email)) $fields['email'] = __('Enter a valid email address');
	else {
		$user = new Model('users');
		$user = $user->getOneResult('email', strip_tags(htmlspecialchars(stripslashes(trim($_POST['email'])))));
		if($user) {
			//Email exists, we send a reset link to the user's email
			$name = $user->firstname . ' ' . $user->lastname;
			$resetModel = new Model('passwords_reset');
			$resetModel->user = $user->id;
			$resetModel->expiration_date = array(date('Y-m-d H:i:s'), '>=');
			$resetModel = $resetModel->get();
			if(count($resetModel) > 0) $code = $resetModel[0]->code;
			else {
				$resetModel = new Model('passwords_reset');
				$resetModel->user = $user->id;
				$resetModel->delete();
				$code = \Utils\Util::storeResetPassword($user->id);
			}
			$resetLink = _ADDRESS_ . _FOLDER_URL_ . 'password_reset.html?code=' . $code;
			$message = '<h2>' . __('Hi') . ' ' . $name . '</h2>
				<p> '. __('To reset your password click the link below') . ':</p>
				<p><a href="' . $resetLink . '">' . $resetLink . '</a></p>';
			\Utils\Util::send_email($email, $name, __('Password reset'), $message, true);
			$content = '<div class="col-lg-12"><h3>' . __('An email with password reset instructions have been sent to you.') . '</h3></div>';
		}
		else $fields['email'] = __('No user registered with this email address');
	}
}
elseif(array_key_exists('code', $_REQUEST)) {
	$fields = array();
	$checkFields = array('password', 'confirmPassword');
	if(array_key_exists('password', $_POST)) {
		foreach($checkFields AS $field) if(array_key_exists($field, $_POST) && !\Utils\Util::checkFieldValue($field, $_POST[ $field ])) $fields[$field] = 1;
		if($_POST['password'] != $_POST['confirmPassword']) $fields['confirmPassword'] = 1;
	}
	if(array_key_exists('password', $_POST) && count($fields) == 0) {
		$code = strip_tags(htmlspecialchars(stripslashes(trim($_REQUEST['code']))));
		$resetModel = new Model('passwords_reset');
		$resetModel->code = $code;
		$resetModel->expiration_date = array(date('Y-m-d H:i:s'), '>=');
		$resetModel = $resetModel->get();
		if(count($resetModel)) {
			$user = $resetModel[0]->users;
			$resetModel[0]->delete();
			$bcrypt = new Utils\Bcrypt(10);
			$user->password = $bcrypt->hash(strip_tags(htmlspecialchars(stripslashes(trim($_POST['password'])))));
			$user->update();
			$content = '<div class="col-lg-12"><h2>' . __('Your password has been updated') . '</h2></div>';
		}
		else $errors = '<div class="alert alert-danger">' . __('Code not found') . '</div>';
	}
	else {
		$password = (array_key_exists('password', $fields) && $fields['password'] == 1)?'<div class="alert alert-danger">' . __('Enter a password') . '</div>':'';
		$confirmPassword = (array_key_exists('confirmPassword', $fields) && $fields['confirmPassword'] == 1)?'<div class="alert alert-danger">' . __('Confirm the password') . '</div>':'';
		$content = '<div class="row justify-content-md-center mt-4">
			<div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3">
				<div class="alert-placeholder"></div>
				<div class="panel panel-login">
					<div class="panel-heading"><div class="row"><div class="col-12 text-center"><h2><b>' . __('Change your password') . '</b></h2></div></div><hr /></div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-12">
								<form action="' . _ADDRESS_ . _FOLDER_URL_ . 'password_reset.html" method="post" autocomplete="off" class="validateform">
									<input type="hidden" name="code" value="' . strip_tags(htmlspecialchars(stripslashes(trim($_REQUEST['code'])))) . '" />
									<div class="col-lg-12 col-12 mt-5 field form-group">
										<div class="input input-hoshi">
											<input type="password" name="password" id="password" class="input__field input__field-hoshi form-control" data-rule="maxlen:8" data-msg="' . sprintf(__('Enter at least %s characters'), '8') . '" pattern=".{8,}" required />
											<label class="input__label input__label-hoshi input__label-hoshi-color-1" for="password" data-ex="' . __('8 characters minimum') . '">
												<span class="input__label-content input__label-content-hoshi"><i class="fa fa-eye-slash"></i> * ' . __('New password') . '</span>
											</label>
											' . $password . '
										</div>
									</div>
									<div class="col-lg-12 col-12 mt-5 field form-group">
										<div class="input input-hoshi">
											<input type="password" name="confirmPassword" id="confirmPassword" class="input__field input__field-hoshi form-control" data-rule="maxlen:8" data-msg="' . __('Confirm password') . '" pattern=".{8,}" required />
											<label class="input__label input__label-hoshi input__label-hoshi-color-1" for="confirmPassword" data-ex="' . __('8 characters minimum') . '">
												<span class="input__label-content input__label-content-hoshi"><i class="fa fa-eye-slash"></i> * ' . __('Confirm new password') . '</span>
											</label>
											' . $confirmPassword . '
										</div>
									</div>
									<div class="col-lg-12 col-12 field form-group">
										<div class="row justify-content-sm-center">
											<div class="col-sm-6">
												<input type="submit" class="form-control btn btn-login" value="' . __('Change password') . '" />
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>';
	}
}
if(empty($content)) {
	$email = array(
		'value' => (array_key_exists('email', $_POST))?' value="' . strip_tags(htmlspecialchars(stripslashes(trim($_POST['email'])))) . '"':'',
		'validation' => (array_key_exists('email', $fields))?'<div class="alert alert-danger">' . $fields['email'] . '</div>':''
	);
	$content = '<div class="row justify-content-md-center mt-4">
			<div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3">
				' . $errors . '
				<div class="panel panel-login">
					<div class="panel-heading"><div class="row"><div class="col-12 text-center"><h2><b>' . __('Password reset') . '</b></h2></div></div><hr /></div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-12">
								<form action="' . _ADDRESS_ . _FOLDER_URL_ . 'password_reset.html" method="post" class="validateform">
									<div class="col-lg-12 col-12 mt-5 field form-group">
										<div class="input input-hoshi">
											<input type="email" name="email" id="email" class="input__field input__field-hoshi form-control" data-rule="maxlen:2" data-msg="' . __('Enter your email') . '"' . $email['value'] . ' pattern="^(?:[\w\d-]+.?)+@(?:(?:[\w\d]-?)+.)+\w{2,4}$" required />
											<label class="input__label input__label-hoshi input__label-hoshi-color-1" for="email" data-ex="eg: john.smith@yahoo.com">
												<span class="input__label-content input__label-content-hoshi"><i class="fa fa-envelope-o"></i> * ' . __('Email') . '</span>
											</label>
											' . $email['validation'] . '
										</div>
									</div>
									<div class="col-lg-12 col-12 field form-group">
										<div class="row justify-content-sm-center">
											<div class="col-sm-6">
												<input type="submit" class="form-control btn btn-login" value="' . __('Send code') . '" />
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>';
}