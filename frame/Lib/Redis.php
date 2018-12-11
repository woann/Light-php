<?php
// +----------------------------------------------------------------------
// | Created by PhpStorm
// +----------------------------------------------------------------------
// | Date: 18-12-10 下午12:04
// +----------------------------------------------------------------------
// | Author: woann <304550409@qq.com>
// +----------------------------------------------------------------------
namespace Lib;
class Redis
{
    private static $instance;
    private function __construct ()
    {
        try{
            self::$instance = new \Redis();
            self::$instance->pconnect (config('redis.host'),config ('redis.port'));
            if(config ('redis.passwd','')!=''){
                self::$_instance->auth(config ('redis.passwd'));
            }
            self::$instance->select(config ('redis.db'));
            Log::getInstance()->write ("INFO","REDIS","已连接",config('redis.host').":".config ('redis.port'));
        }catch (\RedisException $e){
            self::$instance = NULL ;
            Log::getInstance()->write ("INFO","REDIS",$e->getMessage ());
        }
    }

    public static function getInstance(){
        if(is_null (self::$instance)){
            new self();
        }
        return self::$instance;
    }

    public function __call($method ,$args=NULL){
        $this->handle->$method(...$args);
    }
}
