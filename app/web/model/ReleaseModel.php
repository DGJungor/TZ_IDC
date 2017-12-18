<?php
/**
 * Created by PhpStorm.
 * User: 胡志伟
 * Date: 2017/12/13
 * Time: 10:37
 */

namespace app\web\model;

use think\Model;
use think\Db;
class ReleaseModel extends  Model
{
    /*
     * 发布评论
     *
     * */
    public function setcomment($data)
    {
        $result = Db::name('user_comment')->insertGetId($data);
        return $result;
    }

    /*
     * 评论的总条数
     *
     * */
    public function numbercomment()
    {
        $number = Db::name('user_comment')->count('id');
        return $number;
    }

    /*
     * 回复信息
     *
     * */
    public function setreply($data)
    {
        $result = Db::name('user_reply')->insertGetId($data);
        return $result;
    }

    /*
     * 回复的总条数
     *
     * */
    public function numberreply()
    {
        $number = Db::name('user_reply')->count('id');
        return $number;
    }

}