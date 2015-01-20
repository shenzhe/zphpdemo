<?php include(TPL_PATH.'header.php');?>
<div class="container">

    <form class="form-signin" role="form" method="post" action="<?php echo \common\Utils::makeUrl('main', 'savereg');?>" />
        <h2 class="form-signin-heading">请注册</h2>
        <input type="text" name="username" class="form-control" placeholder="用户名" required autofocus>
        <input type="password" name="password" class="form-control" placeholder="密码" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit">注册</button>
    </form>

</div> <!-- /container -->
<?php include(TPL_PATH.'footer.php');?>