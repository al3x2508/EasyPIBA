<?php
require_once(dirname(dirname(__FILE__)) . '/Utils/functions.php');
$message = '';
if(array_key_exists('username',$_POST) && array_key_exists('password',$_POST)) {
	$checkAuth = Controller\AdminController::checkAuth($_POST['username'], $_POST['password']);
	if(is_array($checkAuth)) $message = $checkAuth["mesaj"];
}
if(isset($_GET['logout'])) {
	unset($_SESSION);
	session_destroy();
	header('Location: /admin/');
	exit;
}
if(!Controller\AdminController::getCurrentUser() && empty($message)) $message = __('Admin login');
if (Controller\AdminController::getCurrentUser()) {
	$found = false;
	$filename = str_replace(array(basename(dirname(__FILE__)) . DIRECTORY_SEPARATOR),'',trim($page_url,'/'));
	$admin = Controller\AdminController::getCurrentUser();
	require_once(dirname(__FILE__) . '/Template.class.php');
	$templateFile = (isset($templateFile))?$templateFile:'template.html';
	$template = new Template($templateFile, $admin->name);
	$pageName = __('Admin area');
	$js = array();
	$css = array();
	$content = '';
	require_once(dirname(__FILE__) . '/AdminMenu.php');
	if($filename != '' && file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . $filename . '.php')) {
		$perms = AdminMenu::getLinks(true);
		if(in_array($filename, $perms)) require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . $filename . '.php');
		else {
			header('Location: /admin/');
			exit(0);
		}
	}
	else {
		$contentValues = array();
		$users = new \Model\Model('users');
		$contentValues["total_users"] = $users->countItems();
		$users->stare = 1;
		$contentValues["confirmed_users"] = $users->countItems();
		$content = file_get_contents(dirname(__FILE__) . '/dashboard.html');
		foreach ($contentValues as $key => $value) $content = str_replace("{".$key."}", $value, $content);
		$js = array('plugins/chartjs/Chart.min.js', 'plugins/flot/jquery.flot.min.js', 'plugins/flot/jquery.flot.resize.min.js', 'plugins/flot/jquery.flot.categories.min.js', 'js/stats.js');
	}
	$template->seteaza("titlu", $numePagina);
	$template->js($js);
	$template->css($css);
	$template->seteaza("continut", $content);
	$template->h1($numePagina);
	$template->output();
}
else { ?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>MyKoolio Admin | Log in</title>
		<!-- Tell the browser to be responsive to screen width -->
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
		<!-- Bootstrap 3.3.5 -->
		<link rel="stylesheet" href="/css/bootstrap.min.css">
		<!-- Font Awesome -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
		<!-- Ionicons -->
		<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
		<!-- Theme style -->
		<link rel="stylesheet" href="dist/css/AdminLTE.min.css">
		<!-- iCheck -->
		<link rel="stylesheet" href="plugins/iCheck/square/blue.css">

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
			<a href="/">My<b>Koolio</b></a>
		</div>
		<!-- /.login-logo -->
		<div class="login-box-body">
			<p class="login-box-msg"><?=$message;?></p>

			<form action="" method="post">
				<div class="form-group has-feedback">
					<input type="text" name="username" class="form-control" placeholder="Utilizator" />
					<span class="glyphicon glyphicon-user form-control-feedback"></span>
				</div>
				<div class="form-group has-feedback">
					<input type="password" name="password" class="form-control" placeholder="Parola" />
					<span class="glyphicon glyphicon-lock form-control-feedback"></span>
				</div>
				<div class="row">
					<!-- /.col -->
					<div class="col-xs-12">
						<button type="submit" class="btn btn-primary btn-block btn-flat">Intră</button>
					</div>
					<!-- /.col -->
				</div>
			</form>
		</div>
		<!-- /.login-box-body -->
	</div>
	<!-- /.login-box -->

	<!-- jQuery 2.1.4 -->
	<script type="text/javascript" src="/js/jquery.js"></script>
	<!-- Bootstrap 3.3.5 -->
	<script src="/js/bootstrap.js"></script>
	<!-- iCheck -->
	<script src="plugins/iCheck/icheck.min.js"></script>
	<script>
		$(function () {
			$('input').iCheck({
				checkboxClass: 'icheckbox_square-blue',
				radioClass: 'iradio_square-blue',
				increaseArea: '20%' // optional
			});
		});
	</script>
	</body>
</html>
<?php
}
?>