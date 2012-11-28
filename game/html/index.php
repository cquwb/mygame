<?php
define('ROOT', dirname(dirname(__FILE__)));
require_once './init.php';
require_once ROOT . '/config/define.config.php';
require_once ROOT . '/config/error.config.php';
session_start();
header("Content-Type:text/html; charset=utf-8");
$controller = new Controller();
$controller->exec();
?>