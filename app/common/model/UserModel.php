<?php

namespace app\common\model;

use think\Db;
use think\Model;

class UserModel extends Model
{

    public function demo()
    {
        return 'demo';
    }


//    /**
//     * 检测帐号中的某字段是否唯一
//     */
//    public function checkAccountOnly($account, $type)
//    {
//
//        $info = $this->where($type, $account)->count();
//
//        return $info;
//
//    }
}