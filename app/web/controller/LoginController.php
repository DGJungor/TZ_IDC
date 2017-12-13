<?php
/**
 * Created by PhpStorm.
 * User: 胡志伟
 * Date: 2017/12/6
 * Time: 14:06
 */

namespace app\web\controller;

use cmf\controller\HomeBaseController;
use app\web\model\LoginModel;
use FontLib\Table\Type\glyf;
use think\Cookie;
use think\Session;
//use think\Validate;
use think\Db;
use think\Request;

class LoginController extends HomeBaseController
{

    /*
     * AJAX以POST提交值
     * NAME: emnil ---> 邮箱地址
     *       password ----> 密码
     *       Autologon ----> 自动登陆值
     * ---------登录
     * */
    public function login()
    {
        include_once(dirname(dirname(dirname(__FILE__))).'\\header.php');
        $loginModel = new loginModel();
        $data = $loginModel->getlog($_POST);
        if(count($data))
        {
            if(cmf_compare_password($_POST['password'], $data['user_pass']))
            {
                $loginModel->logtime();
                if($_POST['Autologon']=="1")
                {
                    Session::set('user',$data);   //设置用户信息
                    Session::set('email',$data['user_email']);  //设置用户邮箱

                    Session::set('password',$_POST['password']); //设置用户密码
                    Session::set('user_id',$data['id']); //设置用户id
                }else{
                    Session::delete('email');
                    Session::delete('password');
                    cookie('user',$data); //设置用户信息
                    cookie('user_id',$data['id']); //设置用户id
                }
                return json(['name'=>'登录成功','data'=>$data,'id'=>1]);
            }
            return json(['name'=>'密码错误','id'=>0]);
        }
        return json(['name'=>'用户不存在','id'=>2]);
    }

    /*
     * AJAX以POST提交值
     * NAME: user ---> 用户名
     *       mobile ---> 手机号
     *       emnil ---> 邮箱地址
     *       password ---> 密码
     *       repassword ---> 确认密码
     * 函数 Validate 验证 data（数据信息）
     * ---------注册
     * */
    public function  register()
    {
        include_once(dirname(dirname(dirname(__FILE__))).'\\header.php');
        $loginModel = new loginModel();
        $result = $loginModel->getreg($_POST);
        if($result)
        {
            $data = [
                'user'        => $_POST['user'],
                'mobile'      => $_POST['mobile'],
                'email'       => $_POST['email'],
                'password'    => $_POST['password'],
                'repassword'  => $_POST['repassword'],
            ];
            $validate = \think\Loader::validate('Log');
            if($validate->check($data))
            {
                $result = $loginModel->setreg($data);
                if ($result)
                {
                    return json(['name' => '注册成功', 'id' => 1]);
                }
                return json(['name' => '注册失败', 'id' => 0]);
            }
            return json(['name' => '验证失败', 'id' => 3]);
        }
        return json(['name' => '用户以存在:', 'id' => 2]);
    }

    /*
     * 点击退出
     * 删除Session 用户Id
     * */

    public function outlogin()
    {
        include_once(dirname(dirname(dirname(__FILE__))).'\\header.php');
        Session::delete('user_id');
        Session::delete('user');
        Cookie::delete('user_id');
        Cookie::delete('user');
    }

    /*
     * 判断是否是登录状态
     * */
    public function islogin()
    {
        include_once(dirname(dirname(dirname(__FILE__))).'\\header.php');
        if(session('?user_id'))
        {
            return json(['id'=> session('user_id'),'img'=>session('user')['avatar']]);
        }
        if(cookie('user_id'))
        {
            return json(['id'=> cookie('user_id'),'img'=>cookie('user')['avatar']]);
        }
        return json(['name'=>'未登陆','id'=>0]);
    }
}
