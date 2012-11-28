<?php
define('ROOT_PATH', dirname(__FILE__) . '/');
define('CONFIG_PATH', ROOT_PATH . '../config/');
define('LOG_PATH', ROOT_PATH . '../log/');
define('LIB_PATH', ROOT_PATH . 'lib/');
define('CLASS_PATH', ROOT_PATH . 'class/');
include CONFIG_PATH . 'config.php';
include LIB_PATH . 'mysql/Mysqli.class.php';
include LIB_PATH . 'cache/Memcache.class.php';
include LIB_PATH . 'common/Autoload.class.php';
include LIB_PATH . 'common/SessionHandler.class.php';


?>