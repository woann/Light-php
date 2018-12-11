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
class Request
{
    /**
     * 对象实例
     * @var object
     */
    private static $instance;
    /**
     * 私有化
     */
    private $server;
    private $header;
    private $request;
    private $post;
    private $get;
    private $cookie;
    private $files;
    private $tmpfiles;
    private $rawContent;
    private $getData;

    private function __construct (){}

    /**
     * @Author woann <304550409@qq.com>
     * @return Request|object
     * @description 获取实例
     */
    public static function getInstance(){
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    //先拿到$request，然后挨个给它变身
    public function set($request){
        $this->server       = $request->server;
        $this->header       = $request->header;
        $this->tmpfiles     = $request->tmpfiles;
        $this->request      = $request->request ;
        $this->cookie       = $request->cookie ;
        $this->get          = $request->get ;
        $this->files        = $request->files ;
        $this->post         = $request->post ;
        $this->rawContent   = $request->rawContent();
        $this->getData      = $request->getData();
    }
    /*
    // 以上变身方法也可以用魔术方法
    public function __set($name,$value){
        $this->$name = $value;
    }
    */
    //变身后它就不是废物了，那就得让小伙伴们能使用它，这里使用了这么一个魔术方法。
    public function __get($name){
        return $this->$name;
    }

    /**
     * @author woann<304550409@qq.com>
     * @param null $name
     * @return array|null
     * @des 获取get参数
     */
    public function get($name = null)
    {
        if (is_array($name) || $name == null) {
            $data = [];
            foreach ($this->get as $k=>$v){
                if ($name == null) {
                    if (in_array($k, $name)) {
                        $data[$k] = $v;
                    }else{
                        return null;
                    }
                }else{
                    $data[$k] = $v;
                }
            }
            return $data;
        }
        if (is_string($name)){
            return $this->get[$name] ?? null;
        }
    }

    /**
     * @author woann<304550409@qq.com>
     * @param null $name
     * @return array|null
     * @des 获取post参数
     */
    public function post($name = null)
    {
        if (is_array($name) || $name == null) {
            $data = [];
            foreach ($this->post as $k=>$v){
                if ($name == null) {
                    if (in_array($k, $name)) {
                        $data[$k] = $v;
                    }else{
                        return null;
                    }
                }else{
                    $data[$k] = $v;
                }
            }
            return $data;
        }
        if (is_string($name)){
            return $this->post[$name] ?? null;
        }

    }
}
