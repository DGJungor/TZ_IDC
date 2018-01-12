<?php
// +----------------------------------------------------------------------
// | IDCKX
// +----------------------------------------------------------------------
// | Author:
// +----------------------------------------------------------------------
namespace app\web\controller;

include_once(dirname(dirname(dirname(__FILE__))).'/tools/ajaxEcho.php');
include_once(dirname(dirname(dirname(__FILE__))).'/tools/cookie_session.php');
use cmf\controller\HomeBaseController;
use think\Loader;
use think\Validate;
use app\web\model\UserModel;
use app\tools\controller\AjaxController;

class RegisterController extends HomeBaseController
{




	/**
	 * 注册框
	 * 接口地址：user/register/doregister
	 * 参数：
	 *      nickname: 昵称
	 *      mobile：手机
	 *      password：密码
	 *      repassword：重复密码
	 *      username：用户账号
	 */
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