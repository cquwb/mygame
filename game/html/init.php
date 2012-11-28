<?php
class Autoloader
{
    /**
     * 这个方法的不好之处就是一个类可能要判断三次file_exists 类的命名上做手脚？
     *
     */
    public static function load($class){
        $systemFile = ROOT . '/system/' . $class . '.php';
        $kernelFile = ROOT . '/kernel/' . $class . '.class.php';
        $moduleFile = ROOT . '/module/' . $class . '.php';
        if (file_exists($systemFile)){
            require $systemFile;
        } elseif (file_exists($kernelFile)){
            require $kernelFile;
        } elseif (file_exists($moduleFile)){
            require $moduleFile;
        } else {
            exit($class . ' not exists');
        }
    }
}
//按照定义的顺序一个一个的调用 如果已经存在了 就跳出这个束缚  这个比__autoload 好 __autoload只能定义一次
spl_autoload_register(array('Autoloader', 'load'));

class SessionHandler
{
    private $savePath;
    private $maxLifeTime = 3600;
    function open()
    {
        return true;
    }

    function close()
    {
        return true;
    }

    function read($id)
    {
        return MemcacheHelper::get($id);
    }

    function write($id, $data)
    {
        return MemcacheHelper::set($id, $data, 0, $this->maxLifeTime);
    }

    function destroy($id)
    {
        MemcacheHelper::del($id);
        return true;
    }

    function gc()
    {
        return true;
    }
}
$handler = new SessionHandler();
session_set_save_handler(
    array($handler, 'open'),
    array($handler, 'close'),
    array($handler, 'read'),
    array($handler, 'write'),
    array($handler, 'destroy'),
    array($handler, 'gc')
);
?>