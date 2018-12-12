<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport' />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    <style type="text/css">
        body{margin: 20px;}
        #message div{ margin: 5px;}
    </style>
</head>

<body>
<input id="msg" type="text"/>
<button id="btn_send">发送消息 </button>

<button id="btn_close_ws">关闭WebSocket连接</button>
<HR/>
<div id="message"></div>
<script type="text/javascript">
    var websocket = null;
    //判断当前浏览器是否支持WebSocket
    if ('WebSocket' in window) {
        websocket = new WebSocket("ws://127.0.0.1:9521/");
        set_msg_innerhtml('尝试连接 ws://127.0.0.1:9521/ ')
    } else {
        set_msg_innerhtml('当前浏览器不支持WebSocket')
    }

    //连接发生错误的回调方法
    websocket.onerror = function () {
        if(websocket.readyState!=1){
            set_msg_innerhtml("WebSocket服务连接失败---状态码:"+websocket.readyState);
        }
        set_msg_innerhtml("状态码:"+websocket.readyState);
        set_msg_innerhtml("WebSocket连接发生错误");
    };

    //连接成功建立的回调方法
    websocket.onopen = function () {

        set_msg_innerhtml("ws://127.0.0.1:9521/ 连接成功");
    }

    //接收到消息的回调方法
    websocket.onmessage = function (event) {
        set_msg_innerhtml(event.data);
    }

    //连接关闭的回调方法
    websocket.onclose = function () {
        set_msg_innerhtml("WebSocket连接关闭");
    }

    //监听窗口关闭事件，当窗口关闭时，主动去关闭websocket连接，防止连接还没断开就关闭窗口，server端会抛异常。
    window.onbeforeunload = function () {
        websocket.close();
    }

    //将消息显示在网页上
    function set_msg_innerhtml(value) {
        html = "<div>"+value+"</div>";
        $('#message').append(html);
    }


    $(function(){
        //发送消息
        $("#btn_send").click(function () {
            var msg = $('#msg');
            websocket.send('{"route":"ws","uid":1,"msg":"'+msg.val()+'"}');
        });
        //关闭WebSocket连接
        $("#btn_close_ws").click(function () {
            websocket.close();
        });
    });


</script>
</body>

</html>