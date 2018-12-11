<?php
// +----------------------------------------------------------------------
// | Created by PhpStorm
// +----------------------------------------------------------------------
// | Date: 18-12-10 下午12:07
// +----------------------------------------------------------------------
// | Author: woann <304550409@qq.com>
// +----------------------------------------------------------------------
namespace Lib;

class Hook
{
    private static $instance;
    private static $config ;

    private function __construct ()
    {
    }

    public static function getInstance(){
        if(is_null (self::$instance)){
            self::$instance = new self();
            self::$config =  Config::getInstance ()->get("hook");
        }
        return self::$instance;
    }

    public function listen($hook , ...$args){
        $hooks = isset(self::$config[$hook]) ? self::$config[$hook] : [] ;
        while($hooks){
            list($class,$func) = array_shift ($hooks);
            try{
                $class::getInstance()->$func(...$args);
            }catch (\Exception $e){
                Log::getInstance ()->write ('ERROR',$e->getMessage ());
            }
        }
    }
}
