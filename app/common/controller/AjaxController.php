<?php
// +----------------------------------------------------------------------
// | IDCKX
// +----------------------------------------------------------------------
// | Author:
// +----------------------------------------------------------------------
namespace app\common\controller;

use cmf\controller\HomeBaseController;
use think\Loader;

class AjaxController extends HomeBaseController
{

	/**
	 * 前端对象输出（凡是通过ajax请求的接口都必须调用此函数输出）
	 * 方法名: ajaxEcho
	 * 参数：$data=[],$info="",$code=0
	 * $data为要输出的数据，默认是空数组
	 * $info提示信息，默认是空
	 * $code错误代码，默认是0
	 * */
	function ajaxEcho($data = [], $info = "", $code = 0)
	{
		return json(["state" => $code, "data" => $data, "msg" => $info]);
	}
}
