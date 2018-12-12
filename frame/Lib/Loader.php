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
namespace Lib;

class Loader
{
    // 类名映射
    protected static $map = [] ;
    //命名空间映射
    protected static $namespaces = [] ;

    public static function register(){
        spl_autoload_register ('\\Lib\\Loader::autoload',true , true );
        self::addNamespace ('Lib',__DIR__.'/');
    }
    public static function autoload($class){
        if($file = self::find($class)){
            include $file;
            return true;
        }
    }

    //查找文件，并映射到$map
    private static function find($class){
        if(!empty(self::$map[$class])){   //如果已存在就直接返回
            return self::$map[$class];
        }
        //下面就是找啊找。。
        $classes = array_filter(explode ('\\',$class ));
        $namespace = array_shift ($classes);
        $logicalPath  = join (DIRECTORY_SEPARATOR ,$classes) .'.php';

        if(isset(self::$namespaces[$namespace]) ){  // 如果命名空间已注册，那就往下找。
            $dir = self::$namespaces[$namespace] ;
            if(is_file ($path = $dir.$logicalPath)){
                self::$map[$class] = $path;
                return $path;
            }
        }
        return  false;
    }

    // 注册 类
    public static function addMap($class , $map = ''){
        self::$map[$class] = $map ;
    }
    // 注册命名空间
    public static function addNamespace($namespace,$path=''){
        self::$namespaces[$namespace] = rtrim($path,'/').DIRECTORY_SEPARATOR;
    }
}
