<?php
namespace app\Controllers\Index;

use Lib\WsController;
class WebSocketController extends WsController {
    public function index()
    {
        //给客户端发送消息
        //$this->>fd 客户端唯一标示
        //$this->>server websocket server对象
        //
        $data = "哈哈哈我是一条消息";
        $data2 = "这是一条通过task任务群发消息";
        $this->server->push($this->fd,$data);
        //投递异步任务
        $this->task->delivery (\app\Task\Notice::class,'ToAll',[$this->fd,$data2]);
    }

}