<?php
include './required.php';
session_start();
$uid = User_Login_Index::getInstance()->islogin();
if ($uid <= 0){
    header('Location: /login.php');
    exit;
}
echo 'hello! welcome to xiyou world! have fun!';
?>