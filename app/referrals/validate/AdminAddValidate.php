<?php

namespace app\referrals\validate;

use think\Validate;

/**
 * Class AddSlideshowValidate
 *
 * 后台添加产品推介  验证规则
 * @auth 张俊
 * @package app\slideshow\validate
 *
 */
class AdminAddValidate extends  Validate
{
    protected $rule = [
        'reName'  =>  'require|token',
        'email' =>  'email',
    ];


}
