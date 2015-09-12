<?php
if (!function_exists('is_mobile')) {
    function is_mobile()
    {
        //php判断客户端是否为手机
        $agent = $_SERVER['HTTP_USER_AGENT'];
        return (strpos($agent, "NetFront") || strpos($agent, "iPhone") || strpos($agent, "MIDP-2.0") || strpos($agent, "Opera Mini") || strpos($agent, "UCWEB") || strpos($agent, "Android") || strpos($agent, "Windows CE") || strpos($agent, "SymbianOS"));
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>zphp + swoole小蝌蚪互动聊天室</title>
    <link rel="stylesheet" type="text/css" href="<?=$static_url?>css/main.css"/>
    <meta name="viewport"
          content="width=device-width; initial-scale=1.0; maximum-scale=1.0; minimum-scale=1.0; user-scalable=0;"/>
    <meta name="apple-mobile-web-app-capable" content="YES">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <link rel="apple-touch-icon" href="<?=$static_url?>images/apple-touch-icon.png"/>
    <meta name="title" content="zphp-swoole-todpole!"/>
    <meta name="description" content="zphp+swoole + HTML5+WebSocket +PHP socket 广播 小蝌蚪交互游戏程序 ，坐标实时推送、实时聊天等"/>
    <link rel="image_src" href="<?=$static_url?>images/fb-image.jpg"
    / >
</head>
<body>
<canvas id="canvas"></canvas>

<div id="ui">
    <div id="fps"></div>

    <input id="chat" type="text"/>

    <div id="chatText"></div>
    <h2>zphp + swoole</h2>
    <?php if (!is_mobile()) { ?>
        <div id="instructions">
            <h2>介绍</h2>

            <p>直接打字聊天!<br/>输入 name: XX 则会设置你的昵称为XX</p>
        </div>
        <aside id="info">
            <section id="share">
                <a rel="external" href="http://github.com/shenzhe/zphpdemo" title="zphp-todpole at GitHub">源代码：<img
                        src="<?=$static_url?>css/images/github.png" alt="fork on github"></a>
                &nbsp;&nbsp;
            </section>
            <section id="wtf">
                <h2>powered&nbsp;by&nbsp;<a rel="external" href="http://www.swoole.com" target="_blank">zphp+swoole</a>
                    &nbsp;&nbsp;&nbsp;&nbsp;感谢<a href="http://rumpetroll.com/" target="_blank">rumpetroll.com</a>提供的界面
                </h2>
            </section>
        </aside>
    <?php } ?>
    <aside id="frogMode">
        <h3>Frog Mode</h3>
        <section id="tadpoles">
            <h4>Tadpoles</h4>
            <ul id="tadpoleList">
            </ul>
        </section>
        <section id="console">
            <h4>Console</h4>
        </section>
    </aside>

    <div id="cant-connect">
        与服务器断开连接了。您可以重新刷新页面。
    </div>
    <div id="unsupported-browser">
        <p>
            您的浏览器不支持 <a rel="external" href="http://en.wikipedia.org/wiki/WebSocket">WebSockets</a>.
            推荐您使用以下浏览器
        </p>
        <ul>
            <li><a rel="external" href="http://www.google.com/chrome">Google Chrome</a></li>
            <li><a rel="external" href="http://apple.com/safari">Safari 4</a></li>
            <li><a rel="external" href="http://www.mozilla.com/firefox/">Firefox 4</a></li>
            <li><a rel="external" href="http://www.opera.com/">Opera 11</a></li>
        </ul>
        <p>
            <a href="#" id="force-init-button">仍然浏览!</a>
        </p>
    </div>

</div>

<script src="<?=$static_url?>/js/lib/parseUri.js"></script>
<script src="<?=$static_url?>/js/lib/modernizr-1.5.min.js"></script>
<script src="<?=$static_url?>/js/jquery.min.js"></script>
<script src="<?=$static_url?>/js/lib/Stats.js"></script>

<script src="<?=$static_url?>/js/App.js"></script>
<script src="<?=$static_url?>/js/Model.js"></script>
<script src="<?=$static_url?>/js/Settings.js"></script>
<script src="<?=$static_url?>/js/Keys.js"></script>
<script src="<?=$static_url?>/js/WebSocketService.js"></script>
<script src="<?=$static_url?>/js/Camera.js"></script>

<script src="<?=$static_url?>/js/Tadpole.js"></script>
<script src="<?=$static_url?>/js/TadpoleTail.js"></script>

<script src="<?=$static_url?>/js/Message.js"></script>
<script src="<?=$static_url?>/js/WaterParticle.js"></script>
<script src="<?=$static_url?>/js/Arrow.js"></script>
<script src="<?=$static_url?>/js/formControls.js"></script>

<script src="<?=$static_url?>/js/main.js"></script>

</body>
</html>
