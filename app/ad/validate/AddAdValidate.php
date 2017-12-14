<?php

namespace app\ad\validate;

use think\Validate;

/**
 * Class AddAdValidate
 *
 * 广告添加 验证
 *
 * @author 张俊
 * @package app\ad\validate
 *
 *
 */
class AddAdValidate extends  Validate
{


    /**
     * @var array
     *广告添加 通过验证token 判断是否有重复提交
     *
     *
     */
    protected $rule = [
        'adName'  =>  'require|token',
//        'email' =>  'email',
    ];


}
