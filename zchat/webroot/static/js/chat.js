if(!window.console) {
    window.console.log = function(msg) {
        return msg;
    }
}
var chatCMD = {
    LOGIN : 1, //登录
    LOGIN_SUCC : 2, //登录成功
    RELOGIN : 3,      //重复登录
    NEED_LOGIN : 4, //需要登录
    LOGIN_ERROR : 5,  //登录失败
    HB : 6,           //心跳
    CHAT : 7,         //聊天
    OLLIST : 8,      //获取在线列表
    LOGINOUT: 9,        //退出
    ERROR : -1			//错误
};
var chatClient = {
    host: '127.0.0.1',
    port: 8991,
    socket: null,
    uinfo: {},
    olList: {},  //在线用户
    renum: 2,
    hb: 0,
    uinfo:[],
    msgEof : '##||##',
    buffer: '',
    cb: {
        connect: function (success, data) {
            console.log("server connect, success:" + success + " data:" + data);
        },
        send: function (data) {
            console.log("send data" + data)
        },
        receive: function (data) {
            console.log("receive data" + data)
        },
        close: function () {
            this.connected = 0;
            console.log("server close");
        }
    },
    connected: 0,
    init: function (host, port, target, swf, uinfo) {
        this.host = host;
        this.port = port;
        this.uinfo = uinfo;
        this.socket = new jSocket(this.ready, this.connect,
            this.receive, this.close);
        this.socket.setup(target, swf);
    },
    ready: function () {
        chatClient.socket.connect(chatClient.host, chatClient.port);
    },

    retry: function () {
        if (this.renum < 1) {
            return 0;
        }
        this.ready();
        this.renum--;
        return 1;
    },

    connect: function (success, data) {
        return chatClient.cb.connect.call(this, success, data);
    },

    send: function (data) {
        this.socket.write(JSON.stringify(data) + chatClient.msgEof);
        return this.cb.send.call(this, JSON.stringify(data) + chatClient.msgEof);
    },

    receive: function (content) {
        console.log("receive from server: "+content);
        var tmpArr = content.split(chatClient.msgEof);
        for(var key in tmpArr) {
            if(tmpArr[key].length > 0) {
                chatClient.cb.receive.call(this, JSON.parse(tmpArr[key]));
            }
        }

    },

    close: function () {
        return chatClient.cb.close.call(this);
    },

    selfClose: function () {
        chatClient.socket.close();
        chatClient.connected = 0;
        chatClient.clearhb();
    },

    heartbeat: function (seconds) {
        chatClient.hb = window.setInterval(function () {
            chatClient.send([chatCMD.HB]);
        }, seconds * 1000);
    },

    clearhb: function () {
        if (chatClient.hb) {
            window.clearInterval(chatClient.hb);
            chatClient.hb = 0;
        }
    }
};

function checkScroll() {
    var obj = document.getElementById('chat_content');
    obj.scrollTop = obj.scrollHeight;
}

function sendMsg() {
    if (!chatClient.connected) {
        alert('服务器没有连接');
        return;
    }

    var msgContent = $.trim($("#msgContent").val());
    if (msgContent == "") {
        alert('请输入聊天内容');
        $("#msgContent").focus();
        return;
    }
    var sendTo = parseInt($("#sendTo").val());
    if(sendTo == chatClient.uinfo[0]) {
        alert("不能和自己聊天～");
        return ;
    }
    chatClient.send([
        chatCMD.CHAT,
        [sendTo, msgContent]
    ]);
    $("#msgContent").val('');
}


chatClient.cb.connect = function (success, data) {
    if (!success) {
        console.log("error:" + data);
        $("#chat_content").append('<p>服务器连接失败</p>');
        if (chatClient.retry()) {
            $("#chat_content").append('<p>重连中</p>');
        }
        return;
    }
    chatClient.connected = 1;
    $("#chat_content").append('<p>服务器连接成功</p>');
    $("#chat_content").append('<p>正在初始化用户信息</p>');
    chatClient.send([
        chatCMD.LOGIN,
        [chatClient.uinfo[0],chatClient.uinfo[1]]
    ]);
}

chatClient.cb.close = function () {
    $("#chat_content").append('<p>聊天服务器关闭中.</p>');
    chatClient.connected = 0;
    chatClient.clearhb();
}

chatClient.cb.receive = function (data) {
    console.log(data);
    checkScroll();
    switch (data[0]) {
        case chatCMD.ERROR:
            console.log("error");
            break;
        case chatCMD.LOGIN_SUCC:
            if(data[1][0] == chatClient.uinfo[0]) { //自已
                $("#chat_content").append('欢迎来到zchat demo~~');
                chatClient.heartbeat(60);
                chatClient.send([
                    chatCMD.OLLIST,
                ]);
                $("#ollist").html('<p>正在获取在线列表</p>');
            } else { //别人
                $("#chat_content").append('<p>'+data[1][1]+' 来到了聊天室！</p>');
                chatClient.olList[data[1][0]] = data[1];
                parseOl();
            }
            break;
        case chatCMD.OLLIST:    //获取在线列表
            chatClient.olList = data[1];
            parseOl();
            break;
        case chatCMD.CHAT:
            if(data[1][2] < 1) {
                $("#chat_content").append('<p>' + chatClient.olList[data[1][0]][1] + ' 对 大家 说： ' + data[1][1] +'</p>');
            } else {
                if(data[1][0] == chatClient.uinfo[0]) {
                    $("#chat_content").append('<p> 你 对 ' + chatClient.olList[data[1][2]][1] + ' 说： ' + data[1][1] +'</p>');
                } else {
                    $("#chat_content").append('<p>' + chatClient.olList[data[1][0]][1] + ' 对 你 说： ' + data[1][1] +'</p>');
                }
            }
            break;
        case chatCMD.LOGINOUT:
            $("#chat_content").append('<p>'+chatClient.olList[data[1][0]][1]+' 退出了聊天室！</p>');
            delete chatClient.olList[data[1][0]];
            parseOl();
            break;
    }
}

function parseOl() {
    var html = '';
    var shtml = '<option value="0">所有人</option>';
    for(var key in chatClient.olList) {
        if(key != chatClient.uinfo[0]) {
            shtml += '<option value="'+key+'">'+chatClient.olList[key][1]+'</option>';
        }
        html += '<p id="ol_'+key+'">'+chatClient.olList[key][1]+'</p>';
    }
    $("#sendTo").html(shtml);
    $('#ollist').html(html);
}


jQuery(document).keypress(function (e) {
    if (e.ctrlKey && e.which == 13 || e.which == 10) {
        jQuery("#sendBtn").click();
    } else if (e.shiftKey && e.which == 13 || e.which == 10) {
        jQuery("#sendBtn").click();
    }
});
