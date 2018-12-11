<?php
namespace Lib;

class Server
{
    private $server;
    private static $instance;
    private $config = [];
    private function __construct(){
//        $this->server = new \swoole_http_server('0.0.0.0',9501);
//        $this->server->on('start', [$this, 'onStart']);
//        $this->server->on('request' ,[$this,'onRequest']);
//        $this->server->start();
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function set_config($config){
        $this->config = $config;
        $this->name = $config["name"];
    }
    public function run(){
        $swoole_server = isset($this->config['server']) && $this->config['server'] == 'websocket' ? 'swoole_websocket_server' : 'swoole_http_server';
        $this->server = new $swoole_server($this->config['ip'],$this->config['port']);
        $this->server->set($this->config['set']);
        $this->server->on('start', [$this, 'onStart']);
        $this->server->on('WorkerStart', [$this, 'onWorkerStart']);
        if($this->config['server'] == 'websocket'){
            $this->server->on('open' ,[$this,'onOpen']);
            $this->server->on('message',[$this,'onMessage']);
            $this->server->on('close',[$this,'onClose']);
        }

        if( isset($this->config['set']['task_worker_num']) && $this->config['set']['task_worker_num']>0){
            $this->server->on('task',[$this,'onTask']);
            $this->server->on('finish',[$this,'onFinish']);
        }
        $this->server->on('request' ,[$this,'onRequest']);
        $this->server->start();
    }

    // 管理进程相关回调 ----------------------------------------------------------------------
    public function onWorkerStart($server, $id){
    }

    public function onStart($server){
        //设置默认时区
        date_default_timezone_set(config('app.timezone') ?? 'Asia/Shanghai');
        //设置进程名称
        $this->set_process_title($this->name . '-master');
        Hook::getInstance()->listen("start",$this->name,$this->config['ip'],$this->config['port']);
    }

    // Http相关回调 ----------------------------------------------------------------------
    public function onRequest($request,$response)
    {
        if($this->config['set']['enable_static_handler'] && $request->server['request_uri'] == '/favicon.ico'){
            return ;
        }
        register_shutdown_function('handleFatal',$request,$response);
        App::getInstance()->http ($request,$response);
    }

    // Websocket相关回调 ----------------------------------------------------------------------
    public function onOpen()
    {
    }

    public function onMessage( $server ,$frame )
    {
        App::getInstance()->websocket($server,$frame);
    }

    public function onClose($server,$fd){
    }

    // Task进程相关回调 ----------------------------------------------------------------------

    /**
     * @author woann<304550409@qq.com>
     * @param $server
     * @param $task_id
     * @param $workder_id
     * @param $data
     * @return mixed
     * @des 投递任务
     */
    public function onTask($server,$task_id,$workder_id,$data){
        return Task::getInstance()->setServer($server)->dispatch ($task_id,$workder_id,$data);
    }

    /**
     * @author woann<304550409@qq.com>
     * @param $server
     * @param $task_id
     * @param $data
     * @des 任务完成
     */
    public function onFinish($server,$task_id,$data){
        Task::getInstance()->setServer($server)->finish($task_id,$data);
    }

    public function set_process_title($title)
    {
        if(PHP_OS === 'Darwin')  return ;
        // >=php 5.5
        if (function_exists('cli_set_process_title')) {
            @cli_set_process_title($title);
        }else {
            @swoole_set_process_name($title);
        }
    }

}

