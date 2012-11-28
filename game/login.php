<?php
require './required.php';

if (isset($_POST['pt'])){
    //登录
    $pt = $_POST['pt'];
    $password = $_POST['pwd'];
    $uid = User_Login_Index::getInstance()->login($pt);
    if ($uid < 0){
        header('Location: /login.php?pt='.$pt.'&error=账号或密码错误');
        exit;
    } elseif ($uid == 0){
        header('Location: /regist_game.php?pt='.$pt);
        exit;
    } else {
        header('Location: /index.php');
    }
} else {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo SERVER_NAME;?> - 我的西游</title>
<meta name="keywords" content="网页游戏 牛B 好玩 我的西游 游族" />
<meta name="Description" content="最值得期待的网页游戏 webgame" />
<link href="/style/css.css" type="text/css" rel="stylesheet" />
</head>
<body>
	<div class="con">
		<div class="logo">
			<span>XGAME</span>
		</div>
		<div class="login">
			<form action="" method="post" name="login" id="login">
                <div class="field">
                    <label for="pt">用户名</label><input type="text" name="pt" id="pt" class="input-txt" 
					value="<?php echo isset($_REQUEST['pt'])?$_REQUEST['pt']:'';?>" />
                </div>
                 <div class="field">
                   <label for="pwd">密 码</label><input type="password" name="pwd" id="pwd"  class="input-txt"  />
                </div>                    
				<div class="input_submit">
					<input type="submit" value="登录" />
				</div>
                <div class="error">
                    <?php if (isset($_REQUEST['error'])) { ?> <span><font color="red" ><?php echo $_REQUEST['error']; ?></font></span> <?php } ?>
                <div>
			</form>
		</div>
	</div>
</body>
</html>
<?php
}
?>