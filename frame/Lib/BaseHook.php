<?php
// +----------------------------------------------------------------------
// | Created by PhpStorm
// +----------------------------------------------------------------------
// | Date: 18-12-10 下午12:14
// +----------------------------------------------------------------------
// | Author: woann <304550409@qq.com>
// +----------------------------------------------------------------------
namespace Lib;
class BaseHook{
    protected static $instance;

    private function __construct ()
    {
    }

    public static function getInstance(){
        if(is_null (self::$instance)){
            $child_class = get_called_class();
            self::$instance = new $child_class();
        }
        return self::$instance;
    }
}