<?php
class DbHelper
{
    public static function connect(){
        static $db = null;
        if (isset($db)){
            return $db;
        }
        $host = DB_HOST;
        $port = DB_PORT;
        $user = DB_USER;
        $password = DB_PASSWORD;
        $database = DB_NAME;
        $mysqli = new mysqli($host, $user, $password, $database, $port);
        if ($mysqli->connect_errno) {
            echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            exit;
        }
        $db = $mysqli;
        return $db;
    }
    
    public static function fetchAll($sql){
        $db = self::connect();
        $res = $db->query($sql);
        $rows = array();
        if ($res){
            while ($row = $res->fetch_assoc()){
                $rows[] = $row;
            }
        }
        return $rows;
    }
    
    public static function fetch($sql){
        $db = self::connect();
        $res = $db->query($sql);
        if ($res){
            while ($row = $res->fetch_assoc()){
                return $row;
            }
        }
        return false;
    }
    
    public static function exec($sql){
        $db = self::connect();
        return $db->query($sql);
    }
    
}
?>