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
use cmf\controller\HomeBaseController;
use think\Db;
use think\Request;

class UserController extends HomeBaseController
{
    public $userID = 0;   //用户登录ID
    public $class = '';

    /**
     * UserController constructor.
     */
    public function __construct()
    {
        $this->userID = cmf_get_current_user_id();
        $this->class = new UuserModel();  //实例化用户类
    }

    /*
     * sesion : user   用户信息
     * 用户管理页面输出
     * @return mixed
     */
    public function index()
    {
        if(session('?user')){
            $this->assign('user',session('user'));
        }
        return $this->fetch();
    }

    /*
     * 用户信息修改
     * @return string
     */
    public function Dateuser()
    {
        if(Request::instance()->isPost())
        {
            $data = [
                'user'         => $_POST['user'],                 //用户名
                'user_qq'      => $_POST['user_qq'],           //用户QQ
                'user_Mobile' => $_POST['mobile'],           //用户手机号
                'email'        => $_POST['email'],           //用户邮箱
            ];
            $validate = \think\Loader::validate('User');
            if($validate->check($data))
            {
                $_POST['user_Mobile'] = $_POST['mobile'];
                $_POST['up_date'] = time();
                $result = $this->class->Dateuser($this->userID,$_POST);
                if($result){
                    return json_encode(['name'=>'修改成功','id'=>1]);
                }
                return json_encode(['name'=>'修改失败','id'=>0]);
            }
            return json_encode(['name'=>'验证失败','id'=>3]);
        }
    }


    /*
     * 用户密码(password)
     * 修改
     * @return \think\response\Json
     */
    public function Datepass()
    {
        $password = $_POST['password'];
        if(!empty($_POST['datepass']) and !empty($_POST['repassword']) and $_POST['datepass'] === $_POST['repassword'])
        {
            $passwordInDb = $this->class->getpass($this->userID);
            if(cmf_compare_password($password, $passwordInDb))    //判断提交密码是否跟MySQL里密码一致
            {
                $result = $this->class->Datepass($this->userID,$_POST['datepass']);
                if($result)
                {
                    return json(['name'=>'修改成功','id'=>1]);
                }
                return json(['name'=>'修改失败','id'=>0]);
            }
        }
    }
}