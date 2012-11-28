<?php
class MemcacheHelper
{
    public static function connect(){
        static $mem = null;
        if (isset($mem)){
            return $mem;
        }
        $port = MEM_PORT;
        $host = MEM_HOST;
        $mem = memcache_connect($host, $port);
        return $mem;
    }
    
    public static function add($key, $value, $flag = 0, $expire = 0){
        $mem = self::connect();
        return $mem->add($key, $value, $flag, $expire);
        
    }
    
    public static function get($key){
        $mem = self::connect();
        return $mem->get($key);
    }
    
    public static function set($key, $value, $flag = 0, $expire = 0){
        $mem = self::connect();
        return $mem->set($key, $value, $flag, $expire);
    }
    
    public static function getList($keyArr){
        if (!is_array($keyArr) || empty($keyArr)){
            return array();
        }
        $mem = self::connect();
        return $mem->get($keyArr);
    }
    
}
?>