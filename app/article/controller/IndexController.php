<?php

namespace app\article\controller;

use cmf\controller\HomeBaseController;
use think\Request;

/**
 * Class IndexController
 *
 * 前台文章页首页面控制器
 *
 * @author 张俊
 * @package app\article\controller
 *
 */
class IndexController extends HomeBaseController
{
    public function index(Request $request)
    {
        //===========================测试数据=================================================
        $test =  $request->param('id');
//        dump($test);

        //===================================================================================

        //获取文章id
        $postId = $request->param('id');



        return $this->fetch();
//        return $this->fetch(':index');
    }
}
