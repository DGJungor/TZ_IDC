<?php
/**
 * Created by PhpStorm.
 * User: 胡志伟
 * Date: 2017/12/14
 * Time: 13:30
 */
namespace app\web\validate;

use think\Validate;

class AdminBaoliaoValidate extends Validate
{
    protected $rule = [
        'categories' => 'require',
        'post_title' => 'require',
    ];
    protected $message = [
        'categories.require' => '请指定文章分类！',
        'post_title.require' => '文章标题不能为空！',
    ];

    protected $scene = [
//        'add'  => ['user_login,user_pass,user_email'],
//        'edit' => ['user_login,user_email'],
    ];
}