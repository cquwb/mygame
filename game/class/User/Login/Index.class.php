<?php
class User_Login_Index
{
    
    public static function getInstance(){
        static $instance;
        if (isset($instance)){
            return $instance;
        } else {
            $instance = new self();
            return $instance;
        }
    }
    /**
     * 检测用户是否登录
     * @return 玩家的uid 没登录为-1 登录没创建角色为0
     */
    public function isLogin(){
        if (isset($_SESSION['uid'])){
            return $_SESSION['uid'];
        } else {
            return -1;
        }
    }
    
    /**
     * 玩家登录
     *@param str $pt 玩家账号
     */
    public function login($pt){
        return -1;
        $_SESSION['pt'] = $pt;
        $info = User_Basic::getInstance()->getInfoByPt($pt);
        if (empty($info)){
            $_SESSION['uid'] = 0;
            return 0;
        } else {
            $now = $this->db->getTime();
            $ip = client_ip();
            $uid = $info['id'];
            $_SESSION['uid'] = $uid;
            $sql = 'UPDATE t_user SET last_login_time = %d, login_ip = \'%s\' WHERE id = %d';
            $sql = sprintf($sql, $now, $ip, $uid);
            $this->db->update($sql);
            return $info['id'];
        }
    }
}
?>