<?php
use Controller\AdminController;
use Model\Model;
$stime = microtime();
$stime = explode(' ', $stime);
$stime = $stime[1] + $stime[0];
require_once(dirname(dirname(__FILE__)) . '/Utils/functions.php');
$message = [];
if (arrayKeyExists('username', $_POST) && arrayKeyExists('password', $_POST)) {
    $message = AdminController::checkAuth($_POST['username'], $_POST['password']);
}
if (isset($_GET['logout'])) {
    unset($_SESSION);
    session_destroy();
    header('Location: ' . _FOLDER_URL_ . basename(dirname(__FILE__)) . '/');
    exit;
}
if (!AdminController::getCurrentUser() && !count($message)) {
    $message = ['message' => __('Admin login'), 'class' => ''];
}
if (AdminController::getCurrentUser() || \Module\Users\Controller::getCurrentUser()) {
    $filename = trim(ltrim($page_url, basename(dirname(__FILE__))), '/');
    if(!empty($filename) && strpos($filename, 'json/') === 0) {
        $filename = str_replace('json/', '', $filename);
        $class = 'Module\\' . $filename . '\\Admin\\JSON';
        $class = new $class();
        $class->get();
    }
    elseif(strpos($filename, 'act/') === 0) {
        $filename = str_replace('act/', '', $filename);
        $id = false;
        if(strpos($filename, '/') !== false) list($filename, $id) = explode("/", $filename);
        $class = 'Module\\' . $filename . '\\Admin\\Act';
        $class = new $class($id);
    } else {
        $admin = AdminController::getCurrentUser();
        require_once(dirname(__FILE__) . '/Template.class.php');
        $templateFile = (isset($templateFile))?$templateFile:'template.html';
        $template = new Template($templateFile, $admin->name, $filename);
        $content = '';
        $adminPage = \Controller\AdminPage::getCurrentModule($filename);
        if ($adminPage) {
            $adminPageOutput = $adminPage->output($filename);
            $template->page = $adminPageOutput;
        } else {
            if ($filename != '' && $filename != basename(dirname(__FILE__))) {
                header('Location: ' . _FOLDER_URL_ . basename(dirname(__FILE__)));
                exit(0);
            } else {
                $contentValues = array();
                $users = new Model('users');
                $contentValues["s_users"] = __('Users');
                $contentValues["total_users"] = $users->countItems();
                $contentValues["s_total_users"] = __('total users');
                $users->status = 1;
                $contentValues["confirmed_users"] = $users->countItems();
                $contentValues["s_confirmed_users"] = __('confirmed users');
                $content = file_get_contents(dirname(__FILE__) . '/dashboard.html');
                foreach ($contentValues as $key => $value) {
                    $content = str_replace("{" . $key . "}", $value, $content);
                }
                $page = new \stdClass();
                $page->title = __('Statistics');
                $page->h1 = __('Statistics');
                $page->js = array();
                $page->css = array();
                $page->content = $content;
                $template->page = $page;
            }
        }
        $template->output($enqScripts, $enqStyles, $stime);
    }
} else { ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?php echo _APP_NAME_; ?> Admin | <?php echo __('Login'); ?></title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="stylesheet" href="<?php echo _FOLDER_URL_; ?>css/main.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="<?php echo _FOLDER_URL_; ?>vendor/almasaeed2010/adminlte/dist/css/AdminLTE.min.css">
        <link rel="stylesheet" href="<?php echo _FOLDER_URL_; ?>vendor/twbs/bootstrap/dist/css/bootstrap.min.css">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <style>
            .d-gradient {
                position: relative;
                margin: 0;
                background-color: #342069;
                transition: all 0.5s;
                -webkit-transition: all 0.5s;
            }
            .d-gradient:after {
                background-color: rgba(28, 234, 222, 0.8);
                background: linear-gradient(-45deg, rgba(187, 26, 222, 0.8) 0%, rgba(28, 234, 222, 0.8) 100%);
                position: absolute;
                width: 100%;
                height: 100%;
                display: block;
                content: "";
                top: 0;
                left: 0;
                z-index: 0;
            }
            .login-box {
                z-index: 1;
                border: 1px solid rgba(255, 255, 255, .4);
                -webkit-border-radius: 10px;
                -moz-border-radius: 10px;
                border-radius: 10px;
            }
            .login-box-body {
                background: transparent;
                color: #f8f8f8;
            }
            .login-logo {
                margin-bottom: 0;
                -webkit-border-radius: 10px 10px 0 0;
                -moz-border-radius: 10px 10px 0 0;
                border-radius: 10px 10px 0 0;
            }
            .login-logo a {
                color: #f8f8f8;
            }
            .login-logo a:hover {
                text-decoration: none;
            }
            .login-box-body {
                -webkit-border-radius: 0 0 10px 10px;
                -moz-border-radius: 0 0 10px 10px;
                border-radius: 0 0 10px 10px;
            }
            .login-box-body input:hover,
            .login-box-body input:focus {
                outline: none;
                -webkit-box-shadow: none;
                -moz-box-shadow: none;
                box-shadow: none;
                border-color: #ccc;
            }
            .d-gradient .login-box-body input:hover,
            .d-gradient .login-box-body input:focus {
                border-color: rgba(255, 255, 255, .6);
            }
        </style>
    </head>
    <body>
        <div class="vh-100 d-flex align-items-center col justify-content-center d-gradient">
            <div class="login-box">
                <div class="login-logo">
                    <a href="/"><?php echo _APP_NAME_; ?></a>
                </div>
                <!-- /.login-logo -->
                <div class="login-box-body">
                    <p class="login-box-msg pb-0 mb-3<?php if(!arrayKeyExists('class', $message)) echo " alert-warning" ?>"><?= $message['message']; ?></p>

                    <form action="" method="post">
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" name="username" id="username" class="form-control" required />
                                <label for="username" class="control-label"><?php echo __('Username'); ?></label>
                                <i class="bar"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control" required />
                                <label for="password" class="control-label"><?php echo __('Password'); ?></label>
                                <i class="bar"></i>
                            </div>
                        </div>
                        <div class="d-flex justify-content-sm-center">
                            <div class="col-sm-6">
                                <input type="submit" class="form-control btn btn-outline-primary" value="<?php echo __('Login'); ?>" />
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /.login-box-body -->
            </div>
            <!-- /.login-box -->
        </div>
    <script src="<?php echo _FOLDER_URL_; ?>js/jquery.min.js"></script>
    <script src="<?php echo _FOLDER_URL_; ?>js/tether.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="<?php echo _FOLDER_URL_; ?>vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>
    <script>
        $(function () {
            $("#username").focus();
        });
    </script>
    </body>
    </html>
    <?php
}