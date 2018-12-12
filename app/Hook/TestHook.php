<?php
// +----------------------------------------------------------------------
// | Created by PhpStorm
// +----------------------------------------------------------------------
// | Date: 18-12-10 下午12:11
// +----------------------------------------------------------------------
// | Author: woann <304550409@qq.com>
// +----------------------------------------------------------------------
namespace app\Hook;

use Lib\BaseHook;
use Lib\Log;
class TestHook extends BaseHook {

    public function start($name,$ip,$port)
    {
        //当server启动时执行此钩子
        Log::getInstance()->write('INFO',$name,"启动成功","{$ip}:{$port}","at",date('Y-m-d H:i:s').PHP_EOL);
    }

    public function open($server,$fd)
    {
        //可以在此执行websocket链接成功后绑定用户id和fd的操作
    }

    public function close($server,$fd)
    {
        //可以在此执行websocket关闭链接后解绑用户id和fd的操作
    }
}