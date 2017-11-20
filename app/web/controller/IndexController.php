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


        dump($a);

        return $this->fetch();


    }
}