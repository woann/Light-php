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
    if (isset($error['type']))
    {
        switch ($error['type'])
        {
            case E_ERROR :
            case E_PARSE :
            case E_CORE_ERROR :
            case E_COMPILE_ERROR :
                $message = $error['message'];
                $file = $error['file'];
                $line = $error['line'];
                $log = "$message ($file:$line)";
                if (isset($request->server['request_uri']))
                {
                    $log .= '<br>[QUERY] ' . $request->server['request_uri'];
                }
                $info = str_replace("#","<br>#",$log);
                if(!empty(ob_get_contents ())) ob_end_clean ();
//                ob_start();
                $content = \Lib\Exception::render('发现了一些错误',$info);
    //            $content = ob_get_contents();
    //            ob_end_clean();
                $response->end($content);
            default:
                break;
        }
    }

}

/**
 * @author woann<304550409@qq.com>
 * @param $view
 * @param array $param
 * @return string
 * @throws Throwable
 * @des 调用blade模板引擎渲染页面
 */
function view($view,array $param = [])
{
    $path = [root_path('resources/views'), root_path('frame/Static')];
    $cachePath = root_path('runtime/cache');

    $file = new Filesystem;
    $compiler = new BladeCompiler($file, $cachePath);

// you can add a custom directive if you want
    $compiler->directive('datetime', function($timestamp) {
        return preg_replace('/(\(\d+\))/', '<?php echo date("Y-m-d H:i:s", $1); ?>', $timestamp);
    });

    $resolver = new EngineResolver;
    $resolver->register('blade', function () use ($compiler) {
        return new CompilerEngine($compiler);
    });

// get an instance of factory
    $factory = new Factory($resolver, new FileViewFinder($file, $path));

// if your view file extension is not php or blade.php, use this to add it
    $factory->addExtension('tpl', 'blade');

// render the template file and echo it
    return $factory->make($view, $param)->render();
}

