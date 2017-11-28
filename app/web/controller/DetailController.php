<?php


namespace app\web\controller;

use cmf\controller\HomeBaseController;
use app\web\model\IndexModel;
use think\Db;
use think\Request;

class DetailController extends HomeBaseController
{
    public function index(Request $request)
    {

        $str = $request->param();
        dump($str);

//        return 'sdfasd';

    }

}