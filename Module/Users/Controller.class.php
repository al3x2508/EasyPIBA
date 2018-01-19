<?php
namespace Module\Users;
use Model\Model;
use Utils\Util;

class Controller {
	/**
	 * Account form used for register and my account pages
	 * @param $fields
	 * @param $values
	 * @param $formId
	 * @param bool $button
	 * @param string $errors
	 * @return string
	 */
	public static function getAccountForm($fields, $values, $formId, $button = false, $errors = '') {
		function fieldValue($field, $values) {
			if(array_key_exists($field, $_POST)) return strip_tags(htmlspecialchars(stripslashes(trim($_POST[$field]))));
			if(array_key_exists($field, $values)) return strip_tags(htmlspecialchars(stripslashes(trim($values[$field]))));
			return false;
		}

		$countriesOptions = '	<option value="">' . __('Country') . '</option>' . PHP_EOL;
		$countries = new Model('countries');
		$countries->order('`countries`.`name` ASC');
		$countries = $countries->get();
		foreach($countries AS $country) {
			$selected = ($country->id == fieldValue('country', $values)) ? ' selected' : '';
			$countriesOptions .= '	<option value="' . $country->id . '"' . $selected . '>' . $country->name . '</option>' . PHP_EOL;
		}
		$firstname = array('value' => (fieldValue('firstname', $values)) ? ' value="' . fieldValue('firstname', $values) . '"' : '', 'validation' => (array_key_exists('firstname', $fields) && $fields['firstname'] == 1) ? '<div class="alert alert-danger">' . __('Enter your firstname') . '</div>' : '');
		$lastname = array('value' => (fieldValue('lastname', $values)) ? ' value="' . fieldValue('lastname', $values) . '"' : '', 'validation' => (array_key_exists('lastname', $fields) && $fields['lastname'] == 1) ? '<div class="alert alert-danger">' . __('Enter your lastname') . '</div>' : '');
		$email = array('value' => (fieldValue('email', $values)) ? ' value="' . fieldValue('email', $values) . '"' : '', 'validation' => (array_key_exists('email', $fields) && $fields['email'] == 1) ? '<div class="alert alert-danger">' . __('Enter your email') . '</div>' : '');
		$country = (array_key_exists('country', $fields) && $fields['country'] == 1) ? '<div class="alert alert-danger">' . __('Select your country') . '</div>' : '';
		$password = (array_key_exists('password', $fields) && $fields['password'] == 1) ? '<div class="alert alert-danger">' . __('Enter a password') . '</div>' : '';
		$confirmPassword = (array_key_exists('confirmPassword', $fields) && $fields['confirmPassword'] == 1) ? '<div class="alert alert-danger">' . __('Confirm the password') . '</div>' : '';
		$content = '<form id="' . $formId . '" method="post" action="#" class="validateform">';
		if(!empty($errors)) $content .= '<div class="col-lg-12 col-12"><div class="alert alert-danger">' . $errors . '</div></div>' . PHP_EOL;
		$content .= '<div class="col-lg-12 col-12 mt-5 field form-group">
							<div class="input input-hoshi">
								<input type="text" name="firstname" id="firstname" class="input__field input__field-hoshi form-control" data-rule="maxlen:2" data-msg="' . sprintf(__('Enter at least %s characters'), '2') . '"' . $firstname['value'] . ' pattern=".{2,}" required />
								<label class="input__label input__label-hoshi input__label-hoshi-color-1" for="firstname" data-ex="eg: John">
									<span class="input__label-content input__label-content-hoshi"><i class="fa fa-user"></i> * ' . __('Firstname') . '</span>
								</label>
								' . $firstname['validation'] . '
							</div>
						</div>
						<div class="col-lg-12 col-12 mt-5 field form-group">
							<div class="input input-hoshi">
								<input type="text" name="lastname" id="lastname" class="input__field input__field-hoshi form-control" data-rule="maxlen:2" data-msg="' . sprintf(__('Enter at least %s characters'), '2') . '"' . $lastname['value'] . ' pattern=".{2,}" required />
								<label class="input__label input__label-hoshi input__label-hoshi-color-1" for="lastname" data-ex="eg: Smith">
									<span class="input__label-content input__label-content-hoshi"><i class="fa fa-user"></i> * ' . __('Lastname') . '</span>
								</label>
								' . $lastname['validation'] . '
							</div>
						</div>
						<div class="col-lg-12 col-12 mt-5 field form-group">
							<div class="input input-hoshi">
								<input type="email" name="email" id="email" class="input__field input__field-hoshi form-control" data-rule="maxlen:2" data-msg="' . __('Enter your email') . '"' . $email['value'] . ' pattern="^(?:[\w\d-]+.?)+@(?:(?:[\w\d]-?)+.)+\w{2,4}$" required />
								<label class="input__label input__label-hoshi input__label-hoshi-color-1" for="email" data-ex="eg: john.smith@yahoo.com">
									<span class="input__label-content input__label-content-hoshi"><i class="fa fa-envelope-o"></i> * ' . __('Email') . '</span>
								</label>
								' . $email['validation'] . '
							</div>
						</div>
						<div class="col-lg-12 col-12 mt-5 field form-group">
							<select name="country" id="country" class="form-control" required>
								' . $countriesOptions . '
							</select>
							' . $country . '
						</div>
						<div class="col-lg-12 col-12 mt-5 field form-group">
							<div class="input input-hoshi">
								<input type="password" name="password" id="password" class="input__field input__field-hoshi form-control" data-rule="maxlen:8" data-msg="' . sprintf(__('Enter at least %s characters'), '8') . '" pattern=".{8,}" required />
								<label class="input__label input__label-hoshi input__label-hoshi-color-1" for="password" data-ex="' . __('8 characters minimum') . '">
									<span class="input__label-content input__label-content-hoshi"><i class="fa fa-eye-slash"></i> * ' . __('Password') . '</span>
								</label>
								' . $password . '
							</div>
						</div>
						<div class="col-lg-12 col-12 mt-5 field form-group">
							<div class="input input-hoshi">
								<input type="password" name="confirmPassword" id="confirmPassword" class="input__field input__field-hoshi form-control" data-rule="maxlen:8" data-msg="' . __('Confirm password') . '" pattern=".{8,}" required />
								<label class="input__label input__label-hoshi input__label-hoshi-color-1" for="confirmPassword" data-ex="' . __('8 characters minimum') . '">
									<span class="input__label-content input__label-content-hoshi"><i class="fa fa-eye-slash"></i> * ' . __('Confirm password') . '</span>
								</label>
								' . $confirmPassword . '
							</div>
						</div>
						<div class="col-lg-12 form-group"><em>' . __('Fields marked with') . ' * ' . __('are required') . '.</em></div>' . PHP_EOL;
		if($button) {
			$termsConditions = (array_key_exists('termsConditions', $fields) && $fields['termsConditions'] == 1) ? '<div class="alert alert-danger">' . __('You must accept our Terms and conditions') . '</div>' : '';
			$content .= '<div class="col-lg-12 col-12 field text-center">
							<input type="checkbox" name="termsConditions" id="termsConditions" /><label for="termsConditions">' . sprintf(__('I have read and agree to the %s'), '<a href="terms-conditions.html">' . __('Terms and Conditions') . '</a>') . '</label>
							' . $termsConditions . '
						</div>
						<div class="col-lg-12 col-12 field form-group">
							<div class="row justify-content-sm-center">
								<div class="col-sm-6">
									<input type="submit" class="form-control btn btn-login" value="' . __($button) . '" />
								</div>
							</div>
						</div>' . PHP_EOL;
		}
		$content .= '				</form>';
		return $content;
	}

	/**
	 * Get logged in user
	 * @return Model|bool
	 */
	public static function getCurrentUser($returnId = true) {
		if(array_key_exists('user', $_SESSION)) {
			if($returnId) return $_SESSION['user'];
			else {
				$user = new Model('users');
				$user = $user->getOneResult('id', $_SESSION['user']);
				return $user;
			}
		}
		return false;
	}

	/**
	 * Get user setting
	 * @param $setting
	 * @param bool $user
	 * @return mixed|null|string
	 */
	public static function getUserSetting($setting, $user = false) {
		if(!$user) $user = self::getCurrentUser(false);
		if($user) {
			$accountSettings = json_decode($user->settings, true);
			if(is_array($accountSettings) && array_key_exists($setting, $accountSettings)) return $accountSettings[$setting];
		}
		return null;
	}

	/**
	 * Get user by cookie token
	 * @param $token
	 * @return bool|Model
	 */
	public static function getUserFromToken($token) {
		$cookie = new Model('cookies');
		$cookie->token = $token;
		$cookie->expiration_date = array(date('Y-m-d H:i:s'), '>=');
		$cookie = $cookie->get();

		return (count($cookie) > 0) ? $cookie[0]->users->id : false;
	}

	/**
	 * Store password reset key for user
	 * @param $user
	 * @return string
	 */
	public static function storeResetPassword($user) {
		$token = Util::GenerateRandomToken();
		$resetting = new Model('passwords_reset');
		$resetting->user = $user;
		$resetting->code = $token;
		$resetting->expiration_date = date('Y-m-d H:i:s', strtotime("+1 week"));
		$resetting->create();
		return $token;
	}

	/**
	 * Send activation email
	 * @param $user
	 * @param bool $generate
	 * @return bool
	 */
	public static function sendActivationEmail($user, $generate = true) {
		$code = '';
		$name = $user->lastname . ' ' . $user->firstname;
		$email = $user->email;
		$user_confirm = new Model('user_confirm');
		$user_confirm->user = $user->id;
		if($generate) {
			$code = md5(time());
			$user_confirm->code = $code;
			$user_confirm->create();
		}
		else {
			$user_confirm = $user_confirm->get();
			if(count($user_confirm) > 0) $code = $user_confirm[0]->code;
		}
		if(!empty($code)) {
			$activationLink = _ADDRESS_ . _FOLDER_URL_ . 'email_confirm';
			$message = /** @lang text */
				"<h2>" . __("Welcome") . ", {$user->firstname}!</h2>
				<h3>" . __("Step") . " 1. " . __("Activate your account") . "!</h3>
				<p>" . __("Activate your account") . " {$email} " . __("by clicking this link") . ": <a href=\"{$activationLink}?key={$code}\">{$activationLink}?key={$code}</a></p>";
			Util::send_email($email, $name, __('Activate your account'), $message, true);
			return true;
		}
		else return false;
	}

	/**
	 * Cookie generator for keep logged in
	 * @param $user
	 */
	public static function storeCookie($user) {
		$token = Util::GenerateRandomToken(24, true);
		self::storeTokenForUser($user, $token);
		$cookie = $token;
		$mac = hash_hmac('sha256', $cookie, _HASH_KEY_);
		$cookie .= ':' . $mac;
		\setcookie('rme' . _CACHE_PREFIX_, $cookie, time() + 60 * 60 * 24 * 30);
	}

	/**
	 * Store cookie for user in database
	 * @param $user
	 * @param $token
	 */
	private static function storeTokenForUser($user, $token) {
		$cookie = new Model('cookies');
		$cookie->user = $user;
		$cookie->token = $token;
		$cookie->expiration_date = date('Y-m-d H:i:s', strtotime("+1 week"));
		$cookie->create();
	}

	/**
	 * Logout user
	 */
	public static function logout() {
		if(isset($_SESSION)) {
			if(array_key_exists('user', $_SESSION)) {
				$cookie = new Model('cookies');
				$cookie->__set('user', $_SESSION['user'])->delete();
				unset($_SESSION);
				session_destroy();
			}
			header("Location: " . _FOLDER_URL_);
		}
		exit;
	}
}