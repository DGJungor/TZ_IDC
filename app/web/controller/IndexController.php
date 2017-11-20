<?php


namespace app\web\controller;

use cmf\controller\HomeBaseController;
use app\web\model\IndexModel;

class IndexController extends HomeBaseController
{
    public function index()
    {

        $hotInfo = new IndexModel();
        $a = $hotInfo->hotInfo();

//        $b = json_encode('中文');
//        $v = json_decode($b);

        $c = $this->indexAjax();
        dump($c);



        return $this->fetch();

    }

    /**
     *首页Ajax 列表标签
     *
     *
     *
     */
    public function indexAjax()
    {

        //模拟接收到的中文标签数据
        $testName = json_encode('网络安全');

        $tagName = json_decode($testName);

        $indexModel = New IndexModel();

        $tagCode = $indexModel->matchTag($tagName);

        $str =  $indexModel->ajaxTag($tagCode['id']);


        return $str;


    }

}