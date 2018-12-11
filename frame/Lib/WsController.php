<?php
// +----------------------------------------------------------------------
// | Created by PhpStorm
// +----------------------------------------------------------------------
// | Date: 18-12-10 上午10:36
// +----------------------------------------------------------------------
// | Author: woann <304550409@qq.com>
// +----------------------------------------------------------------------
namespace Lib;

class WsController
{
    /**
     * @var \Piz\Router
     */
    protected $router;
    /**
     * @var swoole_server
     */
    protected $server;
    /**
     * @var swoole_server->frame
     */
    protected $frame;
    /**
     * @var swoole_server->task
     */
    protected $task;

    public function __set($name,$object){
        $this->$name = $object;
    }

    public function __get($name){
        return $this->$name;
    }
}
