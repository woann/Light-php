<?php
// +----------------------------------------------------------------------
// | Created by PhpStorm
// +----------------------------------------------------------------------
// | Date: 18-12-13 上午11:22
// +----------------------------------------------------------------------
// | Author: woann <304550409@qq.com>
// +----------------------------------------------------------------------
namespace Lib;
class Error {

    public static function systemError($title,$message) {

        list($showTrace, $logTrace) = self::debugBacktrace();
        return self::showError($title, "<li>$message</li>", $showTrace, 0);
    }

    /**
     * 代码执行过程回溯信息
     *
     * @static
     * @access public
     */
    public static function debugBacktrace() {
        $skipFunc[] = 'Error->debugBacktrace';

        $show = $log = '';
        $debugBacktrace = debug_backtrace();
        ksort($debugBacktrace);
        foreach ($debugBacktrace as $k => $error) {
            if (!isset($error['file'])) {
                // 利用反射API来获取方法/函数所在的文件和行数
                try {
                    if (isset($error['class'])) {
                        $reflection = new \ReflectionMethod($error['class'], $error['function']);
                    } else {
                        $reflection = new \ReflectionFunction($error['function']);
                    }
                    $error['file'] = $reflection->getFileName();
                    $error['line'] = $reflection->getStartLine();
                } catch (\Exception $e) {
                    continue;
                }
            }

            $file = $error['file'];
            $func = isset($error['class']) ? $error['class'] : '';
            $func .= isset($error['type']) ? $error['type'] : '';
            $func .= isset($error['function']) ? $error['function'] : '';
            if (in_array($func, $skipFunc)) {
                break;
            }
            $error['line'] = sprintf('%04d', $error['line']);

            $show .= '<li>[Line: ' . $error['line'] . ']' . $file . '(' . $func . ')</li>';
            $log .= !empty($log) ? ' -> ' : '';
            $log .= $file . ':' . $error['line'];
        }
        return array($show, $log);
    }

    /**
     * 异常处理
     *
     * @static
     * @access public
     * @param mixed $exception
     */
    public static function exceptionError($title,$exception,$response) {
        $errorMsg = $exception->getMessage();
        $trace = $exception->getTrace();
        krsort($trace);
        $trace[] = array('file' => $exception->getFile(), 'line' => $exception->getLine(), 'function' => 'break');
        $phpMsg = array();
        foreach ($trace as $error) {
            if (!empty($error['function'])) {
                $fun = '';
                if (!empty($error['class'])) {
                    $fun .= $error['class'] . $error['type'];
                }
                $fun .= $error['function'] . '(';
                if (!empty($error['args'])) {
                    $mark = '';
                    foreach ($error['args'] as $arg) {
                        $fun .= $mark;
                        if (is_array($arg)) {
                            $fun .= 'Array';
                        } elseif (is_bool($arg)) {
                            $fun .= $arg ? 'true' : 'false';
                        } elseif (is_int($arg)) {
                            $fun .= (defined('SITE_DEBUG') && SITE_DEBUG) ? $arg : '%d';
                        } elseif (is_float($arg)) {
                            $fun .= (defined('SITE_DEBUG') && SITE_DEBUG) ? $arg : '%f';
                        } else {
                            $fun .= (defined('SITE_DEBUG') && SITE_DEBUG) ? '\'' . htmlspecialchars(substr(self::clear($arg), 0, 10)) . (strlen($arg) > 10 ? ' ...' : '') . '\'' : '%s';
                        }
                        $mark = ', ';
                    }
                }
                $fun .= ')';
                $error['function'] = $fun;
            }
            if (!isset($error['line'])) {
                continue;
            }
            $phpMsg[] = array('file' =>  $error['file'], 'line' => $error['line'], 'function' => $error['function']);
        }
        $response->end(self::showError($title, $errorMsg, $phpMsg));
    }

    /**
     * 记录错误日志
     *
     * @static
     * @access public
     * @param string $message
     */
    public static function writeErrorLog($message) {

        return false; // 暂时不写入

        $message = self::clear($message);
        $time = time();
        $file = LOG_PATH . '/' . date('Y.m.d') . '_errorlog.php';
        $hash = md5($message);

        $userId = 0;
        $ip = get_client_ip();

        $user = '<b>User:</b> userId=' . intval($userId) . '; IP=' . $ip . '; RIP:' . $_SERVER['REMOTE_ADDR'];
        $uri = 'Request: ' . htmlspecialchars(self::clear($_SERVER['REQUEST_URI']));
        $message = "<?php exit;?>\t{$time}\t$message\t$hash\t$user $uri\n";

        // 判断该$message是否在时间间隔$maxtime内已记录过，有，则不用再记录了
        if (is_file($file)) {
            $fp = @fopen($file, 'rb');
            $lastlen = 50000;  // 读取最后的 $lastlen 长度字节内容
            $maxtime = 60 * 10;  // 时间间隔：10分钟
            $offset = filesize($file) - $lastlen;
            if ($offset > 0) {
                fseek($fp, $offset);
            }
            if ($data = fread($fp, $lastlen)) {
                $array = explode("\n", $data);
                if (is_array($array))
                    foreach ($array as $key => $val) {
                        $row = explode("\t", $val);
                        if ($row[0] != '<?php exit;?>') {
                            continue;
                        }
                        if ($row[3] == $hash && ($row[1] > $time - $maxtime)) {
                            return;
                        }
                    }
            }
        }

    }

    /**
     * 清除文本部分字符
     *
     * @param string $message
     */
    public static function clear($message) {
        return str_replace(array("\t", "\r", "\n"), " ", $message);
    }

    /**
     * sql语句字符清理
     *
     * @static
     * @access public
     * @param string $message
     * @param string $dbConfig
     */
    public static function sqlClear($message, $dbConfig) {
        $message = self::clear($message);
        if (!(defined('SITE_DEBUG') && SITE_DEBUG)) {
            $message = str_replace($dbConfig['database'], '***', $message);
            //$message = str_replace($dbConfig['prefix'], '***', $message);
            $message = str_replace(C('DB_PREFIX'), '***', $message);
        }
        $message = htmlspecialchars($message);
        return $message;
    }

    /**
     * 显示错误
     *
     * @static
     * @access public
     * @param string $type 错误类型 db,system
     * @param string $errorMsg
     * @param string $phpMsg
     */
    public static function showError($title, $errorMsg, $phpMsg = '') {

//        $errorMsg = str_replace(SITE_PATH, '', $errorMsg);
//        ob_end_clean();
        $res = <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
 <title>$title Error</title>
 <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
 <meta name="ROBOTS" content="NOINDEX,NOFOLLOW,NOARCHIVE" />
 <style type="text/css">
 <!--
 body { background-color: white; color: black; font: 9pt/11pt verdana, arial, sans-serif;}
 #container {margin: 10px;}
 #message {width: 1024px; color: black;}
 .red {color: red;}
 a:link {font: 9pt/11pt verdana, arial, sans-serif; color: red;}
 a:visited {font: 9pt/11pt verdana, arial, sans-serif; color: #4e4e4e;}
 h1 {color: #FF0000; font: 18pt "Verdana"; margin-bottom: 0.5em;}
 .bg1 {background-color: #FFFFCC;}
 .bg2 {background-color: #EEEEEE;}
 .table {background: #AAAAAA; font: 11pt Menlo,Consolas,"Lucida Console"}
 .info {
  background: none repeat scroll 0 0 #F3F3F3;
  border: 0px solid #aaaaaa;
  border-radius: 10px 10px 10px 10px;
  color: #000000;
  font-size: 11pt;
  line-height: 160%;
  margin-bottom: 1em;
  padding: 1em;
 }

 .help {
  background: #F3F3F3;
  border-radius: 10px 10px 10px 10px;
  font: 12px verdana, arial, sans-serif;
  text-align: center;
  line-height: 160%;
  padding: 1em;
 }

 .sql {
  background: none repeat scroll 0 0 #FFFFCC;
  border: 1px solid #aaaaaa;
  color: #000000;
  font: arial, sans-serif;
  font-size: 9pt;
  line-height: 160%;
  margin-top: 1em;
  padding: 4px;
 }
 -->
 </style>
</head>
<body>
<div id="container">
<h1>$title Error</h1>
<div class='info'>$errorMsg</div>
EOT;
        if (!empty($phpMsg)) {
            $res .= '<div class="info">';
            $res .= '<p><strong>PHP Debug</strong></p>';
            $res .= '<table cellpadding="5" cellspacing="1" width="100%" class="table"><tbody>';
            if (is_array($phpMsg)) {
                $res .= '<tr class="bg2"><td>No.</td><td>File</td><td>Line</td><td>Code</td></tr>';
                foreach ($phpMsg as $k => $msg) {
                    $k++;
                    $res .= '<tr class="bg1">';
                    $res .= '<td>' . $k . '</td>';
                    $res .= '<td>' . $msg['file'] . '</td>';
                    $res .= '<td>' . $msg['line'] . '</td>';
                    $res .= '<td>' . $msg['function'] . '</td>';
                    $res .= '</tr>';
                }
            } else {
                $res .= '<tr><td><ul>' . $phpMsg . '</ul></td></tr>';
            }
            $res .= '</tbody></table></div>';
        }
        $res .= <<<EOT

</div>
</body>
</html>
EOT;
        return $res;
    }
}