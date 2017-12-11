<?php
/**
 * Created by PhpStorm.
 * User: 胡志伟
 * Date: 2017/12/8
 * Time: 10:04
 */

namespace app\web\controller;


use FontLib\Table\Type\glyf;
use app\web\model\UuserModel;
use FontLib\Table\Type\name;
use think\Session;
use think\Validate;
use think\Db;
use think\Request;

class UserController extends Validate
{
    public $userID = '';
    public function __construct()
    {
        $this->userID = cmf_get_current_user_id();
    }

    public function index()
    {
        $UuserModel = new UuserModel();
        $array = [
            'id' => $this->userID,
        ];

        $data = $UuserModel->userfind($array);
        if($data){
            $this->assign('user',$data);
        }

        return $this->fetch();
    }
    public function Dateuser()
    {
        if(Request::instance()->isPost())
        {
            $rule = [
                'user'         =>  'require|chsAlpha|max:25',
                'text'         =>  'require',
                'user_qq'      =>  'require|number|min:9|max:11',
                'user_Mobile' =>   'require|number|min:11',
            ];
            $msg = [
                'user.chsAlpha'          => '用户名只能是汉字或者字母',
                'user.max'                => '用户名最多不能超过25个字符',
                'user.require'            => '用户名不能为空',
                'user_Mobile.number'     => '手机号只能是数字',
                'user_Mobile.min'        => '手机号至少11位',
                'user_Mobile.require'    => '手机号不能为空',
                'text.require'            => '内容不能为空',
                'user_qq.require'         => 'QQ号不能为空',
                'user_qq.max'             => 'QQ号最多不能超过11个字符',
                'user_qq.min'             => 'QQ号至少9位',
                'user_qq.number'          => 'QQ号只能是数字',
            ];
            $data = [
                'user'         => $_POST['user'],
                'user_qq'      => $_POST['user_qq'],
                'user_Mobile' => $_POST['mobile'],
                'text'         => $_POST['text'],
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if($result){
                $_POST['user_Mobile'] = $_POST['mobile'];
                $_POST['up_date'] = time();
                $UuserModel = new UuserModel();
                $result = $UuserModel->Dateuser($this->userID,$_POST);
                if($result){
                    return json_encode(['name'=>'修改成功','id'=>1]);
                }
                return json_encode(['name'=>'修改失败','id'=>0]);
            }
            return json_encode(['name'=>'验证失败','id'=>3]);
        }
    }
    public function Datepass()
    {
        $result = Db::name('user_vip')->where('userpass',$_POST['password'])->find();
//        if()
//        {
//
//        }
    }
}