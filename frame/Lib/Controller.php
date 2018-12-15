<?php
// +----------------------------------------------------------------------
// | Created by PhpStorm
// +----------------------------------------------------------------------
// | Date: 18-12-10 上午10:08
// +----------------------------------------------------------------------
// | Author: woann <304550409@qq.com>
// +----------------------------------------------------------------------
namespace Lib;

use Xiaoler\Blade\FileViewFinder;
use Xiaoler\Blade\Factory;
use Xiaoler\Blade\Compilers\BladeCompiler;
use Xiaoler\Blade\Engines\CompilerEngine;
use Xiaoler\Blade\Filesystem;
use Xiaoler\Blade\Engines\EngineResolver;
class Controller
{
    /**
     * @var \Piz\Router
     */
    protected $router;
    /**
     * @var swoole_http_server->request
     */
    protected $request;
    /**
     * @var swoole_http_server->response
     */
    protected $response;
    /**
     * @var swoole_server
     */
    protected $server;
    /**
     * @var swoole_server->task
     */
    protected $task;

    /**
     * @author woann<304550409@qq.com>
     * @param array $array
     * @param null $callback
     * @return false|string
     * @des 渲染json
     */
    final public function json($array=array(),$callback=null){
        $this->gzip ();
        $this->response->header('Content-type','application/json');
        $json = json_encode($array);
        $json = is_null($callback) ? $json : "{$callback}({$json})" ;
        return  $json;
    }

    /**
     * @author woann<304550409@qq.com>
     * @param $view
     * @param array $param
     * @return mixed
     * @des 调用blade 模板引擎渲染页面
     */
    final public function view($view,array $param = [])
    {
        $views = root_path('resources/views');
        $cache = root_path('runtime/cache');
        $blade = new Blade($views, $cache);
        return $blade->view()->make($view, $param)->render();
    }

    /**
     * 启用Http GZIP压缩
     * $level 压缩等级，范围是1-9
     */
    final public function gzip($level = NULL  ){
        if($level === NULL ){
            $level = Config::getInstance()->get('app.gzip',0);
        }
        $level>0 && $this->response->gzip( $level);
    }

    public function __set($name,$object){
        $this->$name = $object;
    }

    public function __get($name){
        return $this->$name;
    }

}