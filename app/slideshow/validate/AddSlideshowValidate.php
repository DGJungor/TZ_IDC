<?php

namespace app\slideshow\validate;

use think\Validate;

class AddSlideshowValidate extends  Validate
{
    protected $rule = [
        'title'  =>  'require|token',
        'email' =>  'email',
    ];


}
