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
		'username' => 'require|alphaDash|min:4|max:16',
		'nickname' => 'require',
		'email'    => 'require|email',
		//		'password' => 'require|min:6|max:32',
		//		'email' => 'email',
	];

	protected $message = [

		'username.require'   => '用户账号不能为空',
		'username.alphaDash' => '用户账号只能为字母,数字,下划线',
		'username.min'       => '用户账号最少4个字符',
		'username.max'       => '用户账号最长16个字符',
		'nickname.require'   => '昵称不能为空',
//		'mobile'

		'email'            => '邮箱格式错误',
		'password.require' => '密码不能为空',
		'password.min'     => '密码不能了少于6个字符',
		'password.max'     => '密码不能超过32个字符',
	];


}