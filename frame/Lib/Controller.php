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
     * @return string
     * @throws \Throwable
     * @des 调用blade 模板引擎渲染页面
     */
    final public function view($view,array $param = [])
    {
        $path = [root_path('resources/views'), root_path('frame.Static')];
        $cachePath = root_path('runtime/cache');
        $file = new Filesystem;
        $compiler = new BladeCompiler($file, $cachePath);
        $compiler->directive('datetime', function($timestamp) {
            return preg_replace('/(\(\d+\))/', '<?php echo date("Y-m-d H:i:s", $1); ?>', $timestamp);
        });
        $resolver = new EngineResolver;
        $resolver->register('blade', function () use ($compiler) {
            return new CompilerEngine($compiler);
        });
        $factory = new Factory($resolver, new FileViewFinder($file, $path));
        $factory->addExtension('tpl', 'blade');
        return $factory->make($view, $param)->render();
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