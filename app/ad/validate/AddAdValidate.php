<?php

namespace app\ad\validate;

use think\Validate;

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
