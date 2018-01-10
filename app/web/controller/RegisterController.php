<?php
// +----------------------------------------------------------------------
// | IDCKX
// +----------------------------------------------------------------------
// | Author:
// +----------------------------------------------------------------------
namespace app\web\controller;

use cmf\controller\HomeBaseController;
use think\Loader;
use think\Validate;
use app\web\model\UserModel;

class RegisterController extends HomeBaseController
{

	public function index()
	{
		//判断是否开放注册
		$isOpenRegistration = cmf_is_open_registration();

		$validate = Loader::validate('Register');


		//测试数据

	}

	public function doRegister()
	{
		//判断是否接收到参数
		if ($this->request->isPost()) {

			//判断是否开放注册
			$isOpenRegistration = cmf_is_open_registration();

			//若未开放注册则抛出错误
			if ($isOpenRegistration) {
				$data = $this->request->post();



			} else {
				$this->error("网站未开放注册");
			}

		} else {
			$this->error("请求错误");
		}
	}
}