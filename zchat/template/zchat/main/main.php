<?php include(TPL_PATH.'header.php');?>
<div class="container">
    <div>
        <div class="user_list">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">在线用户</h3>
                </div>
                <div class="panel-body h500" id="ollist">

                </div>
            </div>
        </div>
        <div class="chat_list">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">聊天区</h3>
                </div>
                <div id="chat_content" class="panel-body h500">
                    <p>zchat demo~~</p>
                </div>
            </div>
        </div>
    </div>
    <div class="chat_send">
        <div class="col-lg-6">
            <div class="input-group">
                <select class="form-control" id="sendTo" style="width:100px;">
                    <option value="0">全体</option>
                </select>
                <input type="text" id="msgContent" class="form-control">
      <span class="input-group-btn">
        <button class="btn btn-default" type="button" id="sendBtn" onclick="sendMsg()">发言</button>(ctrl+enter发送)
      </span>
            </div><!-- /input-group -->
        </div><!-- /.col-lg-6 -->
    </div>
    <div>协议自适应范例(socket和http自适应)：<a href="<?php echo \common\Utils::makeUrl('chat/main', 'online');?>" target='_blank'>web方式查看在线用户</a>)</div>


</div> <!-- /container -->
<div id="chat_swf"></div>
<script src="<?php echo $static_url;?>js/jquery-1.9.1.js"></script>
<script src="<?php echo $static_url;?>js/jsocket.js"></script>
<script src="<?php echo $static_url;?>js/swfobject.js"></script>
<script src="<?php echo $static_url;?>js/chat.js"></script>
<script type="text/javascript">
    chatClient.init('<?php echo $app_host; ?>',
        8991,
        'chat_swf',
        '<?php echo $static_url;?>js/jsocket.swf',
        [<?php echo $uid;?>, '<?php echo $token;?>']
    );
</script>
<?php include(TPL_PATH.'footer.php');?>
