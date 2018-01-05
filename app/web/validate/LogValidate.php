<?php

/**
 * Created by PhpStorm.
 * User: 胡志伟
 * Date: 2017/12/13
 * Time: 9:56
 */

namespace app\web\validate;

use think\Validate;

class LogValidate extends Validate
{
    public $rule = [
                'user' => 'require|chsAlpha|max:25',
                'mobile' => 'require|number|min:11',
                'email' => 'require|email',
                'password' => 'require|max:25',
                'repassword' => 'require|confirm:password',
                ];
    public $message = [
                'user.chsAlpha' => '用户名只能是汉字或者字母',
                'user.max' => '用户名最多不能超过25个字符',
                'user.require' => '用户名不能为空',
                'mobile.number' => '手机号只能是数字',
                'mobile.min' => '手机号至少11位',
                'mobile.require' => '手机号不能为空',
                'email' => '邮箱格式错误',
                'email.require' => '邮箱不能为空',
                'password.require' => '密码不能为空',
                'password.max' => '密码最多不能超过25个字符',
                'repassword.require' => '确认密码不能为空',
                'repassword.confirm' => '两次密码不相同',
                ];
}