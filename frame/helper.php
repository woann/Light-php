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
use Xiaoler\Blade\FileViewFinder;
use Xiaoler\Blade\Factory;
use Xiaoler\Blade\Compilers\BladeCompiler;
use Xiaoler\Blade\Engines\CompilerEngine;
use Xiaoler\Blade\Filesystem;
use Xiaoler\Blade\Engines\EngineResolver;
use Philo\Blade\Blade;
/**
 * @author woann<304550409@qq.com>
 * @param $class
 * @return mixed
 * @des 获取某类实例
 */
function get_instance($class){
    return ($class)::getInstance();
}

/**
 * @author woann<304550409@qq.com>
 * @param $name
 * @param null $default
 * @return mixed
 * @des 获取配置参数
 */
function config($name,$default = NULL){
    return get_instance('\Lib\Config')->get($name,$default);
}

/**
 * @author woann<304550409@qq.com>
 * @param $path
 * @return string
 * @des 项目根路径查找文件
 */
function root_path($path)
{
    return dirname(__DIR__).'/'.$path;
}

/**
 * @author woann<304550409@qq.com>
 * @param $type
 * @param mixed ...$log
 * @des 写日志
 */
function logs($type,...$log)
{
    get_instance('\Lib\Log')->write($type,...$log);
}

/**
 * @author woann<304550409@qq.com>
 * @param $request
 * @param $response
 * @des 错误处理函数
 */
function handleFatal($request,$response)
{
    $error = error_get_last();
    $res = \Lib\Error::systemError("系统错误！",$error["message"]);
    $response->end($res);
}

/**
 * @author woann<304550409@qq.com>
 * @param $view
 * @param array $param
 * @return mixed
 * @des 调用blade模板引擎渲染页面
 */
function view($view,array $param = [])
{
    $views = root_path('resources/views');
    $cache = root_path('runtime/cache');
    $blade = new Blade($views, $cache);
    return $blade->view()->make($view, $param)->render();
}

function uploadFile($file,$path,$filename)
{
    $up = new \Lib\FileUpload();
    $up->set('path',root_path('resources/uploads'))
        ->set('size',1)
        ->set('allowtype',array('gif','jpg','png'))
        ->set('israndname',true);
    $res = $up->upload($file,$path,$filename);
    unset($up);
    return $res;

}
