<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Powerless < wzxaini9@gmail.com>
// +----------------------------------------------------------------------
namespace app\user\controller;

use cmf\controller\HomeBaseController;
use think\Loader;
use think\Validate;
use app\user\model\UserModel;
use app\tools\controller\AjaxController;

class RegisterController extends HomeBaseController
{

	public function test()
	{
		//判断是否开放注册
		$isOpenRegistration = cmf_is_open_registration();

		$registerValidate = Loader::validate('Register');

		$ajaxTools = new AjaxController();

//		$info3 = $ajaxTools->ajaxEcho('11111');

		$data = [
			'username' => 'z568171152',
			'nickname' => 'JUN',
			'mobile'   => '15812816866',
			'password' => 'zhangjun',

		];

		$info  = $validate->check($data);
		$info2 = $validate->getError();

		//测试数据
		dump($info3);


	}


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

		//实例化ajax工具
		$ajaxTools = new AjaxController();

		//判断是否接收到参数
		if ($this->request->isPost()) {

			//判断是否开放注册
			$isOpenRegistration = cmf_is_open_registration();

			//若未开放注册则抛出错误
			if ($isOpenRegistration) {

				//获取注册表单的数据(POST)
				$data = $this->request->post();

				//判断两次输入的密码是否一致
				if (trim($data['password']) == trim($data['repassword'])) {

					//加载注册验证规则 并执行验证  返回结果
					$registerValidate = Loader::validate('Register');
					$result           = $registerValidate->check($data);

					//根据结果  若正确则执行添加数据库操作 错误则返回错误信息
					if ($result) {
						//实例化 用户模型
						$userModel = new UserModel();

						//判断账户名是否存在
						if (is_null($userModel->existUserLogin($data['username']))) {
							$result = $userModel->addUser($data);
							if ($result) {
								$info = $ajaxTools->ajaxEcho(null, '注册成功', 1);
								return $info;
							}

						} else {
							$info = $ajaxTools->ajaxEcho(null, '账户名已存在', 0);
							return $info;
						}

					} else {
						$info = $ajaxTools->ajaxEcho(null, $resultInfo, 0);
						return $info;
					}

				} else {
					$info = $ajaxTools->ajaxEcho(null, '两次输入的密码不一致', 0);
					return $info;
				}

			} else {
				$info = $ajaxTools->ajaxEcho(null, '网站未开放注册', 0);
				return $info;
			}
		} else {
			$info = $ajaxTools->ajaxEcho(null, '错误请求', 0);
			return $info;
		}
	}


	/**
	 * 前台用户注册
	 */
	public function indexCmf()
	{
		$redirect = $this->request->post("redirect");
		if (empty($redirect)) {
			$redirect = $this->request->server('HTTP_REFERER');
		} else {
			$redirect = base64_decode($redirect);
		}
		session('login_http_referer', $redirect);

		if (cmf_is_user_login()) {
			return redirect($this->request->root() . '/');
		} else {
			return $this->fetch(":register");
		}
	}


	/**
	 * 前台用户注册提交
	 */
	public function doRegisterCmf()
	{
		if ($this->request->isPost()) {
			$rules = [
				'captcha'  => 'require',
				'code'     => 'require',
				'password' => 'require|min:6|max:32',

			];

			$isOpenRegistration = cmf_is_open_registration();

			if ($isOpenRegistration) {
				unset($rules['code']);
			}

			$validate = new Validate($rules);
			$validate->message([
				'code.require'     => '验证码不能为空',
				'password.require' => '密码不能为空',
				'password.max'     => '密码不能超过32个字符',
				'password.min'     => '密码不能小于6个字符',
				'captcha.require'  => '验证码不能为空',
			]);

			$data = $this->request->post();
			if (!$validate->check($data)) {
				$this->error($validate->getError());
			}
			if (!cmf_captcha_check($data['captcha'])) {
				$this->error('验证码错误');
			}

			if (!$isOpenRegistration) {
				$errMsg = cmf_check_verification_code($data['username'], $data['code']);
				if (!empty($errMsg)) {
					$this->error($errMsg);
				}
			}

			$register          = new UserModel();
			$user['user_pass'] = $data['password'];
			if (Validate::is($data['username'], 'email')) {
				$user['user_email'] = $data['username'];
				$log                = $register->registerEmail($user);
			} else if (preg_match('/(^(13\d|15[^4\D]|17[013678]|18\d)\d{8})$/', $data['username'])) {
				$user['mobile'] = $data['username'];
				$log            = $register->registerMobile($user);
			} else {
				$log = 2;
			}
			$sessionLoginHttpReferer = session('login_http_referer');
			$redirect                = empty($sessionLoginHttpReferer) ? cmf_get_root() . '/' : $sessionLoginHttpReferer;
			switch ($log) {
				case 0:
					$this->success('注册成功', $redirect);
					break;
				case 1:
					$this->error("您的账户已注册过");
					break;
				case 2:
					$this->error("您输入的账号格式错误");
					break;
				default :
					$this->error('未受理的请求');
			}

		} else {
			$this->error("请求错误");
		}

	}
}