<?php
// +----------------------------------------------------------------------
// | Created by PhpStorm
// +----------------------------------------------------------------------
// | Date: 2018/12/8
// +----------------------------------------------------------------------
// | Blog: ( http://www.woann.cn )
// +----------------------------------------------------------------------
// | Author: woann <304550409@qq.com>
// +----------------------------------------------------------------------
return [
    'debug'     =>env("APP_DEBUG",true),
    'name'      => env("SERVER_NAME","Light-php"),                            //项目名称
    'namespace' => 'app',                               //项目命名空间
    'path'      => realpath (__DIR__.'/../app/'),  //项目所在路径
    'gzip'      => 0,                                    //gzip 等级， 请查看  https://wiki.swoole.com/wiki/page/410.html

    //server设置
    'ip'        => env("SERVER_HOST","0.0.0.0"),   //监听IP
    'port'      => env("SERVER_PORT",3306),        //监听端口
    'server'    => env("SERVER_TYPE","http"),     //服务，可选 websocket 默认http

    'set'       => [            //配置参数  请查看  https://wiki.swoole.com/wiki/page/274.html
        'daemonize'             => env("DAEMONIZE",0) ,
        'document_root'         => realpath (__DIR__.'/../static/') ,
        'worker_num'            => function_exists('\swoole_cpu_num') ? \swoole_cpu_num() * 2 : 8,
        'max_request'           => 10000,
        'task_worker_num'       => function_exists('\swoole_cpu_num') ? \swoole_cpu_num() * 2 : 8,
        'pid_file'              => root_path('runtime/light.pid'),
        'document_root' => root_path('resources/'),
        'enable_static_handler' => true,
        'log_file'              => root_path("logs/swoole/".date('Ymd').'.log'),
        'log_level'             => 5,
        'enable_coroutine'      => true
    ],

    'log' => [
        //输出到屏幕，当 set.daemonize = false 时，该配置生效，
        'echo'  => 0 ,
        // 日志保存目录
        'path'  => LOG_PATH,
        // 日志记录级别，共8个级别
        'level' => ['EMERGENCY','ALERT','CRITICAL','ERROR','WARNING','NOTICE','INFO','DEBUG','SQL'] ,
    ] ,
    'version' => 'v1.0.8',

];