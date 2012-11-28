<?php
/**
 * 路由类
 *
 */

class Controller
{
    public $module = '';
    public $action = '';
    public $aid = 0;
    
    public function __construct(){
        $this->module = isset($_GET['m'])?$_GET['m']:'main';
        $this->action = isset($_GET['a'])?$_GET['a']:'index';
    }
    
    public function exec(){
        $isLogin = Avatar::isLogin();
        if (!$isLogin && !($this->module == 'main' && $this->action == 'login')){
            header('Location: http://127.0.0.1/xiyou/html/login.php');
            exit;
        }
        $this->aid = $isLogin;
        $class = ucfirst($this->module);
        $method = $this->action;
        $obj = new $class;
        $obj->$method();
    }
    
    public function err($id){
        require_once ROOT . '/config/text/'. LANG . '/error.text.php';
        if (isset($errorText[$id])){
            $arr = array(
                'status' => $id,
                'content' => $errorText[$id],
            );
        } else {
            $arr = array(
                'status' => -1,
                'content' => $errorText[-1],
            );
        }
        echo json_encode($arr);
        exit;
    }
    
    public function out($arr){
        $arr = array(
            'status' => 1,
            'content' => $arr,
        );
        echo json_encode($arr);
        exit;
    }
    
}
?>