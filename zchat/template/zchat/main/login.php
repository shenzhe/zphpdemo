<?php include(TPL_PATH.'header.php');?>
<div class="container">

    <form class="form-signin" role="form" method="post" action="<?php echo \common\Utils::makeUrl('main', 'check');?>" />
        <h2 class="form-signin-heading">请登录</h2>
        <input type="text" name="username" class="form-control" placeholder="用户名" required autofocus>
        <input type="password" name="password" class="form-control" placeholder="密码" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit">登录</button>
        <a href="<?php echo \common\Utils::makeUrl('main', 'reg');?>" class="btn btn-primary btn-lg active" role="button">注册</a>
    </form>

</div> <!-- /container -->
<?php include(TPL_PATH.'footer.php');?>