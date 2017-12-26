<?php

namespace app\lists\controller;

use cmf\controller\HomeBaseController;
use think\Request;

/**
 * Class IndexController
 *
 * 前台列表控制器
 *
 * @author 张俊
 * @package app\listing\controller
 *
 */
class IndexController extends HomeBaseController
{

	/**
	 *自动获取最新文章
	 *
	 * @return mixed
	 *
	 */
	public function index()
	{
		return $this->fetch();
	}

}