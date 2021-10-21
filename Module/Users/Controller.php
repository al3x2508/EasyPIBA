<?php

namespace Module\Users;

use Model\Model;
use Util;

class Controller
{
    /**
     * Account form used for register and my account pages
     * @param $fields
     * @param $values
     * @param $formId
     * @param bool $button
     * @param string $errors
     * @return string
     */
    public static function getAccountForm($fields, $values, $formId, $button = false, $errors = '')
    {
        function fieldValue($field, $values)
        {
            if (arrayKeyExists($field, $_POST)) {
                return trim($_POST[$field]);
            }
            if (arrayKeyExists($field, $values)) {
                return trim($values[$field]);
            }
            return false;
        }

        $countriesOptions = '	<option value="">' . __('Country') . '</option>' . PHP_EOL;
        $countries = new Model('countries');
        $countries->order('`countries`.`name` ASC');
        $countries = $countries->get();
        foreach ($countries as $country) {
            $selected = ($country->id == fieldValue('country', $values)) ? ' selected' : '';
            $countriesOptions .= '	<option value="' . $country->id . '"' . $selected . '>' . $country->name . '</option>' . PHP_EOL;
        }
        $firstname = array(
            'value' => (fieldValue('firstname', $values)) ? ' value="' . fieldValue(
                    'firstname',
                    $values
                ) . '"' : '',
            'validation' => (arrayKeyExists(
                    'firstname',
                    $fields
                ) && $fields['firstname'] == 1) ? '<div class="alert alert-danger">' . __(
                    'Enter your firstname'
                ) . '</div>' : ''
        );
        $lastname = array(
            'value' => (fieldValue('lastname', $values)) ? ' value="' . fieldValue(
                    'lastname',
                    $values
                ) . '"' : '',
            'validation' => (arrayKeyExists(
                    'lastname',
                    $fields
                ) && $fields['lastname'] == 1) ? '<div class="alert alert-danger">' . __(
                    'Enter your lastname'
                ) . '</div>' : ''
        );
        $country = (arrayKeyExists(
                'country',
                $fields
            ) && $fields['country'] == 1) ? '<div class="alert alert-danger">' . __(
                'Select your country'
            ) . '</div>' : '';
        $subscribe = (fieldValue('subscribe', $values)) ? ' checked' : '';
        $content = '<form id="' . $formId . '" method="post" action="#" class="validateform">';
        if (!empty($errors)) {
            $content .= '<div class="col-12"><div class="alert alert-danger">' . $errors . '</div></div>' . PHP_EOL;
        }
        if ($formId == 'myaccount') {
            $emailField = '<div class="col-12 field">' . __('Email') . ': ' . $values['email'] . '</div><span></span>';
            $passwordFields = '';
        } else {
            $email = array(
                'value' => (fieldValue('email', $values)) ? ' value="' . fieldValue(
                        'email',
                        $values
                    ) . '"' : '',
                'validation' => (arrayKeyExists(
                        'email',
                        $fields
                    ) && $fields['email'] == 1) ? '<div class="alert alert-danger">' . __(
                        'Enter your email'
                    ) . '</div>' : ''
            );
            $password = (arrayKeyExists(
                    'password',
                    $fields
                ) && $fields['password'] == 1) ? '<div class="alert alert-danger">' . __(
                    'Enter a password'
                ) . '</div>' : '';
            $confirmPassword = (arrayKeyExists(
                    'confirmPassword',
                    $fields
                ) && $fields['confirmPassword'] == 1) ? '<div class="alert alert-danger">' . __(
                    'Confirm the password'
                ) . '</div>' : '';
            $emailField = '<div class="col-12 mt-5 field form-group">
							<div class="input-group">
								<input type="email" name="email" id="email" class="form-control" data-rule="maxlen:2" data-msg="' . __(
                    'Enter your email'
                ) . '"' . $email['value'] . ' pattern="^(?:[\w\d-]+.?)+@(?:(?:[\w\d]-?)+.)+\w{2,4}$" autocomplete="off" required />
								<label class="control-label" for="email" data-ex="eg: john.smith@yahoo.com">
									<i class="fa fa-envelope"></i> * ' . __('Email') . '
								</label>
								<i class="bar"></i>
								' . $email['validation'] . '
							</div>
						</div>';
            $passwordFields = '<div class="col-12 mt-5 field form-group">
							<div class="input-group">
								<input type="password" name="password" id="password" class="form-control" data-rule="maxlen:8" data-msg="' . sprintf(
                    __('Enter at least %s characters'),
                    '8'
                ) . '" pattern=".{8,}" autocomplete="new-password" required />
								<label class="control-label" for="password" data-ex="' . __('8 characters minimum') . '">
									<i class="fa fa-eye-slash"></i> * ' . __('Password') . '
								</label>
								<i class="bar"></i>
								' . $password . '
							</div>
						</div>
						<div class="col-12 mt-5 field form-group">
							<div class="input-group">
								<input type="password" name="confirmPassword" id="confirmPassword" class="form-control" data-rule="maxlen:8" data-msg="' . __(
                    'Confirm password'
                ) . '" pattern=".{8,}" autocomplete="new-password" required />
								<label class="control-label" for="confirmPassword" data-ex="' . __(
                    '8 characters minimum'
                ) . '">
									<i class="fa fa-eye-slash"></i> * ' . __('Confirm password') . '
								</label>
								<i class="bar"></i>
								' . $confirmPassword . '
							</div>
						</div>';
        }
        $content .= '<div class="col-12 mt-5 field form-group">
							<div class="input-group">
								<input type="text" name="firstname" id="firstname" class="form-control" data-rule="maxlen:2" data-msg="' . sprintf(
                __('Enter at least %s characters'),
                '2'
            ) . '"' . $firstname['value'] . ' pattern=".{2,}" required />
								<label class="control-label" for="firstname" data-ex="eg: John">
									<i class="fa fa-user"></i> * ' . __('Firstname') . '
								</label>
								<i class="bar"></i>
								' . $firstname['validation'] . '
							</div>
						</div>
						<div class="col-12 mt-5 field form-group">
							<div class="input-group">
								<input type="text" name="lastname" id="lastname" class="form-control" data-rule="maxlen:2" data-msg="' . sprintf(
                __('Enter at least %s characters'),
                '2'
            ) . '"' . $lastname['value'] . ' pattern=".{2,}" required />
								<label class="control-label" for="lastname" data-ex="eg: Smith">
									<i class="fa fa-user"></i> * ' . __('Lastname') . '
								</label>
								<i class="bar"></i>
								' . $lastname['validation'] . '
							</div>
						</div>
						' . $emailField . '
						<div class="col-12 mt-5 field form-group">
						    <div class="input-group">
                                <select name="country" id="country" class="form-control select2" required>
                                    ' . $countriesOptions . '
                                </select>
                                <label for="country" class="control-label">' . __('Country') . '</label>
							    ' . $country . '
							</div>
						</div>';
        $content .= $passwordFields . '
						<div class="col-12 mb-0 field form-group">
                            <div class="input-group">
							    <input type="checkbox" name="subscribe" id="subscribe"' . $subscribe . ' data-bootstrap-switch />
							    <label for="subscribe">' . __('Subscribe to newsletter') . '</label>
							</div>
						</div>' . PHP_EOL;
        if ($button) {
            $termsConditions = (arrayKeyExists(
                    'termsConditions',
                    $fields
                ) && $fields['termsConditions'] == 1) ? '<div class="alert alert-danger">' . __(
                    'You must accept our Terms and conditions'
                ) . '</div>' : '';
            $content .= '<div class="col-12 mb-0 field form-group">
                            <div class="input-group">
							    <input type="checkbox" name="termsConditions" id="termsConditions" data-bootstrap-switch />
							    <label for="termsConditions">' . sprintf(
                    __('I have read and agree to the %s'),
                    '<a href="' . _FOLDER_URL_ . 'terms-conditions">' . __(
                        'Terms and Conditions'
                    ) . '</a>'
                ) . '</label>
							    ' . $termsConditions . '
							</div>
						</div>
						<div class="col-12 mb-3">
							<div class="d-flex justify-content-sm-center">
								<div class="col-sm-6">
									<input type="submit" class="form-control btn btn-outline-primary" value="' . __(
                    $button
                ) . '" />
								</div>
							</div>
						</div>' . PHP_EOL;
        }
        $content .= '				    <div class="col-12 form-group"><em>' . __('Fields marked with') . ' * ' . __(
                'are required'
            ) . '.</em></div>
				</form>';
        return $content;
    }

    /**
     * Get user setting
     * @param $setting
     * @param bool $user
     * @return mixed|null|string
     */
    public static function getUserSetting($setting, $user = false)
    {
        if (!$user) {
            $user = self::getCurrentUser(false);
        }
        if ($user) {
            $accountSettings = json_decode($user->settings, true);
            if (is_array($accountSettings) && arrayKeyExists($setting, $accountSettings)) {
                return $accountSettings[$setting];
            }
        }
        return null;
    }

    /**
     * Get logged in user
     * @return Model|bool
     */
    public static function getCurrentUser($returnId = true)
    {
        if (arrayKeyExists('user', $_SESSION)) {
            if ($returnId) {
                return $_SESSION['user'];
            } else {
                $user = new Model('users');
                $user = $user->getOneResult('id', $_SESSION['user']);
                return $user;
            }
        }
        return false;
    }

    /**
     * Get user by cookie token
     * @param $token
     * @return bool|Model
     */
    public static function getUserFromToken($token)
    {
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
    public static function storeResetPassword($user)
    {
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
    public static function sendActivationEmail($user, $generate = true)
    {
        $code = '';
        $name = $user->lastname . ' ' . $user->firstname;
        $email = $user->email;
        $user_confirm = new Model('user_confirm');
        $user_confirm->user = $user->id;
        if ($generate) {
            $code = md5(time());
            $user_confirm->code = $code;
            $user_confirm->create();
        } else {
            $user_confirm = $user_confirm->get();
            if (count($user_confirm) > 0) {
                $code = $user_confirm[0]->code;
            }
        }
        if (!empty($code)) {
            $activationLink = _ADDRESS_ . _FOLDER_URL_ . 'email_confirm';
            $message = /** @lang text */
                "<h2>" . __("Welcome") . ", {$user->firstname}!</h2>
				<h3>" . __("Step") . " 1. " . __("Activate your account") . "!</h3>
				<p>" . __("Activate your account") . " {$email} " . __(
                    "by clicking this link"
                ) . ": <a href=\"{$activationLink}?code={$code}\">{$activationLink}?code={$code}</a></p>";
            Util::send_email($email, $name, __('Activate your account'), $message, true);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Cookie generator for keep logged in
     * @param $user
     */
    public static function storeCookie($user)
    {
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
    private static function storeTokenForUser($user, $token)
    {
        $cookie = new Model('cookies');
        $cookie->user = $user;
        $cookie->token = $token;
        $cookie->expiration_date = date('Y-m-d H:i:s', strtotime("+1 week"));
        $cookie->create();
    }

    public static function deleteUser($userId)
    {
        $user = new Model('users');
        $user = $user->getOneResult('id', $userId);
        return $user->delete();
    }

    /**
     * Logout user
     */
    public static function logout($redirect = true, $exit = true)
    {
        Controller::logUserActivity('User logout');
        if (isset($_SESSION)) {
            if (arrayKeyExists('user', $_SESSION)) {
                $cookie = new Model('cookies');
                $cookie->__set('user', $_SESSION['user'])->delete();
            }
            session_unset();
            session_destroy();
            if (session_status() == PHP_SESSION_ACTIVE) {
                session_regenerate_id(true);
            }
            if ($redirect) {
                header("Location: " . _FOLDER_URL_);
            }
        }
        if ($exit) {
            exit;
        }
    }

    public static function logUserActivity($message, $act = false)
    {
    }
}
