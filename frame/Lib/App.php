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

class App{
    //单例模式
    private static $instance;
    //映射表
    private static $map = [];
    //将构造方法私有化
    private function __construct()
    {
    }

    /**
     * @Author woann <304550409@qq.com>
     * @return App
     * @description 获取实例
     */
    public static function getInstance()
    {
        if (self::$instance == null){
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function http($server,$request,$response){
//        var_dump($request);
        $req    = Request::getInstance ();
        $req->set($request);
        $router = Router::getInstance ()->http($req->server['request_uri']);
        $app_namespace  = Config::getInstance ()->get('app.namespace');
        $module         = $router['m'] ;
        $controller     = $router['c'] ;
        $action         = $router['a'] ;
        $param          = $router['p'] ;
        $middleware     = $router['middleware'];
        $method         = $router['method'];
        $classname      = "\\{$app_namespace}\\Controllers\\{$module}\\{$controller}" ;
        //如果请求方法不是ANY(任何请求方式)，并且不匹配路由中定义的请求方式，则返回405
        if($method != $request->server['request_method'] && $request->server['request_method'] != 'ANY') {
            //如果控制器不存在
            $response->status(405);
            $content = Error::systemError("405",'请求方法不被允许！');
            $response->end($content);
            return ;
        }
        if (!class_exists($classname)) {
            //如果控制器不存在
            $response->status(404);
            $content = Error::systemError("404",'请求的页面不存在！');
            $response->end($content);
            return ;
        }
        if(!isset(self::$map[$classname])){
            $class      = new $classname ;
            if(get_parent_class ($class) != 'Lib\Controller'){
                $response->status(503);
                $content = Error::systemError("503",'控制器必须继承 Lib\Controller！');
                $response->end($content);
                return ;
            }
            self::$map[$classname]         = $class;
        }
        if (!method_exists(self::$map[$classname],$action)) {
            $response->status(404);
            $content = Error::systemError("404",'请求的页面不存在！');
            $response->end($content);
            return ;
        }
        if ($middleware) {
            try{
                $middleware_class = "\\app\\Middleware\\$middleware";
                $middleware_obj = new $middleware_class;
                if(!empty(ob_get_contents ())) ob_end_clean ();
                ob_start();
                $middleware_res = $middleware_obj->handle($request);
                $middleware_content = ob_get_contents();
                ob_end_clean();
                if ($middleware_res !== true) {
                    if ( $middleware_res ) {
                        $response->end($middleware_res);
                    } else {
                        $response->end($middleware_content);
                    }
                    return;
                }
            }catch(\Exception $exception){
                Error::exceptionError("捕获到异常！",$exception,$response);
            }
        }
        try{
            if(!empty(ob_get_contents ())) ob_end_clean ();
            ob_start();
            self::$map[$classname]->response = $response;
            self::$map[$classname]->request = $req;
            self::$map[$classname]->server = $server;
            $result = self::$map[$classname]->$action($param);
            $content = ob_get_contents();
            ob_end_clean();
            if ($result) {
                $response->end($result);
            }else{
                $response->end($content);
            }
        }  catch (\Exception $exception) {
            Error::exceptionError("捕获到异常！",$exception,$response);
        }

    }

    public function websocket($server,$frame)
    {
        $router = $router = Router::getInstance ()->websocket( $frame ->data );

        $app_namespace  = Config::getInstance ()->get('app.namespace');
        $module         = $router['m'] ;
        $controller     = $router['c'] ;
        $action         = $router['a'] ;
        $param          = $router['p'] ;

        $classname = "\\{$app_namespace}\\Controllers\\{$module}\\{$controller}";
        if ( ! isset( self ::$map[ $classname ] ) ) {
            try{
                $class = new $classname;
                if(get_parent_class ($class) != 'Lib\WsController'){
                    Log::getInstance()->write("ALERT","[{$classname}]  必须继承 Lib\WsController",PHP_EOL);
                    return ;
                }
                self ::$map[ $classname ] = $class;
            }catch (\Exception $e){
                Log::getInstance()->write("ALERT",$e->getMessage (),PHP_EOL);
                return ;
            }
        }
        try{
            self::$map[$classname]->server = $server;
            self::$map[$classname]->fd = $frame->fd;
            self::$map[$classname]->param = $param;
            self::$map[$classname]->task = Task::getInstance()->setServer($server);
            self::$map[$classname]->$action($param);
        }catch(\Exception $e){
            Log::getInstance()->write("ALERT",$e->getMessage (),PHP_EOL);
            return ;
        }
    }
}