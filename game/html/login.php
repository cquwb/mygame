<?php
header("Content-Type:text/html; charset=utf-8");
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" >
    </head>
    <body>
        <form action = 'http://127.0.0.1/xiyou/html/index.php?m=main&a=login' method = 'POST'>
            <table>
                <tr>
                    <td>账号</td>
                    <td><input type="text" name='user' value="" /></td>
                </tr>
                <tr>
                    <td>密码</td>
                    <td><input type="password" name='password' value="" /></td>
                </tr>
                 <tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" value="登录" /></td>
                </tr>
            </table>
        </form>
    </body>
</html>
