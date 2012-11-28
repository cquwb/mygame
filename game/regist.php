<?php
include './required.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo SERVER_NAME;?> - 一代宗师</title>
<meta name="keywords" content="网页游戏 牛B 好玩 一代宗师 游族" />
<meta name="Description" content="最值得期待的网页游戏 webgame" />
<link href="/style/css.css" type="text/css" rel="stylesheet" />
</head>
<body>
	<div class="con">
		<div class="logo">
			<img src="/style/img/logo.jpg" />
		</div>
		<div class="regist">
				<input type="text" name="username" id="username" class="input_txt"
					value="<?php echo isset($_REQUEST['username'])?$_REQUEST['username']:'';?>" />

                    
				<div class="input_submit">
					<input type="image" src="/style/img/submit.jpg" />
				</div>
               
			</form>
		</div>
        <div class="error">
            <?php if (isset($_REQUEST['error'])) { ?> <span><font color="red" ><?php echo $_REQUEST['error']; ?></font></span> <?php } ?>
        <div>
	</div>
</body>
</html>