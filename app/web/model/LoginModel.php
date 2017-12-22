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
     * @param $data
     * @return array|bool|false|\PDOStatement|string|Model
     */
    public function getlog($data)
    {
        if(!count($data)){return false;}
        $data = Db::name('user_vip')->whereOr($data)->where('user_status',1)->find();
        if(count($data))
        {
            return $data;
        }
        return false;
    }

    /*
     *注册用户信息
     * @param $data
     * @return bool|int|string
     */
    public function setreg($data)
    {
        $array = [
            'user_login' => $data['user'],
            'user_pass' => cmf_password($data['password']),
            'user_email' => $data['email'],
            'mobile' => $data['mobile'],
            'create_time' => time(),
            'user_type'   => 1,
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
     * @param $data
     * @return bool
     */
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
     * 修改用户最后登录信息
     * @param $id
     * @throws \think\Exception
     */
    public function information($id)
    {
        $user_IP = get_client_ip();
        Db::table('idckx_user_vip')->where('id', $id)->update(['last_login_ip'=>$user_IP,'last_login_time' => time()]);
    }

    /**
     * token 储存值
     * @param $data
     * @return int|string
     */
    public function tokenData($data)
    {
        $tokenData =  Db::name('user_token')->insert($data);
        return $tokenData;
    }
}