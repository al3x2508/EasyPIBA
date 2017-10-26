<?php
use Model\Model;
use Controller\AdminController;

require_once(dirname(dirname(__FILE__)) . '/Utils/functions.php');
$message = '';
if(array_key_exists('username',$_POST) && array_key_exists('password',$_POST)) {
	$checkAuth = AdminController::checkAuth($_POST['username'], $_POST['password']);
	if(is_array($checkAuth)) $message = $checkAuth["message"];
}
if(isset($_GET['logout'])) {
	unset($_SESSION);
	session_destroy();
	header('Location: ' . _FOLDER_URL_ . basename(dirname(__FILE__)) . '/');
	exit;
}
if(!AdminController::getCurrentUser() && empty($message)) $message = __('Admin login');
if (AdminController::getCurrentUser()) {
	$found = false;
	$filename = str_replace(array(basename(dirname(__FILE__)) . DIRECTORY_SEPARATOR),'',trim($page_url,'/'));
	$admin = AdminController::getCurrentUser();
	require_once(dirname(__FILE__) . '/Template.class.php');
	$templateFile = (isset($templateFile))?$templateFile:'template.html';
	$template = new Template($templateFile, $admin->name);
	$page_title = __('Statistics');
	$js = array();
	$css = array();
	$content = '';
	require_once(dirname(__FILE__) . '/AdminMenu.php');
	$filepath = dirname(__FILE__) . DIRECTORY_SEPARATOR . $filename . '.php';
	if($filename != '' && file_exists($filepath)) {
		$perms = AdminMenu::getLinks(true);
		if(in_array($filename, $perms)) /** @noinspection PhpIncludeInspection */
			require_once($filepath);
		else {
			header('Location: ' . _FOLDER_URL_ . basename(dirname(__FILE__)));
			exit(0);
		}
	}
	else {
		$contentValues = array();
		$users = new Model('users');
		$contentValues["s_users"] = __('Users');
		$contentValues["total_users"] = $users->countItems();
		$contentValues["s_total_users"] = __('total users');
		$users->status = 1;
		$contentValues["confirmed_users"] = $users->countItems();
		$contentValues["s_confirmed_users"] = __('confirmed users');
		$content = file_get_contents(dirname(__FILE__) . '/dashboard.html');
		foreach ($contentValues as $key => $value) $content = str_replace("{".$key."}", $value, $content);
	}
	$template->title = $page_title;
	$template->js = $js;
	$template->css = $css;
	$template->content = $content;
	$template->h1 = $page_title;
	$template->output();
}
else { ?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title><?php echo _APP_NAME_; ?> Admin | <?php echo __('Login'); ?></title>
		<!-- Tell the browser to be responsive to screen width -->
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
		<!-- Bootstrap 3.3.5 -->
		<link rel="stylesheet" href="/css/bootstrap.min.css">
		<!-- Font Awesome -->
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" />
		<!-- Theme style -->
		<link rel="stylesheet" href="dist/css/AdminLTE.min.css">

		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body class="hold-transition login-page">
		<div class="login-box">
			<div class="login-logo">
				<a href="/"><?php echo _APP_NAME_; ?></a>
			</div>
			<!-- /.login-logo -->
			<div class="login-box-body">
				<p class="login-box-msg"><?=$message;?></p>

				<form action="" method="post">
					<div class="form-group has-feedback">
						<input type="text" name="username" class="form-control" placeholder="<?php echo __('Username'); ?>" />
						<span class="glyphicon glyphicon-user form-control-feedback"></span>
					</div>
					<div class="form-group has-feedback">
						<input type="password" name="password" class="form-control" placeholder="<?php echo __('Password'); ?>" />
						<span class="glyphicon glyphicon-lock form-control-feedback"></span>
					</div>
					<div class="row">
						<!-- /.col -->
						<div class="col-lg-6 col-6">
							<button type="submit" class="btn btn-primary btn-block btn-flat"><?php echo __('Login'); ?></button>
						</div>
						<!-- /.col -->
					</div>
				</form>
			</div>
			<!-- /.login-box-body -->
		</div>
		<!-- /.login-box -->
		<script type="text/javascript" src="/js/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
		<!-- Bootstrap -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
	</body>
</html>
<?php
}