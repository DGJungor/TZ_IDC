<?php

namespace app\ad\validate;

use think\Validate;

class AddAdValidate extends  Validate
{
    protected $rule = [
        'adName'  =>  'require|token',
//        'email' =>  'email',
    ];


}
