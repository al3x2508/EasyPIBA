<?php
namespace Utils {

	use Controller\Mail;
	use Model\Model;

	if(!defined("_FOLDER_URL_")) {
		/**
		 * Database host
		 */
		define("_DB_HOST_", 'localhost');
		/**
		 * Database port
		 */
		define("_DB_PORT_", 3336);
		/**
		 * Database name
		 */
		define("_DB_NAME_", 'dbf');
		/**
		 * Database username
		 */
		define("_DB_USER_", 'dbf');
		/**
		 * Database password
		 */
		define("_DB_PASS_", 'test');
		/**
		 * Website folder
		 */
		define("_FOLDER_URL_", '/');
		/**
		 * Website root address
		 */
		define("_ADDRESS_", "http://192.168.0.144");
		/**
		 * Default redirect after login
		 */
		define("_DEFAULT_REDIRECT_", _ADDRESS_);
		/**
		 * Application name
		 */
		define("_APP_NAME_", "EasyPBI");
		/**
		 * Company name
		 */
		define("_COMPANY_NAME_", "EasyPBI Ltd.");
		/**
		 * Company address (full address)
		 */
		define("_COMPANY_ADDRESS_", "Address line 1, Address line 2");
		/**
		 * Company address (line 1)
		 */
		define("_COMPANY_ADDRESS_L1_", "Address line 1");
		/**
		 * Company address (line 2)
		 */
		define("_COMPANY_ADDRESS_L2_", "Address line 2");
		/**
		 * Company phone
		 */
		define("_COMPANY_PHONE_", "+40 722 222 555");
		/**
		 * Default application language
		 */
		define("_DEFAULT_LANGUAGE_", 'en');
		/**
		 * Entery your hash key
		 */
		define("_HASH_KEY_", "EnterYourHash");
		/**
		 * Facebook App Id
		 */
		define("_FBAPPID_", "");
		/**
		 * Facebook App Secret
		 */
		define("_FBAPPSECRET_", "");
		/**
		 * Google Analytics ID
		 */
		define("_GOOGLEANALYTICSID_", "");
		/**
		 * Facebook page url
		 */
		define("_FB_LINK_", "");
		/**
		 * Twitter page url
		 */
		define("_TWITTER_LINK_", "");
		/**
		 * LinkedIn page url
		 */
		define("_LINKEDIN_LINK_", "");
		/**
		 * Pinterest page url
		 */
		define("_PINTEREST_LINK_", "");
		/**
		 * Google+ page url
		 */
		define("_GPLUS_LINK_", "");
		/**
		 * Instagram page url
		 */
		define("_INSTAGRAM_LINK_", "");
		/**
		 * Open Graph image url
		 */
		define("_OG_IMAGE_", _ADDRESS_ . '/img/og_image.png');
		/**
		 * Email server address (for contact forms, newsletter etc.)
		 */
		define("_MAIL_HOST_", "");
		/**
		 * Email server port (for contact forms, newsletter etc.)
		 */
		define("_MAIL_PORT_", "25");
		/**
		 * Email username (for contact forms, newsletter etc.)
		 */
		define("_MAIL_USER_", '');
		/**
		 * Email password (for contact forms, newsletter etc.)
		 */
		define("_MAIL_PASS_", '');
		/**
		 * Email address (for contact forms, newsletter etc.)
		 */
		define("_MAIL_FROM_", "noreply@easypbi.com");
		/**
		 * Email display name (for contact forms, newsletter etc.)
		 */
		define("_MAIL_NAME_", "Webmaster");
		/**
		 * Email for respond (for contact forms, newsletter etc.)
		 */
		define("_MAIL_ADDRESS_", "contact@easypbi.com");
		/**
		 * Set to true if site is currently under maintenance
		 */
		define("MAINTENANCE", false);
		/**
		 * DO NOT MODIFY Application working directory
		 */
		define("_APP_DIR_", realpath(dirname(dirname(__FILE__))) . '/');
		/**
		 * DO NOT MODIFY HTML Templates directory
		 */
		define("_TEMPLATE_DIR_", _APP_DIR_ . 'templates/');
	}

	/**
	 * Class Util
	 * @package Utils
	 */
	class Util {
		/**
		 * Autoload register classes
		 * @param $class_name
		 */
		public static function register($class_name) {
			$class_name = str_replace("\\", '/', $class_name);
			@include_once(_APP_DIR_ . $class_name . '.class.php' . '');
		}

		/**
		 * Get user setting
		 * @param $setting
		 * @param bool $user
		 * @return mixed|null|string
		 */
		public static function getUserSetting($setting, $user = false) {
			if($setting == 'language') return self::getUserLanguage($user);
			if(!$user) $user = self::getCurrentUser();
			if($user) {
				$accountSettings = json_decode($user->settings, true);
				if(is_array($accountSettings) && array_key_exists($setting, $accountSettings)) return $accountSettings[$setting];
			}
			return null;
		}

		/**
		 * Get user language
		 * @param bool $user
		 * @return mixed|string
		 */
		public static function getUserLanguage($user = false) {
			if(array_key_exists('language', $_COOKIE)) return $_COOKIE['language'];
			if(!$user) $user = self::getCurrentUser();
			if($user) {
				$accountSettings = json_decode($user->settings, true);
				if(is_array($accountSettings) && array_key_exists('language', $accountSettings)) return $accountSettings['language'];
			}
			$acceptLanguage = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
			return (!empty($acceptLanguage)?$acceptLanguage:_DEFAULT_LANGUAGE_);
		}

		/**
		 * Get logged in user
		 * @return Model|bool
		 */
		public static function getCurrentUser() {
			if(array_key_exists('user', $_SESSION)) {
				$user = new Model('users');
				return $user->getOneResult('id', $_SESSION['user']);
			}
			return false;
		}

		/**
		 * Get the user IP
		 * @return mixed
		 */
		public static function getUserIP() {
			if(!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
			elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
			else return $_SERVER['REMOTE_ADDR'];
		}

		/**
		 * Transform string to url
		 * @param $string
		 * @return string
		 */
		public static function getUrlFromString($string) {
			$characters = array("-", "$", "+", "/", ":", ";", "=", "?", "@", " ", "'", "\"", "<", ">", ",", "[", "]", "!");
			$url = str_replace($characters, "_", strtolower($string));
			$url = iconv('utf-8', 'us-ascii//TRANSLIT', $url);
			$url = strtolower($url);
			$url = preg_replace('/[^_\w]+/', '_', $url);
			return strtolower($url);
		}

		/**
		 * Generate CSRF Token and store it in session
		 * @param $unique_form_name
		 * @return string
		 */
		public static function csrfguard_generate_token($unique_form_name) {
			if(function_exists("hash_algos") and in_array("sha512", hash_algos())) $token = hash("sha512", mt_rand(0, mt_getrandmax()));
			else {
				$token = ' ';
				for($i = 0; $i < 128; ++$i) {
					$r = mt_rand(0, 35);
					$c = ($r < 26) ? chr(ord('a') + $r) : chr(ord('0') + $r - 26);
					$token .= $c;
				}
			}
			$_SESSION[$unique_form_name] = $token;
			return $token;
		}

		/**
		 * Get user by cookie token
		 * @param $token
		 * @return bool
		 */
		public static function getUserFromToken($token) {
			$cookie = new Model('cookies');
			$cookie->token = $token;
			$cookie->expiration_date = array(date('Y-m-d H:i:s'), '>=');
			$cookie = $cookie->get();

			return (count($cookie) > 0) ? $cookie[0]['user'] : false;
		}

		/**
		 * Store password reset key for user
		 * @param $user
		 * @return string
		 */
		public static function storeResetting($user) {
			$token = self::GenerateRandomToken();
			$resetting = new Model('passwords_reset');
			$resetting->user = $user;
			$resetting->code = $token;
			$resetting->expiration_date = date('Y-m-d H:i:s', strtotime("+1 week"));
			$resetting->create();
			return $token;
		}

		/**
		 * Generate randon token
		 * @param int $length
		 * @param bool $strong
		 * @return string
		 */
		public static function GenerateRandomToken($length = 24, $strong = false) {
			if(function_exists('openssl_random_pseudo_bytes')) {
				$token = base64_encode(openssl_random_pseudo_bytes($length, $strong));
				if($strong) return strtr(substr($token, 0, $length), '+/=', '-_,');
			}
			$characters = '0123456789';
			$characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz/+';
			$charactersLength = strlen($characters) - 1;
			$token = '';
			for($i = 0; $i < $length; $i++) $token .= $characters[mt_rand(0, $charactersLength)];

			return $token;
		}

		/**
		 * Validate input field
		 * @param $key
		 * @param $value
		 * @return bool
		 */
		public static function checkFieldValue($key, $value) {
			if($key == 'CSRFToken') {
				return self::csrfguard_validate_token($_REQUEST['CSRFName'], $value);
			}
			$rules = array('lastname' => "/^([ \x{00c0}-\x{01ff}a-zA-Z\'\-]{2,20})+$/u", 'firstname' => "/^([ \x{00c0}-\x{01ff}a-zA-Z\'\-]{2,20})+$/u", 'email' => '/^(?:[\w\d-]+\.?)+\@(?:(?:[\w\d]\-?)+\.)+\w{2,4}$/', 'password' => '/^(.){8,30}$/', 'cpassword' => '/^(.){8,30}$/', 'user' => '/^([A-Za-z\.0-9]{4,16})$/', 'message' => '/^(.){10,1000}$/', 'referrer');
			$v1 = mb_convert_encoding($value, "UTF-8", "auto");
			if(array_key_exists($key, $rules) && (!preg_match($rules[$key], $v1) || strip_tags($value) != $v1)) return false;
			return true;
		}

		/**
		 * Validate CSRF Token
		 * @param $unique_form_name
		 * @param $token_value
		 * @return bool
		 */
		public static function csrfguard_validate_token($unique_form_name, $token_value) {
			if(!array_key_exists($unique_form_name, $_SESSION)) return false;
			$token = $_SESSION[$unique_form_name];
			if($token === false) return false;
			elseif(hash_equals($token, $token_value)) $result = true;
			else $result = false;
			unset($_SESSION[$unique_form_name]);
			return $result;
		}

		/**
		 * Send webmaster email
		 * @param string $subject
		 * @param string $message
		 * @param bool $isHtml
		 * @param null $from
		 */
		public static function sendWebmasterEmail($subject = '', $message = '', $isHtml = false, $from = null) {
			self::send_email(_MAIL_ADDRESS_, 'webmaster', $subject, $message, $isHtml, $from);
		}

		/**
		 * Send an email
		 * @param string $email Email recipient
		 * @param string $lastname lastname recipient
		 * @param string $subject Subject message
		 * @param string $message Body message
		 * @param bool $isHtml It's a HTML message
		 * @param null|array $from Sender
		 * @param null|object $att Attachment
		 * @param bool $bcc_self Self BCC
		 */
		public static function send_email($email, $lastname, $subject = '', $message = '', $isHtml = false, $from = null, $att = null, $bcc_self = false) {
			$html = file_get_contents(_TEMPLATE_DIR_ . 'email_template.html');
			$html = str_replace('#title#', $subject, $html);
			if(!$isHtml) $html = str_replace('#content#', nl2br($message), $html);
			else $html = str_replace('#content#', $message, $html);
			$mail = new Mail();
			$mail->addAddress($email, $lastname);
			$mail->setFrom(_MAIL_FROM_, _MAIL_NAME_);
			if(!is_null($from)) {
				$mail->addCustomHeader("Sender", "\"{$from['lastname']}\" <{$from['email']}>");
				$mail->addReplyTo($from['email'], $from['lastname']);
			}
			if($bcc_self) $mail->addBCC(_MAIL_ADDRESS_, _MAIL_NAME_);
			$mail->Subject = '=?UTF-8?B?' . base64_encode(html_entity_decode($subject)) . '?=';
			$mail->msgHTML($html, dirname(__FILE__));
			if(!is_null($att)) $mail->addStringAttachment($att->body, $att->filename, 'base64', $att->mime);
			$mail->send();

		}

		/**
		 * Send activation email
		 * @param $user
		 * @param bool $generate
		 * @return bool
		 */
		public static function sendActivationEmail($user, $generate = true) {
			$code = '';
			$lastname = $user->lastname . ' ' . $user->firstname;
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
				$activationLink = _ADDRESS_ . _FOLDER_URL_ . 'email_confirm.html';
				$message = /** @lang text */
					"<h2>" . __("Welcome") . ", {$user->firstname}!</h2>
				<h3>" . __("Step") . " 1. " . __("Activate your account") . "!</h3>
				<p>" . __("Activate your account") . " {$email} " . __("by clicking this link") . ": <a href=\"{$activationLink}?key={$code}\">{$activationLink}?key={$code}</a></p>";
				self::send_email($email, $lastname, __('Activate your account'), $message, true);
				return true;
			}
			else return false;
		}

		/**
		 * @param $arr
		 * @return string
		 */
		public static function arrayToOptions($arr) {
			$html = '';
			foreach($arr AS $key => $value) {
				$html .= "<option value=\"{$key}\">{$value}</option>" . PHP_EOL;
			}
			return $html;
		}

		/**
		 * @param $fields
		 * @param $values
		 * @param $formId
		 * @param $checkPassword
		 * @param bool $button
		 * @param bool $subscribe
		 * @return string
		 */
		public static function getAccountForm($fields, $values, $formId, $checkPassword, $button = false, $subscribe = true) {
			function fieldValue($field, $values) {
				if(array_key_exists($field, $_POST)) return strip_tags(htmlspecialchars(stripslashes(trim($_POST[$field]))));
				if(array_key_exists($field, $values)) return strip_tags(htmlspecialchars(stripslashes(trim($values[$field]))));
				return false;
			}

			$content = /** @lang text */
				'				<form id="' . $formId . '" method="post" action="#" class="validateform contactform ac-custom ac-checkbox ac-boxfill">
					<div class="row">
						<div class="col-lg-12 col-xs-12 margintop20 field">
							<div class="input input-hoshi">
								<input type="text" name="lastname" id="lastname" class="input__field input__field-hoshi" data-rule="maxlen:2" data-msg="Introduceți cel puțin 2 characters"';
			if(fieldValue('lastname', $values)) $content .= " value=\"" . fieldValue('lastname', $values) . "\"";
			$content .= ' required />
								<label class="input__label input__label-hoshi input__label-hoshi-color-1" for="lastname" data-ex="ex: Popescu">
									<span class="input__label-content input__label-content-hoshi"><i class="fa fa-user"></i> * lastname</span>
								</label>
								<div class="validation">';
			if(array_key_exists('lastname', $fields) && $fields['lastname'] == 1) $content .= __('Enter your lastname');
			$content .= /** @lang text */
				'		</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12 col-xs-12 margintop10 field">
							<div class="input input-hoshi">
								<input type="text" name="firstname" id="firstname" class="input__field input__field-hoshi" data-rule="maxlen:2" data-msg="Introduceți cel puțin 2 characters"';
			if(fieldValue('firstname', $values)) $content .= " value=\"" . fieldValue('firstname', $values) . "\"";
			$content .= ' required />
								<label class="input__label input__label-hoshi input__label-hoshi-color-1" for="firstname" data-ex="ex: Teodor">
									<span class="input__label-content input__label-content-hoshi"><i class="fa fa-user"></i> * firstname</span>
								</label>
								<div class="validation">';
			if(array_key_exists('firstname', $fields) && $fields['firstname'] == 1) $content .= __('Enter your firstname');
			$content .= '			</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12 col-xs-12 margintop10 field">
							<div class="input input-hoshi">
								<input type="email" name="email" id="email" class="input__field input__field-hoshi" data-rule="email" data-msg="Introduceți adresa de email"';
			if(fieldValue('email', $values)) $content .= " value=\"" . fieldValue('email', $values) . "\"";
			$content .= ' pattern="^(?:[\w\d-]+.?)+@(?:(?:[\w\d]-?)+.)+\w{2,4}$" required />
								<label class="input__label input__label-hoshi input__label-hoshi-color-1" for="email" data-ex="ex: popescu.teodor@gmail.com">
									<span class="input__label-content input__label-content-hoshi"><i class="fa fa-envelope-o"></i> * Email</span>
								</label>
								<div class="validation">';
			if(array_key_exists('email', $fields) && $fields['email'] == 1) $content .= __('Enter your email');
			$content .= '			</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-6 col-xs-12 margintop10 field">
							<div class="input input-hoshi">' . PHP_EOL;
			if($checkPassword) {
				$content .= '	    					<input type="password" name="password" class="input__field input__field-hoshi" pattern=".{8,}" data-rule="maxlen:8" data-msg="Introduceți cel puțin 8 characters"';
				if(fieldValue('password', $values)) $content .= " value=\"" . fieldValue('password', $values) . "\"";
				$content .= ' required />
								<label class="input__label input__label-hoshi input__label-hoshi-color-1" for="password" data-ex="minim 8 characters">
									<span class="input__label-content input__label-content-hoshi"><i class="fa fa-eye-slash"></i> * password</span>
								</label>
								<div class="validation">';
				if(array_key_exists('password', $fields) && $fields['password'] == 1) $content .= __('Enter at least 8 characters');
				$content .= '			</div>
							</div>' . PHP_EOL;
			}
			else $content .= '							<input type="password" name="password" class="input__field input__field-hoshi" pattern=".{8,}" />
								<label class="input__label input__label-hoshi input__label-hoshi-color-1" for="password" data-ex="8 characters minimum">
									<span class="input__label-content input__label-content-hoshi">password</span>
								</label>
							</div>' . PHP_EOL;
			$content .= '					</div>
						<div class="col-lg-6 col-xs-12 margintop10 field">
							<div class="input input-hoshi">' . PHP_EOL;
			if($checkPassword) {
				$content .= '	    					<input type="password" name="cpassword" id="cpassword" class="input__field input__field-hoshi" pattern=".{8,}" data-rule="maxlen:8" data-msg="Confirmați password"';
				if(fieldValue('cpassword', $values)) $content .= " value=\"" . fieldValue('cpassword', $values) . "\"";
				$content .= ' required />
								<label class="input__label input__label-hoshi input__label-hoshi-color-1" for="cpassword">
									<span class="input__label-content input__label-content-hoshi"><i class="fa fa-eye-slash"></i> * ' . __('Password confirm') . '</span>
								</label>
								<div class="validation">';
				if(array_key_exists('cpassword', $fields) && $fields['cpassword'] == 1) $content .= __('Password confirm');
				$content .= '		</div>
							</div>' . PHP_EOL;
			}
			else $content .= '							<input type="password" name="cpassword" id="cpassword" class="input__field input__field-hoshi" pattern=".{8,}" />
								<label class="input__label input__label-hoshi input__label-hoshi-color-1" for="cpassword" data-ex="Introduceți din nou password">
									<span class="input__label-content input__label-content-hoshi">Confirmare password</span>
								</label>
							</div>' . PHP_EOL;
			$content .= '						</div>
						<div class="col-lg-12"><em>' . __('Fields marked with') . ' * (' . __('except for password') . ') . ' . __('are required') . '.</em></div>.
						</div>
						<div class="row margintop10">' . PHP_EOL;
			/**************************************************************************************/
			if($button) {
				$content .= /** @lang text */
					'							<div class="col-lg-8">
								<div class="row">
									<div class="col-lg-12 field checkbox">
										<input type="checkbox" name="termsConditions" id="termsConditions" /><label for="termsConditions">Am citit și sunt de acord cu <a href="/termeni-conditii.html" target="_blank">Termenii și condițiile</a></label>
										<div class="validation">';
				if(array_key_exists('termsConditions', $fields) && $fields['termsConditions'] == 1) $content .= 'Trebuie să fiți de acord cu Termenii și condițiile';
				/** @var bool $subscribe */
				$content .= /** @lang text */
					'		</div>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-12 field checkbox">
										<input type="checkbox" name="subscribe" id="subscribe"' . ($subscribe ? ' checked' : '') . ' /><label for="subscribe">Doresc să mă abonez la newsletter</label>
									</div>
								</div>
							</div>
							<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12"><button type="submit" class="btn btn-theme floatright" id="inregistrare"><i class="fa fa-user-plus"></i> ' . $button . '</button></div>
						</div>' . PHP_EOL;
			}
			else {
				$content .= /** @lang text */
					'							<div class="col-lg-6 col-xs-12 margintop10 field checkbox">
								<input type="checkbox" name="subscribe" id="subscribe"' . ($subscribe ? ' checked' : '') . ' /><label for="subscribe">Doresc să mă abonez la newsletter</label>
							</div>
						</div>' . PHP_EOL;
			}
			$content .= '				</form>';
			return $content;
		}

		/**
		 * Login user
		 * @param $user
		 * @param bool $keepLoggedIn
		 */
		public static function login($user, $keepLoggedIn = false) {
			$_SESSION['user'] = $user;
			if($keepLoggedIn) self::storeCookie($user);
		}

		/**
		 * Cookie generator for keep logged in
		 * @param $user
		 */
		public static function storeCookie($user) {
			$secretKey = self::getSetting('SECRET_KEY');
			$token = self::GenerateRandomToken(24, true);
			self::storeTokenForUser($user, $token);
			$cookie = $token;
			$mac = hash_hmac('sha256', $cookie, $secretKey);
			$cookie .= ':' . $mac;
			\setcookie('rme' . _APP_NAME_, $cookie, time() + 60 * 60 * 24 * 30);
		}

		/**
		 * Get application setting
		 * @param $key
		 * @return bool|float|int|null|string
		 */
		public static function getSetting($key) {
			$setting = new Model('settings');
			$setting = $setting->getOneResult('setting', $key);
			return (property_exists($setting, 'value')) ? $setting->value : false;
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
			session_name(_APP_NAME_ . 'Session');
			if(!isset($_SESSION)) session_start();
			if(array_key_exists('site', $_SESSION) && array_key_exists('user', $_SESSION)) {
				$cookie = new Model('cookies');
				$cookie->__set('user', $_SESSION['user'])->delete();
				unset($_SESSION);
			}
			header("Location: " . _FOLDER_URL_);
			exit;
		}

		/**
		 * Translate a string
		 * @param $string
		 * @param $lang
		 * @return mixed
		 */
		public static function translate($string, $lang) {
			$texts = new Model('translations');
			$texts->where(array('language' => $lang, 'strings.text' => $string));
			$texts = $texts->get();
			return (count($texts)?$texts[0]->translation:$string);
		}

		/**
		 * Check image / pdf uploaded
		 * @param $name
		 * @return array
		 */
		public function checkUploaded($name) {
			$allowedTypes = array('jpg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'pdf' => 'application/pdf');
			if(!isset($_FILES[$name]['error']) || is_array($_FILES[$name]['error'])) return array('ok' => 0, 'error' => 'File has errors');
			switch($_FILES[$name]['error']) {
				case UPLOAD_ERR_OK:
					break;
				case UPLOAD_ERR_NO_FILE:
					return array('ok' => 0, 'error' => 'No file uploaded');
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					return array('ok' => 0, 'error' => 'File is too big (max 2MB)');
				default:
					return array('ok' => 0, 'error' => 'File has errors');
			}
			if($_FILES[$name]['size'] > 16777200) return array('ok' => 0, 'error' => 'File is too big (max 2MB)');
			$file_info = new \finfo(FILEINFO_MIME_TYPE);
			if(false === $ext = array_search($file_info->file($_FILES[$name]['tmp_name']), $allowedTypes, true)) return array('ok' => 0, 'error' => 'File not supported (supported extensions: ' . implode(', ', array_keys($allowedTypes)) . ')');
			$filename = sprintf('%s.%s', sha1_file($_FILES[$name]['tmp_name']) . '-' . $name, $ext);
			$contents = file_get_contents($_FILES[$name]['tmp_name']);
			if(empty($contents)) return array('ok' => 0, 'error' => 'File without content');

			return array('ok' => 1, 'filename' => $filename, 'contents' => $contents);
		}
	}

	/**
	 * @return bool|\Memcached
	 */
	function getCache() {
		//TODO: implement cache system
		$cache = false;
		return $cache;
	}
}
namespace {
	//Uncomment these lines if you want to redirect user to https from http and / or with www. prefix
	/*$protocol = (@$_SERVER["HTTPS"] == "on")?"https://":"http://";
	if (isset($_SERVER['HTTP_HOST']) && substr($_SERVER['HTTP_HOST'], 0, 4) !== 'www.' && substr($_SERVER['HTTP_HOST'], 0, 4) !== 'cne.') {
		header('Location: ' . $protocol . 'www.' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['REQUEST_URI']);
		exit;
	}*/
	use \Utils\Util;
	//If site is currently under maintenance and user IP is not in the decoded json from allowedIps setting in DB tell user to come back later
	if(MAINTENANCE) {
		$ok = false;
		$userIp = Util::getUserIP();
		$allowedIps = json_decode(Util::getSetting('allowedIps'), true);
		foreach($allowedIps AS $allowedIp) {
			if(strpos($userIp, $allowedIp) !== false) $ok = true;
		}
		if(!$ok) {
			header(($_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.1' ? 'HTTP/1.1' : 'HTTP/1.0') . ' 503 Service Unavailable', true, 503);
			header("Retry-After: 3600");
			echo '<h1 style="margin:100px auto;text-align:center;">' . __('This site is currently down for maintenance and should be back soon.') . '</h1>';
			exit;
		}
	}

	//Autoload classes (PSR-0)
	if(version_compare(PHP_VERSION, '5.3.0', '>=')) spl_autoload_register('\Utils\Util::register', true, true);
	else spl_autoload_register('\Utils\Util::register');
	//Start user session
	session_name(_APP_NAME_ . 'Session');
	if(!isset($_SESSION)) session_start();
	if(isset($_SERVER['HTTP_COOKIE'])) {
		setcookie('PHPSESSID', '', time() - 1000);
		$_COOKIE["PHPSESSID"] = null;
		unset($_COOKIE["PHPSESSID"]);
	}
	if(php_sapi_name() != "cli" && strpos($_SERVER['REQUEST_URI'], '/admin') === false && !array_key_exists('admin', $_SESSION) && isset($_COOKIE['rme' . _APP_NAME_]) && !array_key_exists('user', $_SESSION)) {
		$secretKey = Util::getSetting('SECRET_KEY');
		$cookie = $_COOKIE['rme' . _APP_NAME_];
		list ($token, $mac) = explode(':', $cookie);
		if($mac === hash_hmac('sha256', $token, $secretKey)) {
			$user = Util::getUserFromToken($token);
			if($user) $_SESSION['user'] = $user;
		}
	}

	/**
	 * Translate a string
	 * @param $string
	 * @param bool $lang
	 * @return mixed
	 */
	function __($string, $lang = false) {
		if(!$lang) $lang = Util::getUserSetting('language');
		if(!$lang) return $string;
		return call_user_func_array('\Utils\Util::translate', array($string, $lang));
	}
	if(array_key_exists('logout', $_GET)) Util::logout();
}