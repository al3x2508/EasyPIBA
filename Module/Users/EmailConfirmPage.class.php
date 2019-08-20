<?php
namespace Module\Users;

use Model\Model;
use Utils\Util;

class EmailConfirmPage {
	public $mustBeLoggedIn = false;
	public $title = false;
	public $content = false;
	public $description = '';
	public $ogimage = '';
	public $h1 = '';
	public $breadcrumbs = array();
	public $js = array();
	public $css = array();
	public $visible = true;

	public function __construct() {
		$this->content = '';
		$this->title = __('Email confirm');
		$this->description = __('Email confirm');
		$code = (arrayKeyExists('code', $_REQUEST) && ctype_alnum($_REQUEST['code'])) ? trim($_REQUEST['code']) : '';
		$showForm = false;
		$codeError = ($code && strlen($code) != 32)?'<div class="alert alert-danger">' . __('Enter the confirmation code') . '</div>':'';
		$form = '<form action="#" method="post">
			<div class="alert alert-danger"></div>
			<div class="col-12 mt-5 field form-group">
				<div class="input input-hoshi">
					<input type="text" name="code" id="code" class="input__field input__field-hoshi form-control" data-rule="maxlen:32" data-msg="' . sprintf(__('Enter at least %s characters'), '32') . '" pattern=".{32,32}" required />
					<label class="input__label input__label-hoshi input__label-hoshi-color-1" for="code" data-ex="' . __('32 characters code') . '">
						<span class="input__label-content input__label-content-hoshi"> * ' . __('Confirmation code') . '</span>
					</label>
					' . $codeError . '
				</div>
			</div>
			<div class="col-12 field form-group">
				<div class="row justify-content-sm-center">
					<div class="col-sm-6">
						<input type="submit" class="form-control btn btn-outline-primary" value="' . __('Confirm') . '" />
					</div>
				</div>
			</div>
		</form>' . PHP_EOL;
		$this->content = '<div class="row justify-content-md-center my-4">
			<div class="col-12 col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3">
				<div class="panel panel-login">
					<div class="panel-heading container"><div class="row"><div class="col-12 text-center"><h2><b>' . __('Email confirm') . '</b></h2></div></div><hr /></div>
					<div class="panel-body container">
						<div class="row">
							<div class="col-12">' . PHP_EOL;
		if(!empty($code)) {
			$confirm = new Model('user_confirm');
			$confirm = $confirm->getOneResult('code', $code);
			if($confirm) {
                /** @var Model $user */
                $user = $confirm->users;
                if($user->status != 2) {
                    $confirm->delete();
                    $user->account_type = 1;
                    $user->status = 1;
                    $user->update();
					$this->content .= '<script>ga(\'send\', \'event\', \'aquisition\', \'confirm_account\', \'Confirm account\', \'1\');</script>
				<h1>' . __('Your email has been confirmed') . '</h1>
			' . sprintf(__('You can login now %s'), '<a href="' . _ADDRESS_ . _FOLDER_URL_ . 'login">' . __('here') . '</a>') . PHP_EOL;
				}
				else $this->content .= '<h1> ' . __('Your account is blocked!') . '!</h1>' . PHP_EOL;
			}
			else {
				$showForm = true;
				$form = str_replace('<div class="alert alert-danger"></div>', '<div class="alert alert-danger">' . __('Code is invalid or already confirmed') . '</div>', $form);
			}
		}
		else $showForm = true;
		if($showForm) {
			$form = str_replace('<div class="alert alert-danger"></div>', '', $form);
			$this->content .= $form;
		}
		$this->content .= '</div>
						</div>
					</div>
				</div>
			</div>
		</div>';
		return $this;
	}
}