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

3. 项目根目录下执行 `php bin\light start` 启动服务

![image.png](https://upload-images.jianshu.io/upload_images/9160823-d5a075e73fd5faeb.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)

4. 浏览器访问`http://127.0.0.1:9521` 即可看到下图所示


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
