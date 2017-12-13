<?php
/**
 * Created by PhpStorm.
 * User: 胡志伟
 * Date: 2017/12/13
 * Time: 9:43
 */

namespace app\web\model;

use think\Model;
use think\Db;
class LoginModel extends Model
{
    /*
     * 判断这个用户是否存在
     * */
    public function getlog($data)
    {
        $data = Db::name('user_vip')->where('user_email',$data['email'])->find();
        if(count($data))
        {
            return $data;
        }
        return false;
    }

    /*
     *注册用户信息
     *
     * */
    public function setreg($data)
    {
        $array = [
            'user_login' => $data['user'],
            'user_pass' => cmf_password($data['password']),
            'user_email' => $data['email'],
            'mobile' => $data['mobile'],
            'create_time' => time(),
        ];
        $result = Db::name('user_vip')->insertGetId($array);
        if($result)
        {
            return $result;
        }
        return false;
    }

    /*
     * 判断这个用户是否已注册
     *
     * */
    public function getreg($data)
    {
        $result = Db::name('user_vip')->where(['user_email'=>$data['email'],'mobile'=>$data['mobile']])->select();
        if(!count($result))
        {
            return true;
        }
        return false;
    }

    /*
     * 修改用户最后登录时间
     *
     * */
    public function logtime()
    {
        Db::table('idckx_user_vip')->where('id', $data['id'])->update(['last_login_time' => time()]);
    }
}