<?php

namespace app\user\validate;

use think\Validate;

/**
 * 前台修改验证模型
 *
 * Class ChangePasswordValidate
 * @author 张俊
 * @package app\user\validate
 *
 */
class ChangePasswordValidate extends Validate
{

	protected $rule = [
		'password' => 'require|min:6|max:16|alphaDash'
	];

	protected $message = [

		'password.require'   => '密码不能为空',
		'password.min'       => '密码不能了少于6个字符',
		'password.max'       => '密码不能超过16个字符',
		'password.alphaDash' => '密码只能为字母,数字,下划线',
	];


}