<?php
namespace app\web\validate;

use think\Validate;
class UserInfoValidate {
    protected $rule = [
        'mobile' => 'require|number|min:11'
    ];
    protected $msg = [
        'mobile.number' => '手机号只能是数字',
        'mobile.min' => '手机号至少11位',
        'mobile.require' => '手机号不能为空'
    ];
    public function checkIinfo($data,$rule,$msg) {
        if(empty($rule)||empty($msg)) {
            $validate = new Validate($this->rule,$this->msg);
            return $validate;
        }else {
            $validate = new Validate($rule,$msg);
            return $validate;
        }
       
    }
}