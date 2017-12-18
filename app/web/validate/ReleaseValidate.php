<?php
/**
 * Created by PhpStorm.
 * User: 胡志伟
 * Date: 2017/12/13
 * Time: 10:47
 */

namespace app\web\validate;

use think\Validate;
class ReleaseValidate extends  Validate
{
    public $rule = [
                'user'         =>  'require|chsAlpha|max:25',
                'text'         =>  'require',
                'title'        =>  'require',
                'user_qq'      =>  'number|min:9|max:11',
                'user_Mobile' =>   'number|min:11',
                ];
    public $msg = [
                'user.chsAlpha'          => '用户名只能是汉字或者字母',
                'user.max'                => '用户名最多不能超过25个字符',
                'user.require'            => '用户名不能为空',
                'user_Mobile.number'     => '手机号只能是数字',
                'user_Mobile.min'        => '手机号至少11位',
                'text.require'            => '内容不能为空',
                'title'                    =>  '标题不能为空',
                'user_qq.max'             => 'QQ号最多不能超过11个字符',
                'user_qq.min'             => 'QQ号至少9位',
                'user_qq.number'          => 'QQ号只能是数字',
                ];

}