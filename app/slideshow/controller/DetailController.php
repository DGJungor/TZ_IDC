<?php


namespace app\slideshow\controller;

use cmf\controller\HomeBaseController;
use think\Db;
use think\Request;

class DetailController extends HomeBaseController
{
    public function index(Request $request)
    {

        return $this->fetch();

    }

}