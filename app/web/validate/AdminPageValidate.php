<?php
/**
 * Created by PhpStorm.
 * User: 胡志伟
 * Date: 2017/12/14
 * Time: 13:55
 */
namespace app\web\validate;

use app\admin\model\RouteModel;
use think\Validate;

class AdminPageValidate extends Validate
{
    protected $rule = [
        'post_title' => 'require',
        'post_alias' => 'checkAlias'
    ];
    protected $message = [
        'post_title.require' => '页面标题不能为空',
    ];

    protected $scene = [
//        'add'  => ['user_login,user_pass,user_email'],
//        'edit' => ['user_login,user_email'],
    ];

    // 自定义验证规则
    protected function checkAlias($value, $rule, $data)
    {
        if (empty($value)) {
            return true;
        }

        $routeModel = new RouteModel();
        $fullUrl    = $routeModel->buildFullUrl('webl/Page/index', ['id' => $data['id']]);
        if (!$routeModel->exists($value, $fullUrl)) {
            return true;
        } else {
            return "别名已经存在!";
        }

    }
}