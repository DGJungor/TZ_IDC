<?php
/**
 * Created by PhpStorm.
 * User: 胡志伟
 * Date: 2017/12/6
 * Time: 14:06
 */

namespace app\web\controller;

//use cmf\controller\HomeBaseController;
//use app\web\model\IndexModel;
use FontLib\Table\Type\glyf;
use think\Cookie;
use think\Session;
use think\Validate;
use think\Db;
use think\Request;

class LoginController extends Validate
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
//        $_POST = [
//            'email'=>'515149416@qq.com',
//            'password'=>'aS123456',
//            'Autologon'=>"1",
//        ];
            $data = Db::name('user_vip')->where('user_email',$_POST['email'])->find();
            if(count($data))
            {
                if(cmf_compare_password($_POST['password'], $data['user_pass']))
                {
                    Db::table('idckx_user_vip')->where('id', $data['id'])->update(['last_login_time' => time()]);
                    if($_POST['Autologon']=="1")
                    {
                        Session::set('user_data',$data);   //设置用户信息
                        Session::set('email',$data['user_email']);  //设置用户邮箱
                        Session::set('password',$_POST['password']); //设置用户密码
                        Session::set('user_id',$data['id']); //设置用户id
                    }else{
                        cookie('user_data',$data); //设置用户信息
                        cookie('user_id',$data['id']); //设置用户id
                    }
                    return json(['name'=>'登录成功','data'=>$data,'id'=>1,"msg"=>cookie("user_data")]);
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
            $result = Db::name('user_vip')->where(['user_email'=>$_POST['email'],'mobile'=>$_POST['mobile']])->select();
            if(!count($result))
            {
                $rule = [
                    'user' => 'require|chsAlpha|max:25',
                    'mobile' => 'require|number|min:11',
                    'email' => 'require|email',
                    'password' => 'require|max:25',
                    'repassword' => 'require|confirm:password',
                ];
                $msg = [
                    'user.chsAlpha' => '用户名只能是汉字或者字母',
                    'user.max' => '用户名最多不能超过25个字符',
                    'user.require' => '用户名不能为空',
                    'mobile.number' => '手机号只能是数字',
                    'mobile.min' => '手机号至少11位',
                    'mobile.require' => '手机号不能为空',
                    'email' => '邮箱格式错误',
                    'email.require' => '邮箱不能为空',
                    'password.require' => '密码不能为空',
                    'password.max' => '密码最多不能超过25个字符',
                    'repassword.require' => '确认密码不能为空',
                    'repassword.confirm' => '两次密码不相同',
                ];
                $data = [
                    'user'        => $_POST['user'],
                    'mobile'      => $_POST['mobile'],
                    'email'       => $_POST['email'],
                    'password'    => $_POST['password'],
                    'repassword'  => $_POST['repassword'],
                ];
                $validate = new Validate($rule, $msg);
                $result = $validate->check($data);
                if ($result) {
                    $array = [
                        'user_login' => $data['user'],
                        'user_pass' => cmf_password($data['password']),
                        'user_email' => $data['email'],
                        'mobile' => $data['mobile'],
                        'create_time' => time(),
                    ];
                   $result = Db::name('user_vip')->insertGetId($array);
                   if ($result) {
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
    }

    /*
     * 判断是否是登录状态
     * */
    public function islogin()
    {
        include_once(dirname(dirname(dirname(__FILE__))).'\\header.php');
        if(session('user_id'))
        {
            return json(['id'=> session('user_id'),'img'=>session('user_data')['avatar']]);
        }
        if(cookie('user_id'))
        {
            return json(['id'=> cookie('user_id'),'img'=>cookie('user_data')['avatar']]);
        }
        return json(['name'=>'未登陆','id'=>0]);
    }
}
