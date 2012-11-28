<?php
class Autoloader
{
    /**
     * 这个方法的不好之处就是一个类可能要判断三次file_exists 类的命名上做手脚？
     *
     */
    public static function load($class){
        $class_info = explode('_', $class);
        $path_depth = count($class_info); 
        $class_path = CLASS_PATH;
        for ($i = 0; $i < $path_depth; $i++){
            if ($i < $path_depth - 1){
                $class_path .= $class_info[$i] . '/';
            } else {
                $class_path .= $class_info[$i] . '.class.php';
            }
        }
        var_dump($class_path);
        if (!file_exists($class_path)){
            exit($class . ' not exists');
        }
        if (!class_exists($class)){
            include $class_path;
        }
        if (!class_exists($class)){
            exit($class . ' can not load');
        }
    }
}
//按照定义的顺序一个一个的调用 如果已经存在了 就跳出这个束缚  这个比__autoload 好 __autoload只能定义一次
spl_autoload_register(array('Autoloader', 'load'));
?>