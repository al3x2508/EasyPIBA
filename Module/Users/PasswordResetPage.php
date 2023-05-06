<?php
namespace Module\Users;
use Model\Model;
use Utils\Bcrypt;
use Utils\Util;

class PasswordResetPage {
	public $mustBeLoggedIn = false;
	public $title = false;
	public $content = false;
	public $description = '';
	public $ogimage = '';
	public $h1 = '';
	public $breadcrumbs = array();
	public $js = array();
	public $css = array('cinput.css');
	public $visible = true;

	public function __construct() {
		$this->content = '';
		$this->title = __('Password reset');
		$this->description = __('Password reset');
		$fields = array();
		$errors = '';
		if(arrayKeyExists('email',$_POST)) {
			$email = strip_tags(htmlspecialchars(stripslashes(trim($_POST['email']))));
			if(!Util::checkFieldValue('email', $email)) $fields['email'] = __('Enter a valid email address');
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
						$code = Controller::storeResetPassword($user->id);
					}
					$resetLink = $_ENV['ADDRESS'] . $_ENV['FOLDER_URL'] . 'password_reset?code=' . $code;
					$message = '<h2>' . __('Hi') . ' ' . $name . '</h2>
				<p> '. __('To reset your password click the link below') . ':</p>
				<p><a href="' . $resetLink . '">' . $resetLink . '</a></p>';
					Util::send_email($email, $name, __('Password reset'), $message, true);
					$this->content = '<div class="col-lg-12"><h3>' . __('An email with password reset instructions have been sent to you.') . '</h3></div>';
				}
				else $fields['email'] = __('No user registered with this email address');
			}
		}
		elseif(arrayKeyExists('code', $_REQUEST)) {
			$fields = array();
			$checkFields = array('password', 'confirmPassword');
			if(arrayKeyExists('password', $_POST)) {
				foreach($checkFields AS $field) if(arrayKeyExists($field, $_POST) && !Util::checkFieldValue($field, $_POST[ $field ])) $fields[$field] = 1;
				if($_POST['password'] != $_POST['confirmPassword']) $fields['confirmPassword'] = 1;
			}
			if(arrayKeyExists('password', $_POST) && count($fields) == 0) {
				$code = strip_tags(htmlspecialchars(stripslashes(trim($_REQUEST['code']))));
				$resetModel = new Model('passwords_reset');
				$resetModel->code = $code;
				$resetModel->expiration_date = array(date('Y-m-d H:i:s'), '>=');
				$resetModel = $resetModel->get();
				if(count($resetModel)) {
					$user = $resetModel[0]->users;
					$resetModel[0]->delete();
					$bcrypt = new Bcrypt(10);
					$user->password = $bcrypt->hash(strip_tags(htmlspecialchars(stripslashes(trim($_POST['password'])))));
					$user->update();
					$this->content = '<div class="col-lg-12"><h2>' . __('Your password has been updated') . '</h2></div>';
				}
				else $errors = '<div class="alert alert-danger">' . __('Code not found') . '</div>';
			}
			else {
				$password = (arrayKeyExists('password', $fields) && $fields['password'] == 1)?'<div class="alert alert-danger">' . __('Enter a password') . '</div>':'';
				$confirmPassword = (arrayKeyExists('confirmPassword', $fields) && $fields['confirmPassword'] == 1)?'<div class="alert alert-danger">' . __('Confirm the password') . '</div>':'';
				$this->content = '<div class="row justify-content-md-center mt-4">
			<div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3">
				<div class="alert-placeholder"></div>
				<div class="panel panel-login">
					<div class="panel-heading"><div class="row"><div class="col-12 text-center"><h2><b>' . __('Change your password') . '</b></h2></div></div><hr /></div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-12">
								<form action="' . $_ENV['ADDRESS'] . $_ENV['FOLDER_URL'] . 'password_reset" method="post" autocomplete="off" class="validateform">
									<input type="hidden" name="code" value="' . strip_tags(htmlspecialchars(stripslashes(trim($_REQUEST['code'])))) . '" />
									<div class="col-lg-12 col-12 mt-5 field form-group">
										<div class="input">
											<input type="password" name="password" id="password" class="input__field input__field-hoshi form-control" data-rule="maxlen:8" data-msg="' . sprintf(__('Enter at least %s characters'), '8') . '" pattern=".{8,}" required />
											<label class="input__label input__label-hoshi input__label-hoshi-color-1" for="password" data-ex="' . __('8 characters minimum') . '">
												<span class="input__label-content input__label-content-hoshi"><i class="fa fa-eye-slash"></i> * ' . __('New password') . '</span>
											</label>
											' . $password . '
										</div>
									</div>
									<div class="col-lg-12 col-12 mt-5 field form-group">
										<div class="input">
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
		if(empty($this->content)) {
			$email = array('value' => (arrayKeyExists('email', $_POST)) ? ' value="' . strip_tags(htmlspecialchars(stripslashes(trim($_POST['email'])))) . '"' : '', 'validation' => (arrayKeyExists('email', $fields)) ? '<div class="alert alert-danger">' . $fields['email'] . '</div>' : '');
			$this->content = '<div class="row justify-content-md-center mt-4">
				<div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3">
					' . $errors . '
					<div class="panel panel-login">
						<div class="panel-heading"><div class="row"><div class="col-12 text-center"><h2><b>' . __('Password reset') . '</b></h2></div></div><hr /></div>
						<div class="panel-body">
							<div class="row">
								<div class="col-lg-12">
									<form action="' . $_ENV['ADDRESS'] . $_ENV['FOLDER_URL'] . 'password_reset" method="post" class="validateform">
										<div class="col-lg-12 col-12 mt-5 field form-group">
											<div class="input">
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
		return $this;
	}
}