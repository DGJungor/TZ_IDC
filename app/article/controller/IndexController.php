<?php

namespace app\article\controller;

use app\article\model\PortalPostModel;
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
//        $test = $request->param('id');
//        dump($test);

        //===================================================================================

        //实例化文章模型
        $portalPostModel = new PortalPostModel();

        //根据文章id取出文章数据
        $postData        = $portalPostModel->get(13);

        //将字符串与图片地址 转化成html代码和  绝对路径的图片地址
        $postHtmlData = $portalPostModel->getPostContentAttr($postData['post_content']);
        $postData->post_content_toHtml = $postHtmlData;

        //将时间戳转化成中文时间
        $timeToStr = date("Y年m月d日 H:i:s",$postData['published_time']);
        $postData->post_published_time_toStr = $timeToStr;
        dump($postData);

        //获取文章id
        $postId = $request->param('id');

        //赋值变量  并渲染模板
        $this->assign('article', $postData);
        return $this->fetch();
//        return $this->fetch(':index');
    }
}
