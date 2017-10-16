<?php
$content = '';
$page_title = __('Email confirm');
$description = __('Email confirm');
$h1 = '';
$js = array();
$css = array();
$code = (array_key_exists('code', $_GET) && ctype_alnum($_GET['code'])) ? trim($_GET['code']) : '';
$showForm = false;
$errorForm = '';
$codeError = (strlen($code) != 16)?'<div class="alert alert-danger">' . __('Enter the confirmation code') . '</div>':'';
$form = '<div class="col-lg-12">
			<div class="row">
				<div class="col-lg-12"><h2>' . __('Email confirm') . '</h2></div>
				<form action="#" method="get">
					<div class="alert alert-danger"></div>
					<div class="col-lg-12 col-12 mt-5 field form-group">
						<div class="input input-hoshi">
							<input type="text" name="code" id="code" class="input__field input__field-hoshi form-control" data-rule="maxlen:16" data-msg="' . sprintf(__('Enter at least %s characters'), '16') . '" pattern=".{16,16}" required />
							<label class="input__label input__label-hoshi input__label-hoshi-color-1" for="code" data-ex="' . __('16 characters code') . '">
								<span class="input__label-content input__label-content-hoshi"> * ' . __('Confirmation code') . '</span>
							</label>
							' . $codeError . '
						</div>
					</div>
					<div class="col-lg-12 col-12 field form-group">
						<div class="row justify-content-sm-center">
							<div class="col-sm-6">
								<input type="submit" class="form-control btn btn-login" value="' . __('Send') . '" />
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>' . PHP_EOL;
$content = '<div class="row justify-content-md-center mt-4">
			<div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3">
				<div class="panel panel-login">
					<div class="panel-heading"><div class="row"><div class="col-12 text-center"><h2><b>' . __('Email confirm') . '</b></h2></div></div><hr /></div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-12">' . PHP_EOL;
if(!empty($code)) {
	$confirm = new Model\Model('user_confirm');
	$confirm = $confirm->getOneResult('code', $code);
	if($confirm) {
		$user = $confirm->users;
		if($user->status != 2) {
			$confirm->delete();
			$user->status = 1;
			$user->update();
			$content .= '<h1>' . __('Your email has been confirmed') . '</h1>
			' . sprintf(__('You can login now %s'), '<a href="' . _ADDRESS_ . _FOLDER_URL_ . 'login.html">' . __('here') . '</a>') . PHP_EOL;
		}
		else $content .= '<h1> ' . __('Your account is blocked!') . '!</h1>' . PHP_EOL;
	}
	else {
		$showForm = true;
		$form = str_replace('<div class="alert alert-danger"></div>', '<div class="alert alert-danger">' . __('Code is invalid or already confirmed') . '</div>', $form);
	}
}
else $showForm = true;
if(array_key_exists('cheie', $_GET) && !ctype_alnum($_GET['cheie'])) $content .= 'Cod invalid!<br />' . PHP_EOL;
if($showForm) $content .= $form;
$content .= '</div>
						</div>
					</div>
				</div>
			</div>
		</div>';