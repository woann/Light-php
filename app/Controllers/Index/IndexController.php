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
namespace app\Controllers\Index;

use Lib\Controller;
use Lib\DB;

class IndexController extends Controller {
    public function index(){
        $res = DB::table('user')->where('id',"=","1")->first();
        return $this->json($res);
    }

    /**
     * @author woann<304550409@qq.com>
     * @des 测试页面渲染
     */
    public function ws()
    {
        return $this->view('ws',['title'=>"哈哈哈"]);
    }
}