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

class Router
{
    /**
     * @var 实例
     */
    private static $instance;
    /**
     * @var array 配置
     */
    private static $config;
    private function __construct() {}

    /**
     * @Author woann <304550409@qq.com>
     * @return Router|实例
     * @description 获取路由对象实例
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new self();
            self::$config = Config::getInstance()->get('route');
        }
        return self::$instance;
    }

    /**
     * @Author woann <304550409@qq.com>
     * @param $request_uri 请求路径
     * @return array
     * @description
     */
    public function http($request_uri){
        $param      = [];
        $module     = self::$config['m'];
        $controller =  self::$config['c'];
        $action     =  self::$config['a'];
        //如果是空路径，则解析到默认
        if(empty($request_uri)) {
            return ['m'=>$module ,'c'=>$controller,'a'=>$action,'p'=>$param];
        }

        //去掉uri左边的"/"
        if ($request_uri != '/') {
            $path = trim($request_uri, '/');
        }else{
            $path = $request_uri;
        }
        if(!empty( self::$config['ext']) &&substr($path,-strlen(self::$config['ext'])) == self::$config['ext'] ){
            $path = substr($path , 0 , strlen($path)-strlen(self::$config['ext']));
        }
        $method = "";
        $middleware = "";
        //如果自定义路由不为空
        if (!empty(self::$config['http']) ) {
            //遍历自定义路由
            foreach (self::$config['http'] as $key => $value) {
                if(substr($path,0,strlen($key)) == $key) {
                    $path = str_replace($key, $value[1], $path);
                    $middleware = $value[2] ?? "";
                    $method = $value[0];
                    break;
                }
            }
        }
        $param = explode( "/" , $path);
        !empty($param[0]) && $module = $param[0];
        isset($param[1]) && $controller = $param[1];
        isset($param[2]) && $action = $param[2];

        if(count($param)>=3){
            $param = array_slice($param, 3);
        }else{
            $param = array_slice($param, 2);
        }
        return ['m'=>ucfirst($module) ,'c'=>ucfirst($controller).'Controller','a'=>$action,'p'=>$param,'middleware' => $middleware,'method' => $method];
    }

    public function websocket($data) {

        $data = json_decode ($data , true );
        if(empty($data)){
            echo 'WEBSOCKET-json解包错误',PHP_EOL;
            return ['m'=>NULL ,'c'=>NULL,'a'=>NULL ,'p' =>  NULL] ;
        }

        $path = empty($data['route']) ? '' : trim($data['route'], '/');

        if(empty($path)){
            echo '请求地址错误',PHP_EOL;
            return ['m'=>NULL ,'c'=>NULL,'a'=>NULL ,'p' =>  NULL] ;
        }

        if (!empty(self::$config['websocket']) && isset(self::$config['websocket'][$path])) {
            $path =  self::$config['websocket'][$path];
        }

        $param = explode( "/" , $path);

        $module     =   array_shift ($param);
        $controller =   array_shift ($param);
        $action     =   array_shift ($param);
        unset($data['route']);
        return ['m'=>ucfirst($module) ,'c'=>ucfirst($controller.'Controller'),'a'=>$action ,'p' =>  $data] ;
    }
}
