<?php


namespace app\referrals\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use think\Request;


/**
 * Class AdminIndexController
 *
 * 产品推介模块  后台显示页面
 *
 * @author ZhangJun
 * @package app\referrals\controller
 *
 *
 *
 */
class AdminIndexController extends AdminBaseController
{
    public function index(Request $request)
    {

        return $this->fetch();

    }

}