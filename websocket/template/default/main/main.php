<?php
    use ZPHP\Common\Route as ZRoute;
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="/static/css/bootstrap.css" rel="stylesheet">
    <script src="/static/js/jquery.js"></script>
    <script src="/static/js/bootstrap.js"></script>
</head>
<body>
<style>
    * {
        font-size: 14px;
    }

    body {
        background-color:rgb(131, 131, 131);
    }

    input {
        height: 36px;
        margin: 5px;
    }

    .login {
        width: 600px;
        margin: 200px auto;
    }
    #show_avatar{
        width: 120px;
        position: relative;
        left: 420px;
        top: -140px;
    }
    #show_avatar img{
        border-radius: 8px;
    }
</style>

<div class='container login' >
    <form action="<?=ZRoute::makeUrl('login', 'check')?>" method="post" class="well" style="height: 162px">
        <h3>zphp websocket chat</h3>
        <input type="text" name="name" placeholder="请输入用户名" required="required"/></br>
        <input type="password" name="password" placeholder="请输入密码"  required="required"/></br>
        <input type="submit" class="btn btn-primary" value="Login" id="login_submit">
        <input type="reset" class="btn" value="Reset"> [<a href="<?=ZRoute::makeUrl('login', 'reg')?>">注册</a>]
    </form>
</div>
</body>
</html>