<?php

namespace app\web\validate;

use think\Validate;

/**
 * 前台注册验证模型
 *
 * Class RegisterValidate
 * @author 张俊
 * @package app\ad\validate
 *
 */
class RegisterValidate extends Validate
{

	protected $rule = [
		'password' => 'require|min:6|max:32',
	];

	protected $message = [
		'email'            => '邮箱格式错误',
		'password.require' => '密码不能为空',
		'password.min'     => '密码不能了少于6个字符',
		'password.max'     => '密码不能超过32个字符',
	];

}