<?php
/**
 * Created by PhpStorm.
 * User: 胡志伟
 * Date: 2017/12/8
 * Time: 10:19
 */

namespace app\web\model;

use think\Model;
use think\Db;

class UuserModel  extends Model
{
    /*
     * 查询单条用户信息
     * array  |  用户信息查询条件
     * */
    public function userfind (array $array)
    {
        $data = Db::name('user_vip')->where($array )->where('status',1)->find();
        if($data){
            return $data;
        }
        return false;
    }

    /*
     * 修改用户信息
     * id   | 用户ID
     * data  |  用户修改信息
     * */
    public function Dateuser($id,$data = null)
    {
        if($data)
        {
            $result = Db::name('user_vip')->where('id',$id)->update($data);
            if($result)
            {
                return $result;
            }
            return false;
        }
    }


    /*
     * 修改用户密码
     * id  |  用户ID
     * value  |   用户原密码
     * */
    public function Datepass($id,$value)
    {
        $result = Db::name('user_vip')->where('id',$id)->update(['user_pass'=>$value]);
    }

    /*
     * 查询用户密码
     *
     * */
    public function getpass($id)
    {
        $passwordInDb = Db::name('user_vip')
            ->field('user_pass')
            ->where('id',$id)
            ->find();
        return $passwordInDb;
    }
}