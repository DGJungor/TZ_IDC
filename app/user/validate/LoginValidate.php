<?php

namespace app\user\validate;

use think\Validate;

/**
 * 前台注册验证模型
 *
 * Class RegisterValidate
 * @author 张俊
 * @package app\ad\validate
 *
 */
class LoginValidate extends Validate
{

	protected $rule = [
		'username' => 'require',
		'password' => 'require',
	];

	protected $message = [
		'username.require' => '用户账号不能为空',
		'password.require' => '密码不能为空',
	];


}