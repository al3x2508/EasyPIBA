<?php
session_start();
if(!isset($_SESSION['admin'])) {
    exit;
}

session_destroy();
header("Location: imgbrowser.php");
