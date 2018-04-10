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


	/**
	 * 判断前台用户是否已登录
	 *
	 * @author 张俊
	 * @return \think\response\Json
	 *
	 */
	public function isLogin()
	{
		//实例化
		$ajaxTools = new AjaxController();


		//判断session中是否存在用户信息
		if (Session("user")) {
			if (Session('user.expire_time') > time()) {
				//拼装返回数据
				$data = [
					'id'  => Session("user.id"),
					'img' => Session("user.avatar"),
				];
				$info = $ajaxTools->ajaxEcho($data, '已登录', 1);
				return $info;
			} else {
				$info = $ajaxTools->ajaxEcho(null, '登录过期', 5000);
				return $info;
			}
		} else {
			$info = $ajaxTools->ajaxEcho(null, '未登录', 5000);
			return $info;
		}
	}


	/**
	 * 退出登录
	 * @author 张俊
	 * @return \think\response\Redirect
	 *
	 * 成功退出登录返回 状态码5000
	 */
	public function outLogin()
	{
		//实例化
		$ajaxTools = new AjaxController();

		//清空session中的信息
		Session('user', null);
		$data = [
			'url' => '/',
		];

		//返回信息
		$info = $ajaxTools->ajaxEcho($data, '已退出登录', 1);
		return $info;

	}


	/**
	 * 登录操作
	 *
	 * @author 张俊
	 * @return \think\response\Json
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
			$log  = $userModel->doName($user);

			switch ($log) {
				case 0:
					$userTokenModel = new UserTokenModel();

					//写入登录信息
					cmf_user_action('login');

					//生成token 并保存token值
					$token = $this->request->token('__token__', Session('user.id'));

					//根据自动登录 修改过期时间 (7天自动登录  604800)
					if ($this->request->post('Autologon') == 1) {
						$res = $userTokenModel->addUserTokenData(Session('user.id'), $token, 604800);
						Session('user.expire_time', time() + 604800);
					} else {
						$res = $userTokenModel->addUserTokenData(Session('user.id'), $token, 3600);
						Session('user.expire_time', time() + 3600);
					}

					//拼装返回的数据数组
					$resData = [
						"id"     => Session('user.id'),
						"avatar" => Session('user.avatar'),
						"token"  => $token,
					];

					//清理过期Token
					$userTokenModel->clearExpireToken();

					$info = $ajaxTools->ajaxEcho($resData, '登录成功', 1);
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

	}


	/**
	 * 登录
	 */
	public function index()
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
	public function doLoginCmf()
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
	public function findPassword()
	{
		return $this->fetch('/find_password');
	}

	/**
	 * 用户密码重置
	 */
	public function passwordReset()
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


	/**
	 * TODO
	 *参数：
	 *     extData: 第三方返回的对象
	 *     type: 第三方类型weibo（微博）、qq（qq）、wx微信
	 *返回参数
	 *    state为1就是登录成功,state为0就是登录失败
	 *    如果要处理这些数据尽量把它处理成和普通登录一样
	 *
	 */
	public function extLogin()
	{


	}


	/**
	 * TODO 第三方登录接口
	 *
	 * 第三方账号登录
	 *
	 * 参数  :
	 *        $type   平台类型:  wechat:微信    weibo:微博    qq:qq
	 */
	public function doLoginByOpenAccount()
	{
		//实例化模型
		$userModel = new UserModel();

		//获取参数
		$openId = $this->request->param('open_id');
		$type   = $this->request->param('type');
		$token  = $this->request->param('token');

		//查询token
		$dbToken = idckx_token_get($token);

		//验证数据库中否存在前端发送过来的token
		if ($dbToken) {

			//验证token是否过期
			if ($dbToken['expire_time'] > time()) {
				//未过期
				dump('未过期');

				dump(idckx_verify_binding($type, $openId));


			} else {
				//已过期

				//清理此条过期token
				idckx_token_del(1, $dbToken['token']);


				dump('已过期');

			}

//			dump(time());
//			dump($dbToken['expire_time']);
//			dump($dbToken);


		} else {
			//不存在token


		}

		//模型测试
//		$res = $userModel->queryBinding($openId, $type);


		//打印测试参数

//		dump($res);


	}


	/**
	 *  TODO 多平台登录  流程不确定  待开发
	 *
	 * 测试控制器
	 */
	public function test()
	{

	}


}