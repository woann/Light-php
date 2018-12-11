<?php
// +----------------------------------------------------------------------
// | Created by PhpStorm
// +----------------------------------------------------------------------
// | Date: 18-12-10 上午11:14
// +----------------------------------------------------------------------
// | Author: woann <304550409@qq.com>
// +----------------------------------------------------------------------
namespace Lib;

class Log
{
    /**
     * 实例
     * @var object
     */
    private static $instance ;
    /**
     * 配置参数
     * @var array
     */
    private static $config = [] ;

    private function __construct ()
    {
    }

    public static function getInstance(){
        if(is_null (self::$instance)){
            self::$config = Config::getInstance ()->get('app.log');
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @author woann<304550409@qq.com>
     * @param $type
     * @param mixed ...$logs
     * @return bool
     * @des 写入日志
     */
    public function write($type,...$logs){
        $type = strtoupper ($type);
        $msg = "{$type} \t ".date("Y-m-d h:i:s")." \t ".join (" \t ",$logs);
        if( !in_array($type,self::$config['level'])) return false;
        if(self::$config['echo']){
            echo $msg,PHP_EOL;
        }
        $this->save($type,$msg);
    }

    public function save($type,$msg){
        $dir_path = LOG_PATH.date('Ym').DIRECTORY_SEPARATOR;
        !is_dir($dir_path) && mkdir($dir_path,0777);
        $filename  = date("d").'.'.$type.'.log';
        swoole_async_writefile($dir_path.$filename , $msg, NULL, FILE_APPEND);
        return true;
    }

}
