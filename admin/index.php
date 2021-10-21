<?php

use Controller\Act;
use Controller\AdminController;
use Controller\AdminPage;
use Controller\JSON;
use Model\Model;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

$start_time = microtime();
$start_time = explode(' ', $start_time);
$start_time = $start_time[1] + $start_time[0];
require_once(dirname(__FILE__, 2) . '/Utils/Util.php');
$message = ['message' => '', 'class' => ''];
if (arrayKeyExists('username', $_POST) && arrayKeyExists('password', $_POST)) {
    $checkAuth = AdminController::checkAuth($_POST['username'], $_POST['password']);
    if (!is_int($checkAuth)) {
        $message = $checkAuth;
    }
    $message['class'] = ' alert-warning';
}
if (isset($_GET['logout'])) {
    unset($_SESSION);
    session_destroy();
    header('Location: ' . _FOLDER_URL_ . basename(dirname(__FILE__)) . '/');
    exit;
}
if (!AdminController::getCurrentUser() && empty($message['message'])) {
    $message['message'] = __('Admin login');
}
if (AdminController::getCurrentUser()) {
    $filename = trim(ltrim(Util::getCurrentUrl(), basename(dirname(__FILE__))), '/');
    if (!empty($filename) && strpos($filename, 'json/') === 0) {
        $filename = str_replace('json/', '', $filename);
        $class = 'Module\\' . $filename . '\\Admin\\JSON';
        if (class_exists($class)) {
            $class = new $class();
        } else {
            $setupClass = 'Module\\' . $filename . '\\Setup';
            $json_file = _APP_DIR_ . 'Module/' . $filename . '/Admin/admin.json';
            if (file_exists($json_file)) {
                $output = json_decode(file_get_contents($json_file));
                $class = new JSON($setupClass::PERMISSION, $setupClass::ENTITY, $output->_wildcards_ ?? ['name']);
            }
        }
        $class->get();
    } elseif (strpos($filename, 'act/') === 0) {
        $filename = str_replace('act/', '', $filename);
        $id = false;
        if (strpos($filename, '/') !== false) {
            list($filename, $id) = explode("/", $filename);
        }
        $class = 'Module\\' . $filename . '\\Admin\\Act';
        if (class_exists($class)) {
            $class = new $class($id);
        } else {
            $setupClass = 'Module\\' . $filename . '\\Setup';
            $class = new Act($setupClass::PERMISSION, $setupClass::ENTITY, $id);
        }
    } else {
        $admin = AdminController::getCurrentUser();
        require_once(dirname(__FILE__) . '/Template.php');
        $templateFile = (isset($templateFile))?$templateFile:'template.html';
        $template = new Template($templateFile, $admin->name, $filename);
        $content = '';
        $adminPage = AdminPage::getCurrentModule($filename);
        if ($adminPage) {
            $adminPageOutput = $adminPage->output($filename);
            $template->page = $adminPageOutput;
        } elseif ($filename != '' && $filename != basename(dirname(__FILE__))) {
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
            $page = new stdClass();
            $page->title = __('Statistics');
            $page->h1 = __('Statistics');
            $page->js = array();
            $page->css = array();
            $page->content = $content;
            $template->page = $page;
        }
        $template->output($start_time);
    }
} else {
    $twig = new Environment(
        new FilesystemLoader(
            'templates/',
            _APP_DIR_ . 'admin'
        ), [
            'cache' => _APP_DIR_ . 'cache/templates',
        ]
    );
    $twig->addFunction(new TwigFunction('__', '__'));
    try {
        echo $twig->render('login.twig', ['LANGUAGE' => $_SESSION['userLanguage'] ?? 'en', 'message' => $message]);
    } catch (Exception $e) {
        debug('Cannot render admin login: ' . $e->getMessage());
    }
}