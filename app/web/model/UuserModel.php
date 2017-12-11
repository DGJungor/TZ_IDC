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
    public function userfind (array $array)
    {
        $data = Db::name('user_vip')->where($array )->find();
        if($data){
            return $data;
        }
        return false;
    }
//    public function get (){
//
//    }
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
}