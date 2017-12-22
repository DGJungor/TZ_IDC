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
use app\web\model\LoginModel;
use think\Cookie;
use think\Request;
use think\Session;

class LoginController extends HomeBaseController
{

    /*
     * AJAX以POST提交值
     * NAME: user_email ---> 邮箱地址
     *       user_login  --->用户名
     *       mobile ----> 用户手机
     *       password ----> 密码
     *       Autologon ----> 自动登陆值
     * ---------登录
     * @return mixed
     */
    public function login()
    {
        include_once(dirname(dirname(dirname(__FILE__))).'\\header.php');
        $loginModel = new loginModel();
        $keywordComplex = [];
        if (!empty($_POST['keyword'])) {
            $keyword = $_POST['keyword'];
            $keywordComplex['mobile|user_login|user_email']    = ['like', "%$keyword%"];
        }
        $data = $loginModel->getlog($keywordComplex);
        if(count($data))
        {
            if(cmf_compare_password($_POST['password'], $data['user_pass']))
            {
                $loginModel->information($data['id']); //  用户登录记录
                if($_POST['Autologon']=="1")
                {
                    Session::set('user',$data);   //设置用户信息
                    Session::set('email',$data['user_email']);  //设置用户邮箱
                    Session::set('nickname',$data['user_nickname']);  //设置用户名称
                    Session::set('password',$_POST['password']); //设置用户密码
                    Session::set('user_id',$data['id']); //设置用户id
                }else{
                    Session::delete('email');
                    Session::delete('password');
                    cookie('user',$data); //设置用户信息
                    cookie('user_id',$data['id']); //设置用户id
                }
                $tokenData = [
                    "user_id"=>$data['id'],
                    "expire_time"=>time()+(60*60),
                    "create_time"=>time(),
                    "token"=>$this->request->token('__token__',$data['id']),
                    "device_type"=>"web"
                ];
                $tokenResult = Db::name('user_token')->insert($tokenData);
                if($tokenResult) {
                    return ajaxEcho(["id"=>$tokenData["user_id"],"token"=>$tokenData["token"],"img"=>$data["avatar"]],"登录成功",1);
                }else {
                    return ajaxEcho([],"token写入错误");
                }
            }
            return ajaxEcho([],"密码错误");
        }
        return ajaxEcho([],"用户不存在");
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
     * @return mixed
     */
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
                    return ajaxEcho([],"注册成功",1);
                }
                return ajaxEcho([],"注册失败");
            }
            return ajaxEcho([],"验证失败");
        }
        return ajaxEcho([],"用户以存在");
    }

    /*
     * 点击退出
     * @throws \think\Exception
     */
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
     * @return mixed
     */
    public function islogin()
    {
        if(byTokenGetUser(Request::instance()->header()["token"])["userId"]==-1) {
            return ajaxEcho([],byTokenGetUser(Request::instance()->header()["token"])["msg"],5000);
        }
        $result = Db::name('user_vip')->where('id',byTokenGetUser(Request::instance()->header()["token"])["userId"])->find();
        return ajaxEcho(["id"=>byTokenGetUser(Request::instance()->header()["token"])["userId"],"img"=>$result["avatar"]]);
    }
}
