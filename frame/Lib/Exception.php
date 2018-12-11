<?php
// +----------------------------------------------------------------------
// | Created by PhpStorm
// +----------------------------------------------------------------------
// | Date: 18-12-10 下午4:26
// +----------------------------------------------------------------------
// | Author: woann <304550409@qq.com>
// +----------------------------------------------------------------------
namespace Lib;
class Exception{
    private static $instance;
    private function __construct()
    {
    }

    public function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function render($title, $info)
    {
        return view('error',['title'=>$title, 'info'=>$info]);
    }
}