<?php
// +----------------------------------------------------------------------
// | Created by PhpStorm
// +----------------------------------------------------------------------
// | Date: 18-12-11 下午1:15
// +----------------------------------------------------------------------
// | Author: woann <304550409@qq.com>
// +----------------------------------------------------------------------
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
$config = config("databases");
$database = [
    'driver'        => $config['db_connection'],
    'host'          => $config['db_host'],
    'database'      => $config['db_database'],
    'username'      => $config['db_username'],
    'password'      => $config['db_password'],
    'charset'       => $config['db_charset'],
    'collation'     => $config['db_collation'],
    'prefix'        => $config['db_prefix'],
    'strict'        => true
];
$capsule = new Capsule;
// 创建链接
$capsule->addConnection($database);
// 设置全局静态可访问DB
$capsule->setAsGlobal();
// 启动Eloquent
$capsule->bootEloquent();