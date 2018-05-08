<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace app\user\controller;

use cmf\controller\HomeBaseController;
use think\Validate;

class VerificationCodeController extends HomeBaseController
{

	/**
	 * post_content 自动转化
	 * @param $value
	 * @return string
	 */
	public function getPostContentAttr($value)
	{
		return cmf_replace_content_file_url(htmlspecialchars_decode($value));
	}

	/**
	 * post_content 自动转化
	 * @param $value
	 * @return string
	 */
	public function setPostContentAttr($value)
	{
		return htmlspecialchars(cmf_replace_content_file_url(htmlspecialchars_decode($value), true));
	}

	/**
	 * 发送邮箱验证码   TODO
	 *
	 * 通过发送随机码到 数据库(idckx_verification_code表中)  记录验证信息   验证时
	 *
	 * 参数:
	 *      email:邮箱
	 *
	 * @author ZhangJun
	 */
	public function sendEmailCode()
	{

		//获取参数
		$email = $this->request->param('email');

		//实例验证类
		$validate = new Validate([
			'email' => 'require',
		]);
		$validate->message([
			'email.require' => '邮箱不能为空!',
		]);


		dump($this->string_remove_xss($email));
		$a = $this->string_remove_xss($email);
		dump($this->getPostContentAttr($a));
		//测试数据
		dump($this->setPostContentAttr($email));
		$b = $this->setPostContentAttr($email);
		dump($this->getPostContentAttr($b));

	}

	/**
	 * 手机发送验证码  TODO 必须功能  (延后开发)
	 */




//=================================无用代码=========================================================================================


	public function send()
	{
		$validate = new Validate([
			'username' => 'require',
		]);

		$validate->message([
			'username.require' => '请输入手机号或邮箱!',
		]);

		$data = $this->request->param();
		if (!$validate->check($data)) {
			$this->error($validate->getError());
		}

		$accountType = '';

		if (Validate::is($data['username'], 'email')) {
			$accountType = 'email';
		} else if (preg_match('/(^(13\d|15[^4\D]|17[013678]|18\d)\d{8})$/', $data['username'])) {
			$accountType = 'mobile';
		} else {
			$this->error("请输入正确的手机或者邮箱格式!");
		}

		//TODO 限制 每个ip 的发送次数

		$code = cmf_get_verification_code($data['username']);
		if (empty($code)) {
			$this->error("验证码发送过多,请明天再试!");
		}

		if ($accountType == 'email') {

			$emailTemplate = cmf_get_option('email_template_verification_code');

			$user     = cmf_get_current_user();
			$username = empty($user['user_nickname']) ? $user['user_login'] : $user['user_nickname'];

			$message = htmlspecialchars_decode($emailTemplate['template']);
			$message = $this->display($message, ['code' => $code, 'username' => $username]);
			$subject = empty($emailTemplate['subject']) ? 'ThinkCMF验证码' : $emailTemplate['subject'];
			$result  = cmf_send_email($data['username'], $subject, $message);

			if (empty($result['error'])) {
				cmf_verification_code_log($data['username'], $code);
				$this->success("验证码已经发送成功!");
			} else {
				$this->error("邮箱验证码发送失败:" . $result['message']);
			}

		} else if ($accountType == 'mobile') {

			$param  = ['mobile' => $data['username'], 'code' => $code];
			$result = hook_one("send_mobile_verification_code", $param);

			if ($result !== false && !empty($result['error'])) {
				$this->error($result['message']);
			}

			if ($result === false) {
				$this->error('未安装验证码发送插件,请联系管理员!');
			}

			cmf_verification_code_log($data['username'], $code);

			if (!empty($result['message'])) {
				$this->success($result['message']);
			} else {
				$this->success('验证码已经发送成功!');
			}

		}


	}

}
