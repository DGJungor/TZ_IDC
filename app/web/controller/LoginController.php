<?php
/**
 * Created by PhpStorm.
 * User: 胡志伟
 * Date: 2017/12/6
 * Time: 14:06
 */

namespace app\web\controller;
include_once(dirname(dirname(dirname(__FILE__))).'\\tools\\ajaxEcho.php');
include_once(dirname(dirname(dirname(__FILE__))).'\\tools\\cookie_session.php');
use cmf\controller\HomeBaseController;
//use app\web\model\IndexModel;
use FontLib\Table\Type\glyf;
use think\Cookie;
use think\Session;
use think\Validate;
use think\Db;
use think\Request;
use think\Controller;

class LoginController extends HomeBaseController
{
    /**
     * 初始化配置 
    */

    /*
     * AJAX以POST提交值
     * NAME: emnil ---> 邮箱地址
     *       password ----> 密码
     *       Autologon ----> 自动登陆值
     * ---------登录
     * */
    public function login()
    {
            $data = Db::name('user_vip')->where('user_email',$_POST['email'])->find();
            if(count($data))
            {
                if(cmf_compare_password($_POST['password'], $data['user_pass']))
                {
                    
                    Db::table('idckx_user_vip')->where('id', $data['id'])->update(['last_login_time' => time()]);
                    
                    $tokenData = [
                        "user_id"=>$data['id'],
                        "expire_time"=>time()+(60*60),
                        "create_time"=>time(),
                        "token"=>$this->request->token('__token__',$data['id']),
                        "device_type"=>"web"
                    ];
                   $tokenResult = Db::name('user_token')->insert($tokenData);

                    // if($_POST['Autologon']=="1")
                    // {
                    //     Session::set('user_data',$data);   //设置用户信息
                    //     Session::set('email',$data['user_email']);  //设置用户邮箱
                    //     Session::set('password',$_POST['password']); //设置用户密码
                    //     Session::set('user_id',$data['id']); //设置用户id
                    // }else{
                    //     cookie('user_data',$data); //设置用户信息
                    //     cookie('user_id',$data['id']); //设置用户id
                    // }
                    // return json(['name'=>'登录成功','data'=>["id"=>$data['id'],"img"=>$data["avatar"]],'id'=>1]);
                    if($tokenResult) {
                        return ajaxEcho(["id"=>$tokenData["user_id"],"token"=>$tokenData["token"],"img"=>$data["avatar"]],"登录成功",1);
                    }else {
                        return ajaxEcho([],"token写入错误");
                    }
                    
                }
                return ajaxEcho([],"密码错误");
                // return json(['name'=>'密码错误','id'=>0]);
            }
            return ajaxEcho([],"用户不存在");
            // return json(['name'=>'用户不存在','id'=>2]);
            
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
                    //    return json(['name' => '注册成功', 'id' => 1]);
                        return ajaxEcho([],"注册成功",1);
                    }
                    // return json(['name' => '注册失败', 'id' => 0]);
                    return ajaxEcho([],"注册失败");
                }
                return ajaxEcho([],"验证失败");
            }
            return ajaxEcho([],"用户以存在");
    }

    /*
     * 点击退出
     * 删除Session 用户Id
     * */

    public function outlogin()
    {
       $result = db("user_token")->where('user_id',byTokenGetUser(Request::instance()->header()["token"])["userId"])->delete();
        if($result) {
            $this->success("退出成功");
        }else {
            $this->error("退出失败");
        }
        
    }

    /*
     * 判断是否是登录状态
     * */
    public function islogin()
    {
        if(byTokenGetUser(Request::instance()->header()["token"])["userId"]==-1) {
            return ajaxEcho([],byTokenGetUser(Request::instance()->header()["token"])["msg"],5000);
        }
        $result = Db::name('user_vip')->where('id',byTokenGetUser(Request::instance()->header()["token"])["userId"])->find();
        return ajaxEcho(["id"=>byTokenGetUser(Request::instance()->header()["token"])["userId"],"img"=>$result["avatar"]]);
    }
}
