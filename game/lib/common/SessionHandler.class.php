<?php
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