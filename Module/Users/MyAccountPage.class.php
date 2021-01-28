<?php

namespace Module\Users;

use Model\Model;
use Utils\Bcrypt;
use Utils\Util;

class MyAccountPage
{
    public $mustBeLoggedIn = true;
    public $title = false;
    public $content = false;
    public $description = '';
    public $ogimage = '';
    public $h1 = '';
    public $breadcrumbs = array();
    public $js = array('validate.min.js', 'Module/Users/my-account.js');
    public $css = array('Module/Users/login_my-account.css');
    public $visible = true;
    public $template = '../admin/template.html';
    public $noMainCss = true;
    public $cache = false;

    public function __construct()
    {
        $user = Controller::getCurrentUser(false);
        if ($user) {
            $this->adminName = $user->firstname . ' ' . $user->lastname;
            $this->myaccountlink = '<a href="' . _FOLDER_URL_ . 'my-account" class="btn btn-sm btn-default btn-flat">' . __('My account') . '</a>';
            $this->logoutlink = '<a href="' . _FOLDER_URL_ . 'logout" class="btn btn-sm btn-default btn-flat"><i class="fa fa-power-off"></i> ' . __('Logout') . '</a>';
            $this->ADMIN_FOLDER_URL = _ADMIN_FOLDER_;
            $this->title = __('My account');
            $this->description = __('My account');
            $this->content = '';
            $fields = array();
            $message = '';
            $password = '';
            if (arrayKeyExists('firstname', $_POST)) {
                $checkFields = array('firstname', 'lastname', 'country', 'CSRFToken');
                foreach ($checkFields AS $field) {
                    if (!arrayKeyExists($field, $_POST)) {
                        $fields[$field] = 1;
                    } else {
                        if (arrayKeyExists($field, $_POST) && !Util::checkFieldValue($field, $_POST[$field])) {
                            $fields[$field] = 1;
                        }
                    }
                }
                if (arrayKeyExists('email', $_POST) && !Util::checkFieldValue('email', $_POST['email'])) {
                    $fields['email'] = 1;
                }
                if (count($fields) == 0) {
                    $logout = false;
                    if (arrayKeyExists('email', $_POST)) {
                        $email = strtolower(strip_tags($_POST['email']));
                        $userModel = new Model('users');
                        $userModel = $userModel->getOneResult('email', $email);
                        if (!$userModel || $userModel->id == $user->id) {
                            if ($user->email != strip_tags($_POST['email'])) {
                                $logout = true;
                                $user->email = strip_tags($_POST['email']);
                                $user->status = 0;
                                Controller::sendActivationEmail($user);
                            }
                        } else {
                            $message = __('There is another user registered with this email address');
                        }
                    }
                    if (empty($message)) {
                        if ($lastname = @ucwords(strtolower(strip_tags(htmlspecialchars(stripslashes(trim($_POST['lastname']))))),
                            " -")
                        ) {
                            $firstname = ucwords(strtolower(strip_tags(htmlspecialchars(stripslashes(trim($_POST['firstname']))))),
                                " -");
                        } else {
                            $lastname = Util::ucname(strip_tags(htmlspecialchars(stripslashes(trim($_POST['lastname'])))));
                            $firstname = Util::ucname(strip_tags(htmlspecialchars(stripslashes(trim($_POST['firstname'])))));
                        }
                        $user->lastname = !empty($lastname)?$lastname:$_POST['lastname'];
                        $user->firstname = !empty($firstname)?$firstname:$_POST['firstname'];
                        if (arrayKeyExists('country', $_POST) && $_POST['country'] != 0) {
                            $user->country = $_POST['country'];
                        }
                        $user->newsletter = (arrayKeyExists('subscribe', $_POST))?1:0;
                        $user->settings = json_encode(array());
                        Controller::logUserActivity('Account edited');
                        $user->update();
                        if ($logout) {
                            Controller::logout();
                        }
                    }
                } else {
                    $message = __('You did not filled the following inputs') . ":<br />\n" . implode(", ",
                            array_keys($fields));
                }
            } elseif (arrayKeyExists('password', $_POST)) {
                if (!Util::checkFieldValue('password', $_POST['password'])) {
                    $fields['password'] = 1;
                }
                if (arrayKeyExists('confirmPassword', $_POST) && !Util::checkFieldValue('confirmPassword',
                        $_POST['confirmPassword'])
                ) {
                    $fields['confirmPassword'] = 1;
                }
                $password = strip_tags($_POST['password']);
                $confirmPassword = strip_tags($_POST['confirmPassword']);
                if ($password != $confirmPassword) {
                    $fields['confirmPassword'] = 1;
                    $message = __('Confirm the password');
                }
                if (count($fields) == 0) {
                    try {
                        $bcrypt = new Bcrypt(10);
                        $user->password = $bcrypt->hash($password);
                        Controller::logUserActivity('Password changed');
                        $user->update();
                    } catch (\Exception $e) {
                    }
                    Controller::logout();
                } else {
                    $message = __('You did not filled the following inputs') . ":<br />\n" . implode(", ",
                            array_keys($fields));
                }
            }
            $values = array(
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->email,
                'country' => $user->country,
                'subscribe' => $user->newsletter
            );
            $alert = (!empty($message))?'								<div class="col-12"><div class="alert alert-danger">' . $message . '</div></div>' . PHP_EOL:'';
            $this->content .= '<div class="row justify-content-md-center my-4">
				<div class="col-12 col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3">
					<div class="panel panel-login">
						<div class="panel-heading container"><div class="row"><div class="col-12 text-center"><h2><b><i class="fa fa-user"></i> ' . __('My account') . '</b></h2></div></div><hr /></div>
						<div class="panel-body container">
							<div class="row">
								<div class="col-12">' . PHP_EOL . $alert . '
									<!-- Start viewinfo -->
									<div id="viewinfo"' . (!empty($message)?' style="display:none;"':'') . '>
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
									<!-- End viewinfo -->
									<!-- Start editinfo -->
									<div id="editinfo"' . ((empty($message) || arrayKeyExists('password',
                        $_POST))?' style="display:none;"':'') . '>
										' . Controller::getAccountForm($fields, $values, 'myaccount', false) . '
									</div>
									<div id="editpasswordF"' . ((empty($message) || !arrayKeyExists('password',
                        $_POST))?' style="display:none;"':'') . '>
										<form id="editPwd" method="post" action="#" class="validateform">
											<div class="col-12 mt-5 field form-group">
												<div class="input-group">
													<input type="password" name="password" id="password" class="form-control" data-rule="maxlen:8" data-msg="' . sprintf(__('Enter at least %s characters'),
                    '8') . '" pattern=".{8,}" autocomplete="new-password" required />
													<label class="control-label" for="password" data-ex="' . __('8 characters minimum') . '">
														<i class="fa fa-eye-slash"></i> * ' . __('Password') . '
													</label>
													<i class="bar"></i>
												</div>
											</div>
											<div class="col-12 mt-5 field form-group">
												<div class="input-group">
													<input type="password" name="confirmPassword" id="confirmPassword" class="form-control" data-rule="maxlen:8" data-msg="' . __('Confirm password') . '" pattern=".{8,}" autocomplete="new-password" required />
													<label class="control-label" for="confirmPassword" data-ex="' . __('8 characters minimum') . '">
														<i class="fa fa-eye-slash"></i> * ' . __('Confirm password') . '
													</label>
													<i class="bar"></i>
												</div>
											</div>
										</form>
									</div>
									<!-- End editinfo -->
								</div>
								<div class="col-12 mb-3">
									<div class="d-flex justify-content-sm-center">
										<div class="col-sm-6">
											<button id="edit" class="form-control btn btn-outline-primary" data-action="edit">' . __('Edit') . '</button>
										</div>
										<div class="col-sm-6">
											<button id="editPassword" class="form-control btn btn-password" data-action="password">' . __('Change password') . '</button>
											<button id="delete" class="form-control btn btn-delete d-none" data-toggle="modal" data-target="#confirm_delete">' . __('Delete') . '</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal fade" id="confirm_delete" role="dialog" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title" id="myModalLabel">' . __('Confirm delete') . '</h4>
							<button class="close" data-dismiss="modal" aria-hidden="true">×</button>
							</div>
						<div class="modal-body">
							<p>' . __('You are about to delete your account.') . '</p>
							<p>' . __('Do you want to proceed?') . '</p>
						</div>
						<div class="modal-footer">
							<button class="btn btn-default" data-dismiss="modal">' . __('Cancel') . '</button>
							<button class="btn btn-danger btn-ok">' . __('Delete') . '</button>
						</div>
					</div>
				</div>
			</div>';
        }
        return $this;
    }
}