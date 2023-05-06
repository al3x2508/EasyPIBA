<?php

use Controller\AdminPage;
use Model\Model;
use Controller\AdminController;

require_once(dirname(__FILE__, 2).'/Utils/functions.php');

$message = '';
if (arrayKeyExists('username', $_POST) && arrayKeyExists('password', $_POST)) {
    $checkAuth = AdminController::checkAuth(
        $_POST['username'],
        $_POST['password']
    );
    if (is_array($checkAuth)) {
        $message = $checkAuth["message"];
    }
}
if (isset($_GET['logout'])) {
    unset($_SESSION);
    session_destroy();
    header('Location: '.$_ENV['FOLDER_URL'].basename(dirname(__FILE__)).'/');
    exit;
}
if (!AdminController::getCurrentUser() && empty($message)) {
    $message = __('Admin login');
}
if (AdminController::getCurrentUser()) {
    $filename = trim(ltrim($page_url, basename(dirname(__FILE__))), '/');
    if (strpos($filename, 'json/') === 0) {
        $filename = str_replace('json/', '', $filename);
        $class = 'Module\\'.$filename.'\\Admin\\JSON';
        $class = new $class();
        $class->get();
    } elseif (strpos($filename, 'act/') === 0) {
        $filename = str_replace('act/', '', $filename);
        $id = false;
        if (strpos($filename, '/') !== false) {
            list($filename, $id) = explode("/", $filename);
        }
        $class = 'Module\\'.$filename.'\\Admin\\Act';
        $class = new $class($id);
        echo json_encode($class->response());
        exit;
    } else {
        $admin = AdminController::getCurrentUser();
        require_once(dirname(__FILE__).'/Template.php');
        $templateFile = (isset($templateFile)) ? $templateFile
            : 'template.html';
        $template = new Template($templateFile, $admin->name, $filename);
        $content = '';
        $adminPage = AdminPage::getCurrentModule($filename);
        if ($adminPage) {
            $template->page = $adminPage->output($filename);
        } elseif ($filename != '' && $filename != basename(dirname(__FILE__))) {
            header('Location: '.$_ENV['FOLDER_URL']
                .basename(dirname(__FILE__)));
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
            $content = file_get_contents(dirname(__FILE__)
                .'/dashboard.html');
            foreach ($contentValues as $key => $value) {
                $content = str_replace("{".$key."}", $value, $content);
            }
            $page = new \stdClass();
            $page->title = __('Statistics');
            $page->h1 = __('Statistics');
            $page->js = array();
            $page->css = array();
            $page->content = $content;
            $template->page = $page;
        }
        $template->output();
    }
} else {
    require_once(dirname(__FILE__).'/Template.php');
    $template = new Template('login.html', '', '');
    $template->message = $message;
    $template->page = '';
    $template->output();
}
