> 🚀`Light-php`是一个基于swoole的高性能php框架，轻量的封装和易用性，使它在中小型高性能项目中有着出色的表现。

[![Latest Stable Version](https://poser.pugx.org/woann/Light-php/v/stable.svg)](https://woann.cn)
[![Latest Unstable Version](https://poser.pugx.org/woann/Light-php/v/unstable.svg)](https://woann.cn)
[![Total Downloads](https://poser.pugx.org/woann/Light-php/downloads.svg)](https://woann.cn)
[![License](https://poser.pugx.org/woann/Light-php/license.svg)](https://github.com/woann/Light-php/blob/master/LICENSE)
[![Php Version](https://img.shields.io/badge/php-%3E=7.2-brightgreen.svg?maxAge=2592000)](https://secure.php.net/)
[![Wwoole Version](https://img.shields.io/badge/swoole-%3E=4.2.9-brightgreen.svg?maxAge=2592000)](https://laravel.com/)

## 简单描述
* 路由
* 中间件
* 控制器
* 采用laravel框架的ORM(Eloquent ORM)
* 采用laravel框架的Blade模板引擎
* redis操作
* 框架大部分使用单例模式
## 安装教程
* 前提：安装swoole扩展，参考完整的[https://wiki.swoole.com/](https://wiki.swoole.com/)
* 执行安装命令 `composer create-project woann/light-php` 或者`git clone https://github.com/woann/Light-php.git` 
* 重命名`.env.example`文件为`.env`,并配置
* 项目根目录下执行 `php bin\light start` 启动服务
![image.png](https://upload-images.jianshu.io/upload_images/9160823-d5a075e73fd5faeb.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)
* 浏览器访问`http://127.0.0.1:9521`
* 暂时没有时间写文档，感兴趣的同学可以看看源代码...

## 压力测试
* 调用框架内一个json输出方法，输出如下内容：
```json
{
    "word": "hello world"
}
```
![image.png](https://upload-images.jianshu.io/upload_images/9160823-3ab2b3c662fb7ba6.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)

* 方法内有一条查询语句的压力测试
```php
 public function index(){
        $res = DB::table('user')->where('id',"=","1")->first();
        return $this->json($res);
    }
```
![image.png](https://upload-images.jianshu.io/upload_images/9160823-d79e85afedbcab85.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)
