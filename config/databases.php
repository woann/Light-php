<?php
// +----------------------------------------------------------------------
// | Created by PhpStorm
// +----------------------------------------------------------------------
// | Date: 18-12-11 下午2:27
// +----------------------------------------------------------------------
// | Author: woann <304550409@qq.com>
// +----------------------------------------------------------------------
return [
    'db_connection' => env("DB_CONNECTION","mysql"),
    'db_host' => env("DB_HOST","127.0.0.1"),
    'db_port' => env("DB_PORT","3306"),
    'db_database' => env("DB_DATABASE","tes1t"),
    'db_username' => env("DB_USERNAME","root"),
    'db_password' => env("DB_PASSWORD","root"),
    'db_prefix' => env("DB_PREFIX",""),
    'db_charset' => env("DB_CHARSET","utf8mb4"),
    'db_collation' => env("DB_COLLATION","utf8mb4_unicode_ci"),
];