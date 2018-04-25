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

use app\tools\controller\UserTokenController;
use app\user\model\UserExtensionModel;
use cmf\controller\HomeBaseController;
use think\Db;
use think\Loader;
use think\Session;
use think\Validate;
use app\user\model\UserModel;
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
	 *      email:邮箱
	 */
	public function doRegister($data = null)
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
						$userModel          = new UserModel();
						$userExtensionModel = new UserExtensionModel();

						//判断账户名是否存在
						if (is_null($userModel->existUserLogin($data['username']))) {

							//开启事务处理
							Db::startTrans();
							//添加用户表 表信息
							$addUserId = $userModel->addUser($data);
							//添加用户扩展表信息
							$addUserExtensionId = $userExtensionModel->addUserExtension($addUserId);

							//判断是否都添加成功
							if ($addUserId && $addUserExtensionId) {
								//提交事务
								Db::commit();
								$info = $ajaxTools->ajaxEcho(null, '注册成功', 1);
								return $info;
							} else {
								//回滚事务
								Db::rollback();
								$info = $ajaxTools->ajaxEcho(null, '注册失败', 0);
								return $info;
							}

						} else {
							$info = $ajaxTools->ajaxEcho(null, '账户名已存在', 0);
							return $info;
						}

					} else {
						$resultInfo = $registerValidate->getError();
						$info       = $ajaxTools->ajaxEcho(null, $resultInfo, 0);
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
	 * 第三方登录注册接口
	 *
	 * 当通过  第三方登录并不绑定时, 自动 注册 一个帐号帐号类型为3的 第三方平台帐号
	 *
	 * 参数：
	 *    nickname: 昵称
	 *    mobile：手机
	 *    password：密码
	 *    repassword：重复密码
	 *    username：用户账号
	 *    email:邮箱
	 *
	 * @author ZhangJun
	 */
	public function doRegisterByOpenAccount()
	{


		//获取参数
		$token    = $this->request->param('token');
		$type     = $this->request->param('bing_type');
		$nickname = $this->request->param('nickname');
		$openId   = $this->request->param('op_id');

		//验证token
		if (idckx_token_valid($token)) {
			//token验证通过
			$data = [
				'nickname' => $nickname,
				'username' => $type . '_' . $openId,
				'password' => mt_rand(10000000, 99999999),
				'mobile'   => null,
				'email'    => null,
			];

			//添加用户信息
			$registerUserId = $this->_register($data);

			if ($registerUserId) {
				//用户表成功

				//实例化用户扩展表  模型
				$userExtensionModel = new UserExtensionModel();

				//判断是否绑定成功
				if ($userExtensionModel->bindUserOpenAccount($registerUserId, $type, $openId)) {
					//绑定成功

					//执行登录
					$loginC = new LoginController();

					return $loginC->doLoginByOpenAccount($openId, $type, $token);

				} else {
					//绑定失败   删除已经创建的临时帐号
					$this->_delUser($registerUserId);
					return idckx_ajax_echo(null, '平台帐号绑定本地临时帐号失败', 0);
				}


			} else {
				//添加用户表失败
				return idckx_ajax_echo(null, '注册失败', 0);
			}


		} else {
			//不存在或则过期
			return idckx_ajax_echo(null, 'token无效或过期', 0);

		}


	}


	/**
	 * 私有   注册前台用户控制器
	 *
	 * @param $data
	 * @return int
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	private function _register($data)
	{

		//实例化 用户模型
		$userModel          = new UserModel();
		$userExtensionModel = new UserExtensionModel();

		//判断账户名是否存在
		if (is_null($userModel->existUserLogin($data['username']))) {

			//开启事务处理
			Db::startTrans();
			//添加用户表 表信息
			$addUserId = $userModel->addUser($data);
			//添加用户扩展表信息
			$addUserExtensionId = $userExtensionModel->addUserExtension($addUserId);

			//判断是否都添加成功
			if ($addUserId && $addUserExtensionId) {
				//注册成功
				Db::commit();

				return $addUserId;
			} else {
				//注册失败
				//回滚事务
				Db::rollback();
				return false;
			}

		} else {

			//账户名已存在
			return false;
		}
	}


	/**
	 * 删除用户帐号
	 *
	 * @authon ZhangJun
	 * @param null $userId
	 */
	private function _delUser($userId = null)
	{

		//事务处理 同时删除用户表与用户扩张表
		Db::transaction(function () use ($userId) {
			Db::name('user')->where('id', $userId)->delete();
			Db::name('user_extension')->where('user_id', $userId)->delete();
		});


	}

	/**
	 * 测试控制器
	 */
	public function test()
	{

//		dump(idckx_verify_binding('weibo',null));
//		dump(idckx_test(1, null));

		dump(idckx_token_get('57323ced9c864af9dbaa7400f4e4ed73'));
		dump(Session::get());

//++++++++++++++curl实例+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

//		//初始化
//		$curl = curl_init();
//		//设置抓取的url
//		curl_setopt($curl, CURLOPT_URL, 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxc06ebce515a3291b&secret=beb6ed823e4f332ac7834901918732b5');
//		//设置头文件的信息作为数据流输出
////		curl_setopt($curl, CURLOPT_HEADER, 1);
//		//设置获取的信息以文件流的形式返回，而不是直接输出。
//		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//		//执行命令
//		$data = curl_exec($curl);
//		//关闭URL请求
//		curl_close($curl);
//		//显示获得的数据
////		print_r($data);
//		print($data);


	}


//=====================================================================================================================
//=====================================================================================================================
//=====================================================================================================================
	/**
	 * 前台用户注册
	 */
	public
	function indexCmf()
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
	public
	function doRegisterCmf()
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