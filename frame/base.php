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
//定义框架路径
define ('FRAME_PATH',__DIR__.'/');
//Config文件目录
define ('CONFIG_PATH',dirname (__DIR__).'/config/');
//Logs路径
define('LOG_PATH',dirname (__DIR__).'/logs/custom/');

//引入助手文件
require_once FRAME_PATH."helper.php";
//composer自动加载
require_once root_path('vendor/autoload.php');
//环境配置
$dotenv = new \Dotenv\Dotenv(dirname(__DIR__));
$dotenv->load();

//引入加载器文件
require_once FRAME_PATH."Lib/Loader.php";
//注册它
\lib\Loader::register ();
//引入orm初始化文件
require_once FRAME_PATH."orm.php";

class start {
    /**
     * 配置参数 config/app.php
     * @var array
     */
    private static $config = null ;

    /**
     * frame/Lib/Server.php 实例
     * @var null
     */
    protected static $worker = null ;

    public static function run($opt = NULL){

        if (version_compare(phpversion(), '7.1', '<')) {
            echo "PHP版本必须大于等于7.1 ，当前版本：",phpversion (),PHP_EOL;
            die;
        }

        if (version_compare(phpversion('swoole'), '4.2.9', '<')) {
            echo "Swoole 版本必须大于等于 4.2.9 ，当前版本：",phpversion ('swoole'),PHP_EOL;
            die;
        }
        if (php_sapi_name() != "cli") {
            echo "仅允许在命令行模式下运行",PHP_EOL;
            die;
        }
        //检查命令
        if(!in_array ($opt , ['start','stop','kill','restart','reload'])){
            echo PHP_EOL,"请使用:",PHP_EOL,"     php bin/light [start|stop|kill|restart|reload]",PHP_EOL,PHP_EOL;
            die;
        }

        self::$config = config('app');
        //注册项目命名空间和路径
        Lib\Loader::addNamespace (config('app.namespace'),config('app.path'));
        //检查日志目录是否存在并创建
        !is_dir(LOG_PATH) && mkdir(LOG_PATH,0777 ,TRUE);
        //检查是否配置app.name
        if(empty(self::$config['name'])){
            echo "配置项 config/app.php [name] 不可留空 ",PHP_EOL;
            die;
        }

        $app_name = self::$config['name'];

        //获取master_pid 关闭或重启时要用到
        $master_pid = exec("ps -ef | grep {$app_name}-master | grep -v 'grep ' | awk '{print $2}'");
        //获取manager_pid 重载时要用到
        $manager_pid = exec("ps -ef | grep {$app_name}-manager | grep -v 'grep ' | awk '{print $2}'");

        if (empty($master_pid)) {
            $master_is_alive = false;
        } else {
            $master_is_alive = true;
        }

        if ($master_is_alive) {
            if ($opt === 'start' ) {
                echo "{$app_name}  正在运行" , PHP_EOL;
                exit;
            }
        } elseif ($opt !== 'start' ) {
            echo "{$app_name} 未运行" , PHP_EOL;
            exit;
        }

        switch ($opt){
            case 'start':
                break;
            case "kill":
                //代码参考 https://wiki.swoole.com/wiki/page/233.html
                exec("ps -ef|grep {$app_name}|grep -v grep|cut -c 9-15|xargs kill -9");
                break;

            case 'stop':
                echo "{$app_name}  正在停止 [{$master_pid}] ..." , PHP_EOL;
                // 发送SIGTERM信号，主进程收到SIGTERM信号时将停止fork新进程，并kill所有正在运行的工作进程
                // 详见 https://wiki.swoole.com/wiki/page/908.html
                $master_pid && posix_kill($master_pid, SIGTERM);
                // Timeout.
                $timeout = 10;
                $start_time = time();

                while (1) {                           //强制退出
                    $master_is_alive = $master_pid && posix_kill($master_pid, 0);
                    if ($master_is_alive) {
                        if (time() - $start_time >= $timeout) {
                            echo "{$app_name} 停止失败" , PHP_EOL;
                            exit;
                        }
                        usleep(10000);
                        continue;
                    }
                    echo "{$app_name} 已停止" , PHP_EOL;
                    break;
                }
                exit(0);
                break;
            case 'reload':
                //详见：https://wiki.swoole.com/wiki/page/20.html
                // SIGUSR1: 向主进程/管理进程发送SIGUSR1信号，将平稳地restart所有worker进程
                posix_kill($manager_pid, SIGUSR1);
                echo "[SYS]","\t", "{$app_name} 重载" , PHP_EOL;
                exit;
            case 'restart':
                echo "{$app_name} 正在停止 ..." , PHP_EOL;
                // 发送SIGTERM信号，主进程收到SIGTERM信号时将停止fork新进程，并kill所有正在运行的工作进程
                // 详见 https://wiki.swoole.com/wiki/page/908.html
                $master_pid && posix_kill($master_pid, SIGTERM);
                $timeout = 40;
                $start_time = time();
                while (1) {
                    //检查master_pid是否存在
                    $master_is_alive = $master_pid && posix_kill($master_pid, 0);
                    if ($master_is_alive) {
                        if (time() - $start_time >= $timeout) {
                            echo "{$app_name} 停止失败" , PHP_EOL;
                            exit;
                        }
                        usleep(10000);
                        continue;
                    }
                    echo "{$app_name} 已停止" , PHP_EOL;
                    break;
                }

                break;
        }
        self::$worker = \Lib\Server::getInstance();
        self::$worker->set_config (self::$config);
        $server_type = self::$config["server"];
        $php_version = phpversion();
        $swoole_version = swoole_version();
        $light_version = self::$config["version"];
        $ip = self::$config["ip"];
        $port = self::$config["port"];
        echo <<<EOT
    
 _     ___ ____ _   _ _____   ____  _   _ ____  
| |   |_ _/ ___| | | |_   _| |  _ \| | | |  _ \ 
| |    | | |  _| |_| | | |   | |_) | |_| | |_) |
| |___ | | |_| |  _  | | |   |  __/|  _  |  __/ 
|_____|___\____|_| |_| |_|   |_|   |_| |_|_|    

SERVER_TYPE             {$server_type}
PHP_VERSION             {$php_version}
SWOOLE_VERSION          {$swoole_version}
LIGHT_VERSION           {$light_version}
Light: is running at {$ip}:{$port}

EOT;
        self::$worker->run();
    }
}
