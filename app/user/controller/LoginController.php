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

use think\Loader;
use think\Session;
use think\Validate;
use cmf\controller\HomeBaseController;
use app\user\model\UserModel;
use app\tools\controller\AjaxController;
use app\user\model\UserTokenModel;

class LoginController extends HomeBaseController
{


	public function test()
	{
		$data = $this->request->Post();

		$pa = cmf_password($data['password']);

		dump($data);

		dump($pa);
	}


	/**
	 * @return \think\response\Json
	 *
	 *
	 *
	 *
	 * 登录框
	 * 接口名称：user/login/dologin
	 * 参数：    username：用户名
	 *           password：密码
	 *           Autologon：是否能自动登录（把token时间设置长点，默认是1小时的）
	 * 返回参数：
	 *        id：用户id
	 *        avatar：头像
	 *        token：当前登录token
	 *        （必须用我封装的那个函数返回）
	 */
	public function doLogin()
	{
		//实例化
		$ajaxTools = new AjaxController();
		$userModel = new UserModel();

		//判断是否接收到参数
		if ($this->request->isPost()) {

			//重新整合数组
			$user = [
				'user_login' => $this->request->post('username'),
				'user_pass'  => $this->request->post('password'),
			];
			$log = $userModel->doName($user);

			switch ($log) {
				case 0:
					$userTokenModel = new UserTokenModel();
					cmf_user_action('login');

					//生成token 并保存token值
					$token = $this->request->token('__token__',Session('user.id'));

					//根据自动登录 修改过期时间

					$res = $userTokenModel->addUserTokenData(Session('user.id'),$token);


					dump(Session('user'));
					dump($res);
					$info = $ajaxTools->ajaxEcho(null, '登录成功', 0);
					return $info;
					break;
				case 1:
					$info = $ajaxTools->ajaxEcho(null, '登录密码错误', 0);
					return $info;
					break;
				case 2:
					$info = $ajaxTools->ajaxEcho(null, '账户不存在', 0);
					return $info;
					break;
				case 3:
					$info = $ajaxTools->ajaxEcho(null, '账号禁止登录', 0);
					return $info;
					break;
				default :
					$info = $ajaxTools->ajaxEcho(null, '未受理请求', 0);
					return $info;
					break;
			}
		} else {
			$info = $ajaxTools->ajaxEcho(null, '错误请求', 0);
			return $info;
		}


//===============================================旧======================================================================

//		//判断是否接收到参数
//		if ($this->request->isPost()) {
//
//			//获取前端传来的参数
//			$data = $this->request->post();
//
//			//加载注册验证规则 并执行验证  返回结果
//			$loginValidate = Loader::validate('Login');
//			$result        = $loginValidate->check($data);
//
//			//根据验证的结果   进行相关操作或者返回相关信息
//			if ($result) {
//				//实例化 用户模型
//				$userModel = new UserModel();
//
//				//根据输入的登录账号  查询账号信息
//				$userData = $userModel->queryUser($data['username']);
//
//				//验证输入的账号是否存在
//				if (!is_null($userData)) {
//
//					//验证密码是否正确
//					if (cmf_compare_password($data['password'], $userData['user_pass'])) {
//						dump('正确');
//						dump();
//						dump(Session::get());
//
//					} else {
//						$info = $ajaxTools->ajaxEcho(null, '密码错误', 0);
//						return $info;
//					}
//
//
//				} else {
//					$info = $ajaxTools->ajaxEcho(null, '账号不存在', 0);
//					return $info;
//				}
//
//			} else {
//				$resultInfo = $loginValidate->getError();
//				$info       = $ajaxTools->ajaxEcho(null, $resultInfo, 0);
//				return $info;
//			}
//
//		} else {
//			$info = $ajaxTools->ajaxEcho(null, '错误请求', 0);
//			return $info;
//
//		}


	}


	/**
	 * 登录
	 */
	public
	function index()
	{
		$redirect = $this->request->post("redirect");
		if (empty($redirect)) {
			$redirect = $this->request->server('HTTP_REFERER');
		} else {
			$redirect = base64_decode($redirect);
		}
		session('login_http_referer', $redirect);
		if (cmf_is_user_login()) { //已经登录时直接跳到首页
			return redirect($this->request->root() . '/');
		} else {
			return $this->fetch(":login");
		}
	}

	/**
	 * 登录验证提交
	 */
	public
	function doLoginCmf()
	{
		if ($this->request->isPost()) {
			$validate = new Validate([
				'captcha'  => 'require',
				'username' => 'require',
				'password' => 'require|min:6|max:32',
			]);
			$validate->message([
				'username.require' => '用户名不能为空',
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

			$userModel         = new UserModel();
			$user['user_pass'] = $data['password'];
			if (Validate::is($data['username'], 'email')) {
				$user['user_email'] = $data['username'];
				$log                = $userModel->doEmail($user);
			} else if (preg_match('/(^(13\d|15[^4\D]|17[013678]|18\d)\d{8})$/', $data['username'])) {
				$user['mobile'] = $data['username'];
				$log            = $userModel->doMobile($user);
			} else {
				$user['user_login'] = $data['username'];
				$log                = $userModel->doName($user);
			}
			$session_login_http_referer = session('login_http_referer');
			$redirect                   = empty($session_login_http_referer) ? $this->request->root() : $session_login_http_referer;
			switch ($log) {
				case 0:
					cmf_user_action('login');
					$this->success('登录成功', $redirect);
					break;
				case 1:
					$this->error('登录密码错误');
					break;
				case 2:
					$this->error('账户不存在');
					break;
				case 3:
					$this->error('账号被禁止访问系统');
					break;
				default :
					$this->error('未受理的请求');
			}
		} else {
			$this->error("请求错误");
		}
	}

	/**
	 * 找回密码
	 */
	public
	function findPassword()
	{
		return $this->fetch('/find_password');
	}

	/**
	 * 用户密码重置
	 */
	public
	function passwordReset()
	{

		if ($this->request->isPost()) {
			$validate = new Validate([
				'captcha'           => 'require',
				'verification_code' => 'require',
				'password'          => 'require|min:6|max:32',
			]);
			$validate->message([
				'verification_code.require' => '验证码不能为空',
				'password.require'          => '密码不能为空',
				'password.max'              => '密码不能超过32个字符',
				'password.min'              => '密码不能小于6个字符',
				'captcha.require'           => '验证码不能为空',
			]);

			$data = $this->request->post();
			if (!$validate->check($data)) {
				$this->error($validate->getError());
			}

			if (!cmf_captcha_check($data['captcha'])) {
				$this->error('验证码错误');
			}
			$errMsg = cmf_check_verification_code($data['username'], $data['verification_code']);
			if (!empty($errMsg)) {
				$this->error($errMsg);
			}

			$userModel = new UserModel();
			if ($validate::is($data['username'], 'email')) {

				$log = $userModel->emailPasswordReset($data['username'], $data['password']);

			} else if (preg_match('/(^(13\d|15[^4\D]|17[013678]|18\d)\d{8})$/', $data['username'])) {
				$user['mobile'] = $data['username'];
				$log            = $userModel->mobilePasswordReset($data['username'], $data['password']);
			} else {
				$log = 2;
			}
			switch ($log) {
				case 0:
					$this->success('密码重置成功', $this->request->root());
					break;
				case 1:
					$this->error("您的账户尚未注册");
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