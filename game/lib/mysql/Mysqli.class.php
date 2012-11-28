<?php
//mysql 连接类 php5 mysqli 版
class c_mysql_db {
    private $C_user; //设置用户名
    
    private $C_passwd; //设置口令
    
    private $C_host; //设置主机地址
    
    private $C_port; //设置数据库名
    
    private $C_db; //设置数据库名
    
    private $C_socket; //是否是SOCKET连接
    
    private $C_tansact = 0; //是否开事务
    
    private $C_error = 0; //是否有错误
    
    private $I_fields = array(); //结果集的列名
    
    private $I_a_rows = ""; //得到 MySQL 最后操作影响的列数目
    
    private $I_linkID = 0; //连线句柄
    
    private $I_stmtID = 0; //查询句柄
    
    private $I_rows = ""; //取得传回列的数目
    
    private $I_cols = ""; //取得传回列的数目
    
    private $A_data = array(); //返回的数据
    
    private static $instance_game = NULL;
    
    private static $instance_log = NULL;
    
    private static $instance_system = NULL;
    
    private $transaction_flag = 0;
    
    private $transaction_sql = array();

    //换成public，允许有多个instance,for different database exists one time
    public function __construct ($user = DATABASE_USER_USER, $pwd = DATABASE_USER_PASS, $host = DATABASE_USER_HOST, $port = DATABASE_USER_PORT, $db = DATABASE_USER_NAME, $socket = FALSE) {
        $this->C_host = $host;
        $this->C_user = $user;
        $this->C_passwd = $pwd;
        $this->C_db = $db;
        $this->C_port = $port;
        $this->C_socket = $socket;
    }
    
    public function __destruct() {
        if (!is_int($this->I_linkID)) {
            //todo SOCKET要保持连接
            mysqli_close($this->I_linkID);
            $this->I_linkID = 0;//Closes a previously opened database connection. 在关闭数据库时候清空连接数据库句柄
        }
    }
    
    /**
     * 连接数据库
     * mysqli_connect() Returns an object which represents the connection to a MySQL Server. 
     * $this->I_linkID Object
     */
    private function _connect() {
        if (!$this->I_linkID = mysqli_connect($this->C_host, $this->C_user, $this->C_passwd, $this->C_db, $this->C_port, $this->C_socket)) {
            printf("Can't connect to MySQL Server. Errorcode: %s\n", mysqli_connect_error());
            exit;
        }
        @mysqli_query($this->I_linkID, "SET NAMES utf8");
//        @mysqli_query($this->I_linkID, "SET CHARACTER SET utf8");
    }

    //保留Singleton，在一个库里频繁插入，则不需要重复连接,只用来保存需要频繁插入的库的连接//
    public static function DBConnectGame($socket = TRUE) {
        if (NULL === self::$instance_game) {
            self::$instance_game = new self(DATABASE_USER_USER, DATABASE_USER_PASS, DATABASE_USER_HOST, DATABASE_USER_PORT, DATABASE_USER_NAME, $socket);
        }
        
        return self::$instance_game;
    }
    
    //保留Singleton，在一个库里频繁插入，则不需要重复连接,只用来保存需要频繁插入的库的连接//
    public static function DBConnectLog($socket = FALSE) {
        if (NULL === self::$instance_log) {
            self::$instance_log = new self(DATABASE_LOG_USER, DATABASE_LOG_PASS, DATABASE_LOG_HOST, DATABASE_LOG_PORT, DATABASE_LOG_NAME, $socket);
        }
        
        return self::$instance_log;
    }
    
    //保留Singleton，在一个库里频繁插入，则不需要重复连接,只用来保存需要频繁插入的库的连接//
    public static function DBConnectSystem($socket = FALSE) {
        if (NULL === self::$instance_system) {
            self::$instance_system = new self(DATABASE_SYS_USER, DATABASE_SYS_PASS, DATABASE_SYS_HOST, DATABASE_SYS_PORT, DATABASE_SYS_NAME, $socket);
        }
        
        return self::$instance_system;
    }

	//返回数据库连接
    public static function DBConnectByConfig($config, $singleton, $socket = FALSE) {
		if($singleton=='DBConnectSystem'){
			self::$instance_system = new self($config['user'], $config['pwd'], $config['host'], 3306, $config['name'], $socket);
			return self::$instance_system;
		}
		elseif($singleton=='DBConnectLog'){
			self::$instance_log = new self($config['user'], $config['pwd'], $config['host'], 3306, $config['name'], $socket);
			return self::$instance_log;
		}
		elseif($singleton=='DBConnectGame'){
			self::$instance_game = new self($config['user'], $config['pwd'], $config['host'], 3306, $config['name'], $socket);
			return self::$instance_game;
		}
    }
    
    /**
     * Selects the default database for database queries
     *
     * @param string $C_db The database name. 
     */
    public function selectDB($db) { 
        if (is_int($this->I_linkID)) {
            $this->_connect();
        }
        
        @mysqli_select_db($this->I_linkID, $db) or $this->MySQL_ErrorMsg("Unable to select database: {$db}");
    }
    
    /**
     * Perform a SELECT statement
     *
     * @param string $sql
     * @param string $pk4index 主键 返回数组下标, null采用0子增下标
     * @return mixed array查询数组；FALSE,查询失败
     */
    public function query($sql, $pk4index = NULL) {
        if (is_int($this->I_linkID)) {
            $this->_connect();
        }
        
        $result = array ();
        
        //mysqli_query
        //Returns FALSE on failure. 
        //For successful SELECT, SHOW, DESCRIBE or EXPLAIN queries mysqli_query() will return a result object.
        //For other successful queries mysqli_query() will return TRUE.
        $this->I_stmtID = @mysqli_query($this->I_linkID, $sql) or $this->MySQL_ErrorMsg("Unable to perform query: {$sql}");
        if (is_bool($this->I_stmtID)) {
            return FALSE;
        }
        
        if (isset($pk4index)) {
            while ($row = mysqli_fetch_assoc($this->I_stmtID)) {
    			$result[$row[$pk4index]] = $row;
    		}
        }
        else {
            while ($row = mysqli_fetch_assoc($this->I_stmtID)) {
    			$result[] = $row;
    		}
        }
        @mysqli_free_result($this->I_stmtID); //Frees the memory associated with a result
        
        return $result; //返回二维数组
    }
    
    /**
     * 取得一行记录
     *
     * @param string $sql
     * @param boolean $is_first boolean, TRUE：取第1行；FALSE：取最后一行
     * @return mixed array()查询信息；FALSE,查询失败
     */
    public function queryOne($sql, $is_first = TRUE) {    
        if (is_int($this->I_linkID)) {
            $this->_connect();
        }
        
        $result = array ();
        
        $this->I_stmtID = @mysqli_query($this->I_linkID, $sql) or $this->MySQL_ErrorMsg("Unable to perform query: {$sql}");
        if (is_bool($this->I_stmtID)) {
            return FALSE;
        }
        
        while ($row = mysqli_fetch_assoc($this->I_stmtID)) {
			if ($is_first) {
			    $result = $row;
			    break;
			}
			else {
			    $result[] = $row;
			}
		}
        @mysqli_free_result($this->I_stmtID); //清空查询句柄
		
        if (!$is_first && (count($result) > 0)) {
		    $result = array_pop($result);
		}
        
        return $result;
    }
    
    /**
     * Executes one or multiple queries which are concatenated by a semicolon.
     *
     * @param string $sql
     * @return array multiple query result
     */
    public function call_sp($sql) {
        if (is_int($this->I_linkID)) {
            $this->_connect();
        }
        
        $A_data = array();
        if(mysqli_multi_query($this->I_linkID, $sql))  { 
            $i = 0;
            do {
                /* store first result set */
                if ($result = mysqli_use_result($this->I_linkID)) {
                    $j = 0;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $A_data[$i][$j] = $row;
                        ++$j;
                    }
                    mysqli_free_result($result);
                }
                ++$i;
            } while (mysqli_next_result($this->I_linkID));
            
            return $A_data;
        }
        else {
            if (0 == $this->C_tansact) {
                $this->MySQL_ErrorMsg("Unable to perform call sp: {$sql}");
            } 
            else {
                $this->C_error = 1;
            }
        }
    }
    
    /**
     * Perform a insert query
     *
     * @param string $sql
     * @return Returns FALSE on failure. Return TRUE for successful queries mysqli_query(). 
     */
    public function insert($sql) {
        if (is_int($this->I_linkID)) {
            $this->_connect();
        }
        
        if (1 == $this->C_tansact) {
            $this->transaction_sql[] = $sql;
        }
        
        $this->I_stmtID = @mysqli_query($this->I_linkID, $sql);
        if ((0 == $this->C_tansact) && !$this->I_stmtID) {
            $this->MySQL_ErrorMsg("Unable to perform insert: {$sql}");
        } 
        elseif (!$this->I_stmtID) {
            $this->C_error = 1;
            if ($this->C_tansact) {
                $this->MySQL_ErrorMsg("In transaction failed " . __FUNCTION__ . ": {$sql}");
            }
        }
        
        return $this->I_stmtID;
    }
    
    /**
     * Perform a Update query
     *
     * @param string $sql
     * @return Returns FALSE on failure. Return TRUE for successful queries mysqli_query(). 
     */
    public function update($sql) {
        if (is_int($this->I_linkID)) {
            $this->_connect();
        }
        
        if (1 == $this->C_tansact) {
            $this->transaction_sql[] = $sql;
        }
        
        $this->I_stmtID = @mysqli_query($this->I_linkID, $sql);
        if ((0 == $this->C_tansact) && (!$this->I_stmtID)) {
            $this->MySQL_ErrorMsg("Unable to perform update: {$sql}");
        } 
        else if(!$this->I_stmtID) {
            $this->C_error = 1;
            if ($this->C_tansact) {
                $this->MySQL_ErrorMsg("In transaction failed " . __FUNCTION__ . ": {$sql}");
            }
        }
        
        return $this->I_stmtID;
    }

	/**
     * Perform a Replace query
     *
     * @param string $sql
     * @return Returns FALSE on failure. Return TRUE for successful queries mysqli_query(). 
     */
    public function replace($sql) {
        if (is_int($this->I_linkID)) {
            $this->_connect();
        }
        
        if (1 == $this->C_tansact) {
            $this->transaction_sql[] = $sql;
        }
        
        $this->I_stmtID = @mysqli_query($this->I_linkID, $sql);
        if ((0 == $this->C_tansact) && (!$this->I_stmtID)) {
            $this->MySQL_ErrorMsg("Unable to perform update: {$sql}");
        } 
        else if(!$this->I_stmtID) {
            $this->C_error = 1;
            if ($this->C_tansact) {
                $this->MySQL_ErrorMsg("In transaction failed " . __FUNCTION__ . ": {$sql}");
            }
        }
        
        return $this->I_stmtID;
    }
    
    /**
     * Perform a Delete query
     *
     * @param string $sql
     * @return Returns FALSE on failure. Return TRUE for successful queries mysqli_query(). 
     */
    public function delete($sql) {
        if (is_int($this->I_linkID)) {
            $this->_connect();
        }
        
        if (1 == $this->C_tansact) {
            $this->transaction_sql[] = $sql;
        }
        
        $this->I_stmtID = @mysqli_query($this->I_linkID, $sql);
        if ((0 == $this->C_tansact) && (!$this->I_stmtID)) {
            $this->MySQL_ErrorMsg("Unable to perform delete: {$sql}");
        } 
        else if(!$this->I_stmtID) {
            $this->C_error = 1;
            if ($this->C_tansact) {
                $this->MySQL_ErrorMsg("In transaction failed " . __FUNCTION__ . ": {$sql}");
            }
        }
        
        return $this->I_stmtID;
    }
    
    /**
     * 开始事物，关闭自动提交
     *
     */
    public function transaction_start() {
        if (is_int($this->I_linkID)) {
            $this->_connect();
        }
        
        if (1 == $this->C_tansact) {
            $sql_json = json_encode($this->transaction_sql);
            $this->MySQL_ErrorMsg("--------Here. Transaction has started \n {$sql_json} \n--------");
        }
        
        $this->I_stmtID = @mysqli_autocommit($this->I_linkID, FALSE);
        $this->C_tansact = 1;
    }
    
    /**
     * 提交或者回滚事物，打开自动提交功能
     *
     * @return boolean TRUE,提交；FLASE，回滚
     */
    public function transaction_submit() {
        if (is_int($this->I_linkID)) {
            $this->_connect();
        }
        
        $result_commit = (0 == $this->C_error);
        if ($result_commit) {
            $this->I_stmtID = @mysqli_commit($this->I_linkID);
            $this->I_stmtID = @mysqli_autocommit($this->I_linkID, TRUE); //恢复MYSQL默认配置
        }
        else {
            $this->I_stmtID = @mysqli_rollback($this->I_linkID);
            $this->I_stmtID = @mysqli_autocommit($this->I_linkID, TRUE);
            
            $sql_json = json_encode($this->transaction_sql);
            $this->MySQL_ErrorMsg("--------Auto rollback message above In transaction failed \n {$sql_json} \n--------");
        }
        
        $this->_resetStat();
        
        return $result_commit;
    }
    
    /**
     * SQL没有错，但是需要手动回滚的时候，可以调用此函数
     *
     */
    public function transaction_rollback() {
        if (is_int($this->I_linkID)) {
            $this->_connect();
        }
        
        $this->I_stmtID = @mysqli_rollback($this->I_linkID);
        $this->I_stmtID = @mysqli_autocommit($this->I_linkID, TRUE);
        
        $sql_json = json_encode($this->transaction_sql);
        $this->MySQL_ErrorMsg("--------Manual rollback message above In transaction failed \n {$sql_json} \n--------");
        
        $this->_resetStat();
    }
    
    /**
     * Gets the number of affected rows in a previous MySQL operation
     *
     * @return Returns the number of rows affected by the last INSERT, UPDATE, REPLACE or DELETE query.
     * For SELECT statements mysqli_affected_rows() works like mysqli_num_rows(). 
     */
    public function getAffectedRows() {
        if (is_int($this->I_linkID)) {
            $this->_connect();
        }
        
        return mysqli_affected_rows($this->I_linkID);
    }
    
    /**
     * Returns the auto generated id used in the last query
     *
     * @return The value of the AUTO_INCREMENT field that was updated by the previous query. 
     * Returns zero if there was no previous query on the connection 
     * or if the query did not update an AUTO_INCREMENT value. 
     */
    public function getInsertId() {
        if (is_int($this->I_linkID)) {
            $this->_connect();
        }
        
        return mysqli_insert_id($this->I_linkID);
    }
    
    public function real_escape_string($str) {
        if (is_int($this->I_linkID)) {
            $this->_connect();
        }
        
        return mysqli_real_escape_string($this->I_linkID, $str);
    }
    
    private function MySQL_ErrorMsg ($msg) {
        $path = LOG ."database/";
        if (!is_dir($path)){
            mkdir($path, 0777, true);
        }
        $filename = $path ."error.log";
        
        if (!$handle = fopen($filename, 'a')) {
            return FALSE;
        }
        
        $msg .=  "\n errorno:" . mysqli_errno($this->I_linkID) . ':' . mysqli_error($this->I_linkID);
        $sess_str = Session::getAction(FALSE);
        if (isset($sess_str) && '' != $sess_str) {
            $msg .= "\n amf interface info:{$sess_str}";
        }
        else {
            $msg .= "\n no amf interface info by session :(";
        }
        $content = date('Y-m-d H:i:s') . "\n{$msg}\n";
        if (!fwrite($handle, $content)) {
            return FALSE;
        }
        fclose($handle);
    }
    
    /**
     * 获取数据库服务器时间
     * @param boolean $is_real_db_time 是否是真的需要服务器时间 当一台服务器的时候，PHP时间也是可以的
     * 
     * @return int 
     */
    public function getTime($is_real_db_time = FALSE) {
        if (!$is_real_db_time) {
            return time();
        }
        $sql = "SELECT UNIX_TIMESTAMP() AS 'time'";
        $info = $this->queryOne($sql);
        
        return $info['time'];
    }

	// 查询数据库记录
	function select ( $dbTable, $condition = '', $orderBy = '', $limit = 0, $offset = 0, $fields = '*', $groupBy = '' )
	{
		if ( is_array ( $fields ) )
		{
			$fieldList = @implode( ',', $fields );
		}
		else
		{
			$fieldList = $fields;
		}
		if ( $condition != '' )
		{
			$condition = "WHERE $condition";
		}
		$orderBy = trim ( $orderBy );
		if ( $orderBy != '' && !strstr ( strtoupper ( $orderBy ), 'ORDER BY' ) )
		{
			$orderBy = "ORDER BY $orderBy";
		}

		$groupBy = trim ( $groupBy );
		if ( $groupBy != '' && !strstr ( strtoupper ( $groupBy ), 'GROUP BY' ) )
		{
			$groupBy = "GROUP BY $groupBy";
		}

		$strSql = " SELECT $fieldList FROM $dbTable $condition $groupBy $orderBy";

		$limit = intval ( $limit );
		$offset = intval ( $offset );
		if ( $limit )
		{
			$strSql .= " LIMIT $limit";
		}
		if ( $offset )
		{
			$strSql .= " OFFSET $offset";
		}
		
		return $this->query ( $strSql );
	}

	// 统计符合条件的记录条数
	function count ( $dbTable, $condition = '', $fields = '*' )
	{
		if (is_int($this->I_linkID)) {
            $this->_connect();
        }

		if ( $condition != '' )
		{
			$condition = "WHERE $condition";
		}
		$strSql = " SELECT COUNT($fields) AS count_records FROM $dbTable $condition";
		
		
		$this->I_stmtID = @mysqli_query($this->I_linkID, $strSql) or $this->MySQL_ErrorMsg("Unable to perform query: {$sql}");
        if (is_bool($this->I_stmtID)) {
            return FALSE;
        }
        
        while ($row = mysqli_fetch_assoc($this->I_stmtID)) {
			if ($is_first) {
			    $result = $row;
			    break;
			}
			else {
			    $result[] = $row;
			}
		}
        @mysqli_free_result($this->I_stmtID); //清空查询句柄
		
        if (!$is_first && (count($result) > 0)) {
		    $result = array_pop($result);
		}
        
        return $result['count_records'];
	}

	// 查询数据库单条记录
	function find ( $dbTable, $condition = '', $orderBy = '', $fields = '*', $groupBy = '' )
	{
		if ( is_array ( $fields ) )
		{
			$fieldList = @implode( ',', $fields );
		}
		else
		{
			$fieldList = $fields;
		}
		if ( $condition != '' )
		{
			$condition = "WHERE $condition";
		}
		$orderBy = trim ( $orderBy );
		if ( $orderBy != '' && !strstr ( strtoupper ( $orderBy ), 'ORDER BY' ) )
		{
			$orderBy = "ORDER BY $orderBy";
		}

		$groupBy = trim ( $groupBy );
		if ( $groupBy != '' && !strstr ( strtoupper ( $groupBy ), 'GROUP BY' ) )
		{
			$groupBy = "GROUP BY $groupBy";
		}

		$strSql = " SELECT $fieldList FROM $dbTable $condition $groupBy $orderBy";


		return  $this->queryOne ( $strSql );
	}
	
	private function _resetStat() {
	    $this->transaction_sql = array();
        $this->C_tansact = 0;
        $this->C_error = 0; //守护进程里面需要还原错误标志，不然后续都回滚了
	}
}
?>