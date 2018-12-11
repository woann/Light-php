<?php
// +----------------------------------------------------------------------
// | Created by PhpStorm
// +----------------------------------------------------------------------
// | Date: 18-12-10 上午9:51
// +----------------------------------------------------------------------
// | Author: woann <304550409@qq.com>
// +----------------------------------------------------------------------
namespace Lib;

final class Task
{
    /**
     * 实例
     * @var object
     */
    private static $instance ;

    public $server ;

    private function __construct (){}

    final public static function getInstance(){
        if (self::$instance == null) {
            self::$instance = new self();

        }
        return self::$instance;
    }

    final public function setServer($server){
        self::$instance->server = $server;
        return self::$instance;
    }

    /**投递任务
     * @param $class        \app\task\classname
     * @param $func
     * @param $params       []
     *
     * @return swoole_server->task_id
     */
    final public function delivery($class,$func,$params=[]){
        $task_id =  $this->server->task([$class,$func,$params]);
        echo "投递任务","\t","TaskID:{$task_id},","Class:{$class}","Func:{$func}","Params:".join (",",$params),PHP_EOL;
        return $task_id;
    }

    /**
     * 调度任务
     */
    final public function dispatch($task_id,$workder_id,$data){
        $ret = NULL ;
        if(empty($data)){
            echo "任务内容不合法","\t","TaskID:{$task_id}","任务内容不合法,必须传递数据，格式 [classname,function,params]",PHP_EOL;
            return FALSE;
        }
        list($classname , $func,$params) = $data ;
        try{
            $class =  (new $classname);
            $class->server = $this->server;
            return $class->$func(...$params);
        }catch (\Exception $e){
            return $e->getMessage ();
        }
    }

    /**
     * 完成任务
     */
    final public function finish($task_id,$data){
        echo "TaskID:{$task_id}\t{$data}",PHP_EOL;
    }

    public function __get($name){
        return $this->$name;
    }
}
