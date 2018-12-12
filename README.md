> 🚀`Light-php`是一个基于swoole的高性能php框架，轻量的封装和易用性，使它在中小型高性能项目中有着出色的表现。

[![Latest Stable Version](https://poser.pugx.org/woann/Light-php/v/stable.svg)](https://packagist.org/packages/woann/light-php)
[![Latest Unstable Version](https://poser.pugx.org/woann/Light-php/v/unstable.svg)](https://packagist.org/packages/woann/light-php)
[![Total Downloads](https://poser.pugx.org/woann/Light-php/downloads.svg)](https://packagist.org/packages/woann/light-php)
[![License](https://poser.pugx.org/woann/Light-php/license.svg)](https://github.com/woann/Light-php/blob/master/LICENSE)
[![Php Version](https://img.shields.io/badge/php-%3E=7.2-brightgreen.svg?maxAge=2592000)](https://secure.php.net/)
[![Swoole Version](https://img.shields.io/badge/swoole-%3E=4.2.9-brightgreen.svg?maxAge=2592000)](https://www.swoole.com/)

## 环境要求

| 依赖 | 说明 |
| -------- | -------- |
| [PHP](https://secure.php.net/manual/zh/install.php) | `>= 7.2` `推荐7.2` |
| [Swoole](https://www.swoole.com/) | `>= 4.2.9` `从2.0.12开始不再支持PHP5` `推荐4.2.9+` |
| [Linux](https://www.linux.org/) | `大部分的linux系统都可以` `推荐centos` |

## 安装教程

1-1.通过[Composer](https://getcomposer.org/)安装([packagist](https://packagist.org/packages/woann/light-php)),此方式安装版本可能不是最新的，出现此问题请用1-2方式安装。
```bash
composer create-project woann/light-php -vvv
```

1-2.通过[Git](https://git-scm.com/)安装。
```bash
git clone https://github.com/woann/Light-php.git
```

2.重命名`.env.example`文件为`.env`,并配置

3.项目根目录下执行 `php bin\light start` 启动服务

![image.png](https://upload-images.jianshu.io/upload_images/9160823-d5a075e73fd5faeb.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)

4.浏览器访问`http://127.0.0.1:9521` 即可看到`hello world`的输出。至此，框架就安装完成了。

## 配置文件
1.Light-php的配置文件在`/config`目录下，框架集成了全局环境配置文件`/.env`,常规配置都在.env文件中进行配置。

2.`/config/app.php`,框架主要配置文件主要用来配置`swoole`扩展相关参数。

3.`/config/databases.php`,数据库配置文件，配置了数据库连接相关参数。

4.`/config/hook.php`,配置钩子(钩子主要用来将业务逻辑和通知服务分离)。

5.`/config/redis.php`,`redis`配置文件，配置了`redis`连接相关参数。

6.`/config/route.php`,路由配置文件。

7.以上配置文件具体参数意义在代码中都有注释，这里不做更多介绍

## 路由

以下是一个路由示例(注意：路由中，控制器参数为控制器的简写，实际控制器文件应在后追加`Controller`)
```php
return [
    'm'             => 'index',    //默认模块
    'c'             => 'index',    //默认控制器
    'a'             => 'init',     //默认操作
    'ext'           => '.html',    //伪静态后缀    例如 .html
    'http'          =>  [          //http路由
        //uri-----请求方法----模块/控制器/方法----[中间件]
        '/'     => ['GET','Index/Index/index','Test'],
        'test/'    => ['GET','Index/Index/ws']
    ],
    'websocket'     =>  [           //websocket路由
        //uri----模块/控制器/方法
        'ws' => 'Index/WebSocket/index',
    ]
];
```

## 中间件
中间件文件应建立在`/app/Middleware`目录下，类名与文件名要一致，并实现`Lib\Middleware`接口，中间件处理方法名必须为`handle`,过滤后如果通过最终返回结果必须为`true`。示例：
```php
<?php
namespace app\Middleware;

use Lib\Middleware;
class Test implements Middleware{
    public function handle($request)
    {
        //在此处理中间件判断逻辑，
        //...

        //程序最后通过验证后，返回true;
        return true;
    }
}
```
## 控制器
中间件文件应建立在`/app/Controller`目录下，类名与文件名要一致，必须继承`Lib\Controller`类，示例：
```php
<?php
namespace app\Controllers\Index;

use Lib\Controller;

class IndexController extends Controller {
    //普通输出
    public function index()
    {
        return 'hello world';
    }
    
    //输出json
    public function index1()
    {
        return $this->json(["code" => 200, "msg" => "success"]);
    }
    
    //调用模板
     public function index2()
    {
        $a = "test";
        //输出/app/resources/views目录下index.blade.php模板，并携带参数$a。支持用 . 拼接模板路径（和laravel中模板引擎部分一样）
        return $this->view("index",["a" => $a]);
        //也可以直接调用view函数
        return view("admin.index",["a" => $a]);
    }
    
}
```

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
