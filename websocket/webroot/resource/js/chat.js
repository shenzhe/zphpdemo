var ws = {};
var client_id = 0;
var userlist = {};
var GET = new Object();
GET = getRequest();

$(document).ready(
	function() {
		if ( window.WebSocket || window.MozWebSocket) 
		{
			ws = new WebSocket( "ws://211.144.68.31:9501" );
			/**
			 * 连接建立时触发
			 */
			ws.onopen = function(e)
			{
				//必须的输入一个名称和一个图像才可以聊天
				if (GET['name'] == undefined || GET['avatar'] == undefined)
				{
					alert('非法请求');
					ws.close();
					return false;
				}
			
				//发送登录信息
				
				msg = new Object();
				msg.cmd = 'login';
				msg.name = GET['name'];
				msg.avatar = GET['avatar'];
				ws.send( $.toJSON( msg ));
			};
			
			//有消息到来时触发
			ws.onmessage = function( e )
			{
				var cmd = $.evalJSON( e.data ).cmd;
				if( cmd == 'login' )
				{
					client_id = $.evalJSON( e.data ).fd;
					//获取在线列表
					msg = new Object();
					msg.cmd = 'getOnline';
					ws.send( $.toJSON( msg ) );
					
					//alert( "收到消息了:"+e.data );
				}
				else if( cmd == 'getOnline' )
				{
					showOnlineList( e.data );
				}
				else if( cmd == 'newUser' )
				{
					showNewUser( e.data );
				}
				else if( cmd == 'fromMsg' )
				{
					showNewMsg( e.data );
				}
				else if( cmd == 'offline' )
				{
					var cid = $.evalJSON( e.data ).fd;
					delUser( cid ); 
					showNewMsg( e.data  );	
				}
			};
			
			
			/**
			 * 连接关闭事件
			 */
			ws.onclose = function(e) 
			{
				if( confirm( "您已退出聊天室" ))
				{
				//alert('您已退出聊天室');
				location.href = 'login.html';
				}
			};
			
			/**
			 * 异常事件
			 */
			ws.onerror = function(e)
			{
				alert( "异常:"+e.data );
				console.log("onerror");
			};
			
			function selectUser(userid) {
				$('#userlist').val(userid);
			}
		}
		else
		{
			alert("您的浏览器不支持WebSocket，请使用Chrome/FireFox/Safari/IE10浏览器");
		}
});


document.onkeydown = function(e){
    var ev = document.all ? window.event : e;
    if(ev.keyCode==13)
    {
    	sendMsg();
    }
}

/**
 * 显示所有在线列表
 * @param data
 */
function showOnlineList( data )
{
	var dataObj = $.evalJSON( data );
	var li = '';
	var option = "<option value='0' id='user_all' >所有人</option>" ;
	
	for ( var i = 0; i < dataObj.list.length; i++ ) 
	{
		li = li+"<li id='inroom_" + dataObj.list[i].fd + "'>" +
		"<a href='javascript:selectUser("
		+ dataObj.list[i].fd + ")'>" + "<img src='" + dataObj.list[i].avatar
		+ "' width='50' height='50'></a></li>" 
		
		userlist[dataObj.list[i].fd] = dataObj.list[i].name; 
		
		if( dataObj.list[i].fd != client_id )
		{
			option = option + "<option value='" + dataObj.list[i].fd + "' id='user_" + dataObj.list[i].fd + "'>"
			+ dataObj.list[i].name + "</option>"
		}
	}
	$('#left-userlist').html( li );
	$('#userlist').html( option );
}

/**
 * 当有一个新用户连接上来时
 * @param userid
 */
function showNewUser( data ) 
{
	var dataObj = $.evalJSON( data );
	if( !userlist[dataObj.fd] )
	{
		userlist[dataObj.fd] = dataObj.name;
		if ( dataObj.fd != client_id )
		{
			$('#userlist').append( "<option value='" + dataObj.fd + "' id='user_" + dataObj.fd + "'>"+dataObj.name+"</option>");

		}
		
		$('#left-userlist').append(
				"<li id='inroom_" + dataObj.fd + "'>" +
				"<a href='javascript:selectUser("
				+ dataObj.fd + ")'>" + "<img src='" + dataObj.avatar
				+ "' width='50' height='50'></a></li>");
		
	}
}

/**
 * 显示新消息
 */
function showNewMsg( data ) 
{
	var dataObj = $.evalJSON( data );
	var content = xssFilter( dataObj.data )
	var fromId = dataObj.from;
	var channal = dataObj.channal;
	content = parseXss( content );
	var said = '';
	
	$("#msg-template .msg-time").html(GetDateT());
	if (fromId == 0)
	{
		$("#msg-template .userpic").html("");
		$("#msg-template .content").html(
				"<span style='color: green'>【系统】</span>" + content);
	} 
	else 
	{
		var html = '';
		var to =  dataObj.to;
		//如果说话的是我自己
		if( client_id == fromId )
		{
			if( channal == 0  )
			{
				said = '我对大家说:';
			}
			else if( channal == 1 )
			{
				said = "我悄悄的对"+userlist[to]+"说:";
			}
			
			html += '<span style="color: orange">'+said+' </span> ';

		} 
		else
		{
			if( channal == 0  )
			{
				said = '对大家说:';
			}
			else if( channal == 1 )
			{
				said = "悄悄的对我说:";
			}
			
			html += '<span style="color: orange"><a href="javascript:selectUser('
					+ fromId + ')">' + userlist[fromId] + said;
			html += '</a></span> '
		}
		html += content + '</span>';
		$("#msg-template .content").html(html);
	}
	$("#chat-messages").append($("#msg-template").html());
	$('#chat-messages')[0].scrollTop = 1000000;
	
}

function xssFilter(val) 
{
	val = val.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\x22/g, '&quot;').replace(/\x27/g, '&#39;');
	return val;
}

function parseXss( val )
{
	val = val.replace( /#(\d*)/g,  '<img src="resource/img/face/$1.gif" />'  );
	val = val.replace( '&amp;' , '&' );
	return val;
}


function GetDateT() {
	var d;
	d = new Date();
	var h,i,s;
	
	h = d.getHours();
	i = d.getMinutes();
	s = d.getSeconds();
	
	h = ( h < 10 ) ? '0'+h : h;
	i = ( i < 10 ) ? '0'+i : i;
	s = ( s < 10 ) ? '0'+s : s;
	return h+":"+i+":"+s;
}

function getRequest()
{
	var url = location.search; // 获取url中"?"符后的字串
	var theRequest = new Object();
	if (url.indexOf("?") != -1) {
		var str = url.substr(1);
	
		strs = str.split("&");
		for ( var i = 0; i < strs.length; i++) {
			var decodeParam = decodeURIComponent( strs[i] );
			var param = decodeParam.split( "=" );
			theRequest[param[0]] = param[1];
		}
		
	}
	return theRequest;
}

function selectUser(userid)
{
	$('#userlist').val(userid);
}

function delUser( userid )
{
	$('#user_' + userid).remove();
	$('#inroom_' + userid).remove();
	delete (userlist[userid]);
	
}

function sendMsg()
{
	var content = $('#msg_content').val();
	content = content.replace(" ", "&nbsp;");
	if( !content )
	{
		return false;
	}
	
	if( $('#userlist').val() == 0 )
	{
		var msg = new Object();
		msg.cmd = 'message';
		msg.from = client_id;
		msg.channal = 0;
		msg.data = content;
		ws.send( $.toJSON( msg ) );
	} 
	else
	{
		var msg = new Object();
		msg.cmd = 'message';
		msg.from = client_id;
		msg.to = $('#userlist').val();
		msg.channal = 1;
		msg.data = content;
		ws.send( $.toJSON( msg ) );
	}
	$('#msg_content').val( "" );
	return false;

}


$(document).ready(function(){
	var a = '';
	for( var i = 1 ; i < 20 ; i++ )
	{
		a = a+'<a class="face" href="#" onclick="selectFace('+i+');return false;"><img src="resource/img/face/'+i+'.gif" /></a>';	
	}
	$("#show_face").html( a );
});


(function($){
	$.fn.extend({
		insertAtCaret:function( myValue )
		{
			var $t=$(this)[0];
			if( document.selection ) 
			{
				this.focus();
				sel = document.selection.createRange();
				sel.text = myValue;
				this.focus();
			}
			else if( $t.selectionStart || $t.selectionStart == '0')
			{
			
				var startPos = $t.selectionStart;
				var endPos = $t.selectionEnd;
				var scrollTop = $t.scrollTop;
				$t.value = $t.value.substring(0, startPos) + myValue + $t.value.substring(endPos, $t.value.length);
				this.focus();
				$t.selectionStart = startPos + myValue.length;
				$t.selectionEnd = startPos + myValue.length;
				$t.scrollTop = scrollTop;
			}
			else 
			{
				
				this.value += myValue;
				this.focus();
			}
		}
	}) 
})(jQuery);


function selectFace( id )
{
   var img = '<img src="resource/img/face/'+id+'.gif" />';
   $( "#msg_content" ).insertAtCaret( "#"+id ); 
   closeChatFace();
}


function showChatFace()
{
	$("#chat_face").attr( "class" ,"chat_face chat_face_hover");
	$("#show_face" ).attr( "class" ,"show_face show_face_hovers");
}

function closeChatFace()
{
	$("#chat_face").attr( "class" ,"chat_face" );
	$("#show_face" ).attr( "class" ,"show_face");
}

function toggleFace()
{
	$("#chat_face").toggleClass("chat_face_hover" );
	$("#show_face").toggleClass("show_face_hovers" );
}
