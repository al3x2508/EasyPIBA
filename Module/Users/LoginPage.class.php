<?php

namespace Module\Users;

use Model\Model;
use Utils\Bcrypt;
use Utils\Util;

class LoginPage
{
    public $mustBeLoggedIn = false;
    public $title = false;
    public $content = false;
    public $description = '';
    public $ogimage = '';
    public $h1 = '';
    public $breadcrumbs = array();
    public $js = array('login.js', 'validate.min.js');
    public $css = array();
    public $visible = true;
    public $disableAmp = true;
    public $gradient = ' d-gradient';

    public function __construct()
    {
        $this->content = '';
        $login_errors = array();
        $register_errors = '';
        //Check login
        if (arrayKeyExists('email', $_POST) && arrayKeyExists('password', $_POST) && !arrayKeyExists('confirmPassword',
                $_POST) && arrayKeyExists('CSRFName', $_POST) && arrayKeyExists('CSRFToken', $_POST)
        ) {
            if (Util::csrfguard_validate_token($_POST['CSRFName'], $_POST['CSRFToken'])) {
                $bcrypt = new Bcrypt(10);
                $user = new Model('users');
                $user = $user->getOneResult('email', strip_tags($_POST['email']));
                if ($user) {
                    $userId = $user->id;
                    $status = $user->status;
                    if ($status != 1) {
                        header('HTTP/1.0 401 Unauthorized');
                        $login_errors[] = ($status == 0)?__('You did not confirmed your account'):__('Your account is blocked');
                    } else {
                        $isGood = $bcrypt->verify(strip_tags($_POST['password']), $user->password);
                        //Successfully logged in
                        if ($isGood) {
                            unset($user->password);
                            $user->last_login = date("Y-m-d H:i:s");
                            $user->update();
                            Controller::logUserActivity('User login');
                            $_SESSION['user'] = $userId;
                            if (arrayKeyExists('remember', $_POST)) {
                                Controller::storeCookie($userId);
                            }
                            $redirectUrl = _DEFAULT_REDIRECT_;
                            $location = (arrayKeyExists('ref', $_SESSION))?$_SESSION['ref']:$redirectUrl;

                            header("Location: {$location}");
                            exit;
                        } else {
                            header('HTTP/1.0 401 Unauthorized');
                            $login_errors[] = __('Incorrect password');
                        }
                    }
                } else {
                    header('HTTP/1.0 401 Unauthorized');
                    $login_errors[] = __('No user registered with these info');
                }
            } else {
                header('HTTP/1.0 401 Unauthorized');
                $login_errors[] = __('Invalid token');
            }
        }
        //Display login / register page
        $fields = array();
        //Check if register form was submited
        if (arrayKeyExists('lastname', $_POST) && arrayKeyExists('email', $_POST)) {
            //Check register fields
            $checkFields = array(
                'lastname',
                'firstname',
                'email',
                'country',
                'password',
                'confirmPassword',
                'termsConditions',
                'CSRFToken'
            );
            foreach ($checkFields AS $field) {
                if (!arrayKeyExists($field, $_POST)) {
                    $fields[$field] = 1;
                } else {
                    if (arrayKeyExists($field, $_POST) && !Util::checkFieldValue($field, $_POST[$field])) {
                        $fields[$field] = 1;
                    }
                }
            }
            if (count($fields) == 0) {
                $email = strtolower(strip_tags($_POST['email']));
                $country = strtolower(strip_tags($_POST['country']));
                $user = new Model('users');
                $user = $user->getOneResult('email', $email);
                if ($user) {
                    $register_errors = __('There is another user registered with this email address');
                } else {
                    $password = strip_tags($_POST['password']);
                    if ($password == strip_tags($_POST['confirmPassword'])) {
                        if ($lastname = @ucwords(strtolower(strip_tags(htmlspecialchars(stripslashes(trim($_POST['lastname']))))),
                            " -")
                        ) {
                            $firstname = ucwords(strtolower(strip_tags(htmlspecialchars(stripslashes(trim($_POST['firstname']))))),
                                " -");
                        } else {
                            $lastname = Util::ucname(strip_tags(htmlspecialchars(stripslashes(trim($_POST['lastname'])))));
                            $firstname = Util::ucname(strip_tags(htmlspecialchars(stripslashes(trim($_POST['firstname'])))));
                        }
                        try {
                            $bcrypt = new Bcrypt(10);
                        } catch (\Exception $e) {
                        }
                        $user = new Model('users');
                        $user->lastname = $lastname;
                        $user->firstname = $firstname;
                        $user->email = $email;
                        $user->country = $country;
                        $user->status = 0;
                        $user->password = $bcrypt->hash($password);
                        $user->newsletter = (arrayKeyExists('subscribe', $_POST))?1:0;
                        $user = $user->create();

                        if (is_array($user) && isset($user['error'])) {
                            $register_errors = __($user['error']);
                        } else {
                            Controller::sendActivationEmail($user);
                            $this->content .= /** @lang text */
                                '<script>
                                ga(\'send\', \'event\', \'aquisition\', \'register_account\', \'Register account\', \'2\');
                            	setTimeout("location.href = \'/email_confirm\';",1000);
                        </script>
                        ' . __('You will be redirected to confirm your email in 1 second');
                        }

                    } else {
                        $register_errors = __('Password not confirmed');
                        $fields['confirmPassword'] = 1;
                    }
                }
            } else {
                $register_errors = __('You did not filled the following inputs') . ":\n";
                foreach ($fields AS $key => $value) {
                    switch ($key) {
                        case 'lastname':
                            $register_errors .= '<br />' . __('lastname');
                            break;
                        case 'firstname':
                            $register_errors .= '<br />' . __('firstname');
                            break;
                        case 'email':
                            $register_errors .= '<br />' . __('email address');
                            break;
                        case 'country':
                            $register_errors .= '<br />' . __('country');
                            break;
                        case 'password':
                            $register_errors .= '<br />' . __('password');
                            break;
                        case 'confirmPassword':
                            $register_errors .= '<br />' . __('password confirm');
                            break;
                        case 'termsConditions':
                            $register_errors .= '<br />' . __('terms and conditions');
                            break;
                    }
                }
            }
        }
        $emailValue = (arrayKeyExists('email', $_POST))?' value="' . $_POST['email'] . '"':'';
        $this->content .= '<div class="container">
			<div class="row justify-content-md-center my-4">			
				<div class="col-md-6">
					<div class="panel panel-login">
						<div class="panel-heading container">
							<div class="row">
								<div class="col-6">
								<a href="#" class="active" id="login-form-link">' . __('Login') . '</a>
							</div>
							<div class="col-6">
								<a href="#" id="register-form-link">' . __('Register') . '</a>
							</div>
							</div>
						</div>
						<div class="panel-body container">
							<div class="row">
								<div class="col-12">
									<form id="login-form" action="#" method="post" style="display: block;" class="validateform">';
        if (count($login_errors)) {
            $this->content .= '<div class="col-12"><div class="alert alert-danger">' . implode("\n",
                    $login_errors) . '</div></div>' . PHP_EOL;
        }
        $this->content .= '						<div class="col-12 mt-5 field form-group">
											<div class="input-group">
												<input type="email" name="email" id="email-login" class="form-control"' . $emailValue . ' data-rule="maxlen:2" data-msg="' . __('Enter your email') . '" pattern="^(?:[\w\d-]+.?)+@(?:(?:[\w\d]-?)+.)+\w{2,4}$" required />
												<label class="control-label" for="email-login" data-ex="eg: john.smith@yahoo.com">
													<i class="fas fa-envelope"></i> ' . __('Email') . '
												</label>
												<i class="bar"></i>
											</div>
										</div>
										<div class="col-12 mt-5 field form-group">
											<div class="input-group">
												<input type="password" name="password" id="password-login" class="form-control" data-rule="maxlen:8" pattern=".{8,}" required />
												<label class="control-label" for="password-login">
													<i class="fas fa-eye-slash"></i> * ' . __('Password') . '
												</label>
												<i class="bar"></i>
											</div>
										</div>
										<div class="col-12 mt-5 field form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" name="remember" id="remember" />
                                                <label for="remember" class="custom-control-label"> ' . __('Remember me') . '</label>
                                            </div>
                                        </div>
										<div class="col-12 mb-3">
                                            <div class="d-flex justify-content-sm-center">
                                                <div class="col-sm-6">
													<input type="submit" class="form-control btn btn-outline-primary" value="' . __('Log in') . '" />
												</div>
											</div>
										</div>
										<div class="col-12 field form-group">
											<div class="row">
												<div class="col-12">
													<div class="text-center">
														<a href="' . _ADDRESS_ . _FOLDER_URL_ . 'password_reset" class="forgot-password">' . __('Forgot Password?') . '</a> | 
														<a href="#" class="forgot-password" id="resend">' . __('Resend email confirmation') . '</a>
													</div>
												</div>
											</div>
										</div>
									</form>';
        $this->content .= Controller::getAccountForm($fields, array(), 'register-form', __('Register'),
                $register_errors) . '
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>';
        $this->title = __('Login | ' . _APP_NAME_);
        $this->description = __('Login | ' . _APP_NAME_);
        return $this;
    }
}