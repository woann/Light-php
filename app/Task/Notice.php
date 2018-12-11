<?php
// +----------------------------------------------------------------------
// | Created by PhpStorm
// +----------------------------------------------------------------------
// | Date: 18-12-10 上午10:01
// +----------------------------------------------------------------------
// | Author: woann <304550409@qq.com>
// +----------------------------------------------------------------------
namespace app\Task;

class Notice{
    /**
     * 通知所有在线的客户端
     * @param $fd       发起请求的FD
     * @param $data     要发送的内容
     *
     * @return bool
     */
    public function ToAll($fd,$data){
        $fds = [] ;//用来存放所有客户端fd
        foreach($this->server->connections as $client_fd){
            if($fd != $client_fd && $this->server->exist($client_fd)){
                //循环向客户端输出消息，排除掉发送者fd
                $this->server->push($client_fd,$data);
                $fds[] = $client_fd;
            }
        }
        return "已向[".join(",",$fds)."]发送通知内容：".$data;
    }
}
