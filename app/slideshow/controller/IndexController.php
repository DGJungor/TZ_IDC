<?php


namespace app\slideshow\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use think\Request;

class IndexController extends AdminBaseController
{
    public function index(Request $request)
    {

        return $this->fetch();

    }

}